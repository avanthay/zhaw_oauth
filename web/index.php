<?php

use Dave\Controller\DefaultController;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';


$app = new Application();
$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider(), array('twig.path' => __DIR__ . '/../src/View'));
$app->register(new SessionServiceProvider());


$app['default.controller'] = $app->share(function() use ($app) {
    return new DefaultController($app);
});

$app->get('/', 'default.controller:homeAction');
$app->get('/google', 'default.controller:googleAction');

$app->run();