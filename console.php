#!/usr/local/php5/bin/php
<?php
/**
 * File console.php
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */

use Dave\App;

require __DIR__.'/vendor/autoload.php';

$app = new App();
$app['console']->run();