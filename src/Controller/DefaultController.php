<?php
/**
 * Class DefaultController
 * @package Dave\Controller
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */


namespace Dave\Controller;


use Dave\Libraries\Provider\GoogleProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class DefaultController {

    private $app;
    private $accessToken;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function homeAction() {
        return $this->app['twig']->render('home.twig', array());
    }

    public function customGoogleAction() {
        $code = $this->app['request']->query->get('code');
        if ($code) {
            $ch = curl_init('https://www.googleapis.com/oauth2/v3/token');
            curl_setopt_array($ch, array(
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS     => http_build_query(array(
                    'code'          => $code,
                    'client_id'     => '843355983392-cdvc45m9dsq8qk6lnaa1u1nmslp6ae58.apps.googleusercontent.com',
                    'client_secret' => '47U1ohqOBr2aw9ISElhKohG0',
                    'redirect_uri'  => 'http://localhost:8080/google',
                    'grant_type'    => 'authorization_code'
                ))
            ));
            $exec = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($exec, true);

            if ($response && $response['access_token']) {
                $this->accessToken = $response['access_token'];
            }

            return $exec;
        }
        return $this->app['twig']->render('google.twig', array('params' => $this->app['request']->query));
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