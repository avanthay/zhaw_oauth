<?php
/**
 * File index.php
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */

use Dave\App;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App();
$app['debug'] = true;

$app->run();