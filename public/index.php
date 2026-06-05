<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

echo "Checkpoint 1 - Startup\n";

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

echo "Checkpoint 2 - Maintenance Check Done\n";

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

echo "Checkpoint 3 - Composer Autoload Loaded\n";

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

echo "Checkpoint 4 - Laravel App Bootstrapped\n";

$app->handleRequest(Request::capture());

echo "Checkpoint 5 - Request Handled\n";
