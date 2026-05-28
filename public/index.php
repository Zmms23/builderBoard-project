<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$temporaryDirectory = __DIR__ . '/../storage/framework/tmp';

if (! is_dir($temporaryDirectory)) {
    mkdir($temporaryDirectory, 0777, true);
}

putenv("TMP={$temporaryDirectory}");
putenv("TEMP={$temporaryDirectory}");
putenv("TMPDIR={$temporaryDirectory}");

$_ENV['TMP'] = $temporaryDirectory;
$_ENV['TEMP'] = $temporaryDirectory;
$_ENV['TMPDIR'] = $temporaryDirectory;
$_SERVER['TMP'] = $temporaryDirectory;
$_SERVER['TEMP'] = $temporaryDirectory;
$_SERVER['TMPDIR'] = $temporaryDirectory;

ini_set('sys_temp_dir', $temporaryDirectory);

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
