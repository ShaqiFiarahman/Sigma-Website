<?php

// Atur variabel lingkungan untuk jalur cache yang dapat ditulis di lingkungan serverless Vercel
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

// Paksa nama script ke /index.php agar Laravel tidak memotong prefix '/api' pada Vercel
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Teruskan permintaan ke public/index.php milik Laravel
require __DIR__ . '/../public/index.php';
