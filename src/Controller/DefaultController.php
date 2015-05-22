<?php
/**
 * Class DefaultController
 * @package Dave\Controller
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */


namespace Dave\Controller;


use Dave\Libraries\OAuth2Client\GoogleProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class DefaultController {

    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function homeAction() {
        return $this->app['twig']->render('home.twig', array());
    }

    public function googleAction() {
        $provider = new GoogleProvider(array(
            'clientId'     => '843355983392-cdvc45m9dsq8qk6lnaa1u1nmslp6ae58.apps.googleusercontent.com',
            'clientSecret' => '47U1ohqOBr2aw9ISElhKohG0',
            'redirectUri'  => 'http://localhost:8080/google',
            'scopes'       => array('email', 'profile', 'https://www.googleapis.com/auth/gmail.readonly')
        ));

        if (!$this->app['request']->get('code')) {
            $authUrl = $provider->getAuthorizationUrl();
            $this->app['session']->set('oauth2state', $provider->state);
            $this->app['session']->remove('token');

            return $this->app->redirect($authUrl);

        } elseif (!$this->app['request']->get('state') || $this->app['request']->get('state') !== $this->app['session']->get('oauth2state')) {
            $this->app['session']->remove('oauth2state');
            $this->app['session']->remove('token');

            throw new AccessDeniedException('Google');
        }

        if (!$this->app['session']->get('token')) {
            $token = $provider->getAccessToken('authorization_code', array('code' => $this->app['request']->get('code')));
            $this->app['session']->set('token', $token);
        }

        $userDetails = $provider->getUserDetails($this->app['session']->get('token'));
        $mailDetails = $provider->getUserEmails($this->app['session']->get('token'));

        return $this->app['twig']->render('google.twig', array('user' => $userDetails, 'mails' => $mailDetails));
    }

}