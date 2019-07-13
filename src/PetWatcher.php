<?php

require __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$settings = require __DIR__ . '/app/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
$dependencies = require __DIR__ . '/app/dependencies.php';
$dependencies($app);

// Register routes
$routes = require __DIR__ . '/app/routes.php';
$routes($app);
