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
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

$loader = require_once __DIR__ . '/../vendor/autoload.php';


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
            'form'    => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
            'logout'  => array('logout_path' => '/admin/logout'),
            'users'   => array(
                // raw password is foo
                'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
                'user'  => array('ROLE_USER', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==')
            )
        ),
        'default' => array(
            'anonymous' => true
        )
    )
));

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'host'     => 'localhost',
        'dbname'   => 'oauth_app',
        'user'     => 'php',
        'password' => 'password'
    )
));
$app->register(new DoctrineOrmServiceProvider(), array(
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type'                         => 'annotation',
                'namespace'                    => 'Dave\Entity',
                'path'                         => __DIR__ . '/../src/Entity',
                'use_simple_annotation_reader' => false
            )
        )
    )
));
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
AnnotationReader::addGlobalIgnoredName('type');

//TODO update schema conditionally
$schemaTool = new SchemaTool($app['orm.em']);
$schemaTool->updateSchema($app['orm.em']->getMetadataFactory()->getAllMetadata());


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