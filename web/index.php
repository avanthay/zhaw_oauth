<?php
/**
 * File index.php
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */


use Dave\Controller\AdminController;
use Dave\Controller\DefaultController;
use Dave\Controller\SecurityController;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';


$app = new Application();
$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider(), array('twig.path' => __DIR__ . '/../src/View'));
$app->register(new UrlGeneratorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin'   => array(
            'pattern' => '^/admin',
            'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
            'logout' => array('logout_path' => '/admin/logout'),
            'users'   => array(
                // raw password is foo
                'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
                'user' => array('ROLE_USER', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==')
            )
        ),
        'default' => array(
            'anonymous' => true
        )
    )
));


$app['controller.default'] = $app->share(function () use ($app) {
    return new DefaultController($app);
});
$app->get('/', 'controller.default:homeAction')->bind('home');
$app->get('/google', 'controller.default:googleAction')->bind('google');

$app['controller.security'] = $app->share(function () use ($app) {
    return new SecurityController($app);
});
$app->get('/login', 'controller.security:loginAction')->bind('login');

$app['controller.admin'] = $app->share(function () use ($app) {
    return new AdminController($app);
});
$app->get('/admin', 'controller.admin:adminAction')->bind('admin');

$app->run();