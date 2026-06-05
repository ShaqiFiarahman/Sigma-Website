<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "PHP Serverless is running! Attempting to load Laravel...\n";

// Forward request to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
