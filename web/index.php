<?php
/**
 * File index.php
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */

use Dave\Libraries\OAuth2Server\AccessTokenStorage;
use Dave\Libraries\OAuth2Server\AuthCodeStorage;
use Dave\Libraries\OAuth2Server\ClientStorage;
use Dave\Libraries\OAuth2Server\ScopeStorage;
use Dave\Libraries\OAuth2Server\SessionStorage;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\ResourceServer;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
            'pattern' => '(^/admin|^/oauth/authorize)',
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


$app['oauth.server.authorization'] = $app->share(function () use ($app) {
    $server = new AuthorizationServer();
    $server->setSessionStorage(new SessionStorage($app));
    $server->setAccessTokenStorage(new AccessTokenStorage($app));
    $server->setClientStorage(new ClientStorage($app));
    $server->setScopeStorage(new ScopeStorage($app));
    $server->setAuthCodeStorage(new AuthCodeStorage($app));
    $server->addGrantType(new AuthCodeGrant());
    return $server;
});
$app['oauth.server.resource'] = $app->share(function () use ($app) {
    return new ResourceServer(new SessionStorage($app), new AccessTokenStorage($app), new ClientStorage($app), new ScopeStorage($app));
});


$app->get('/', 'Dave\Controller\DefaultController::homeAction')->bind('home');
$app->get('/google', 'Dave\Controller\DefaultController::googleAction')->bind('google');

$app->get('/login', 'Dave\Controller\SecurityController::loginAction')->bind('login');

$app->get('/admin', 'Dave\Controller\AdminController::adminAction')->bind('admin');

$app->get('/oauth', function() use ($app) { return new RedirectResponse($app['url_generator']->generate('home')); });
$app->get('/oauth/authorize', 'Dave\Controller\AuthorizationController::authorizeAction')->bind('authorize');
$app->get('/oauth/token', 'Dave\Controller\AuthorizationController::accessTokenAction')->bind('accessToken');

$app->run();