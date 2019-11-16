<?php

define('BASE_PATH', __DIR__);

require BASE_PATH.'/vendor/autoload.php';

use App\Application;

$app = new Application();

$app->bootstrap();

$app->run();
