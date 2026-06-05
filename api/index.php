<?php

// Set environment variables for writable cache paths in Vercel serverless environment
$cachePath = '/tmp';
$_ENV['APP_SERVICES_CACHE'] = $cachePath . '/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $cachePath . '/packages.php';
$_ENV['APP_CONFIG_CACHE'] = $cachePath . '/config.php';
$_ENV['APP_ROUTES_CACHE'] = $cachePath . '/routes.php';
$_ENV['APP_EVENTS_CACHE'] = $cachePath . '/events.php';

$_SERVER['APP_SERVICES_CACHE'] = $cachePath . '/services.php';
$_SERVER['APP_PACKAGES_CACHE'] = $cachePath . '/packages.php';
$_SERVER['APP_CONFIG_CACHE'] = $cachePath . '/config.php';
$_SERVER['APP_ROUTES_CACHE'] = $cachePath . '/routes.php';
$_SERVER['APP_EVENTS_CACHE'] = $cachePath . '/events.php';

putenv('APP_SERVICES_CACHE=' . $cachePath . '/services.php');
putenv('APP_PACKAGES_CACHE=' . $cachePath . '/packages.php');
putenv('APP_CONFIG_CACHE=' . $cachePath . '/config.php');
putenv('APP_ROUTES_CACHE=' . $cachePath . '/routes.php');
putenv('APP_EVENTS_CACHE=' . $cachePath . '/events.php');

// Forward request to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
