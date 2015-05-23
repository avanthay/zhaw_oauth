<?php


namespace Dave\Provider;

use Dave\Entity\AccessToken;
use Dave\Entity\Client;
use Dave\Entity\Scope;
use Dave\Entity\Session;
use Dave\Entity\User;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ControllerProvider
 * @package Dave\Provider
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class ControllerProvider implements ControllerProviderInterface {

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app) {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'Dave\Controller\DefaultController::homeAction')->bind('home');
        $controllers->get('/google', 'Dave\Controller\DefaultController::googleAction')->bind('google');
        $controllers->get('/own', 'Dave\Controller\DefaultController::ownServerAction')->bind('ownServer');

        $controllers->get('/login', 'Dave\Controller\SecurityController::loginAction')->bind('login');

        $controllers->get('/admin', 'Dave\Controller\AdminController::adminAction')->bind('admin');

        $controllers->get('/oauth', function() use ($app) { return new RedirectResponse($app['url_generator']->generate('home')); });
        $controllers->match('/oauth/authorize', 'Dave\Controller\AuthorizationController::authorizeAction')->bind('authorize');
        $controllers->match('/oauth/token', 'Dave\Controller\AuthorizationController::accessTokenAction')->bind('accessToken');

        $controllers->get('/api', 'Dave\Controller\ResourceApiController::getAction')->bind('api');

        $controllers->get('/create', function() use ($app) {

            $shouldExecute = false;

            if (!$shouldExecute) {
                return new RedirectResponse($app['url_generator']->generate('home'));
            }

            $user = new User('admin', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==', array('ROLE_ADMIN'));
            $user->setName('Admin User');
            $user->setEmail('admin@test.com');
            $user2 = new User('user', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==', array('ROLE_USER'));
            $user2->setName('User User');
            $user2->setEmail('user@test.com');
            $app['orm.em']->persist($user);
            $app['orm.em']->persist($user2);

            $redirectUri = 'http://localhost:8080/own';

            $client = new Client($app['oauth.server.authorization']);
            $client->setName('Dave\'s oauth 2.0 app');
            $client->setSecret('secret');
            $app['orm.em']->persist($client);
            $app['orm.em']->flush();

            $emailScope = new Scope($app['oauth.server.authorization'], 'email', 'Show the user mail address');
            $profileScope = new Scope($app['oauth.server.authorization'], 'profile', 'Show the user profile informations');
            $session = new Session($app['oauth.server.authorization'], 'client', $client->getId(), $client, $redirectUri);
            $session->addScope($profileScope);
            $session->addScope($emailScope);
            $app['orm.em']->persist($emailScope);
            $app['orm.em']->persist($profileScope);
            $app['orm.em']->persist($session);

            $accessToken = new AccessToken($app['oauth.server.authorization'], 'encryptedToken', time() + 86400, $session);
            $app['orm.em']->persist($accessToken);

            $app['orm.em']->flush();

            return new Response('Entities where created successfully!');
        });

        return $controllers;
    }
}