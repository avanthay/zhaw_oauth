<?php


namespace Dave\Provider;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


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

        $controllers->get('/login', 'Dave\Controller\SecurityController::loginAction')->bind('login');

        $controllers->get('/admin', 'Dave\Controller\AdminController::adminAction')->bind('admin');

        $controllers->get('/oauth', function() use ($app) { return new RedirectResponse($app['url_generator']->generate('home')); });
        $controllers->get('/oauth/authorize', 'Dave\Controller\AuthorizationController::authorizeAction')->bind('authorize');
        $controllers->get('/oauth/token', 'Dave\Controller\AuthorizationController::accessTokenAction')->bind('accessToken');

        return $controllers;
    }
}