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

    public function homeAction(Application $app) {
        return $app['twig']->render('home.twig', array());
    }

    public function googleAction(Application $app) {
        $provider = new GoogleProvider(array(
            'clientId'     => '843355983392-cdvc45m9dsq8qk6lnaa1u1nmslp6ae58.apps.googleusercontent.com',
            'clientSecret' => '47U1ohqOBr2aw9ISElhKohG0',
            'redirectUri'  => 'http://localhost:8080/google',
            'scopes'       => array('email', 'profile', 'https://www.googleapis.com/auth/gmail.readonly')
        ));

        if (!$app['request']->get('code')) {
            $authUrl = $provider->getAuthorizationUrl();
            $app['session']->set('oauth2state', $provider->state);
            $app['session']->remove('token');

            return $app->redirect($authUrl);

        } elseif (!$app['request']->get('state') || $app['request']->get('state') !== $app['session']->get('oauth2state')) {
            $app['session']->remove('oauth2state');
            $app['session']->remove('token');

            throw new AccessDeniedException('Google');
        }

        if (!$app['session']->get('token')) {
            $token = $provider->getAccessToken('authorization_code', array('code' => $app['request']->get('code')));
            $app['session']->set('token', $token);
        }

        $userDetails = $provider->getUserDetails($app['session']->get('token'));
        $mailDetails = $provider->getUserEmails($app['session']->get('token'));

        return $app['twig']->render('google.twig', array('user' => $userDetails, 'mails' => $mailDetails));
    }

}