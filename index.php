<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['path.root'] = __DIR__;

if (file_exists('conf/bootstrap.before.php')) {
	require_once 'conf/bootstrap.before.php';
}

// Set error handler.
// $app->error(function(\Exception $e, $code) use ($app) {
// 	if (!$app['debug']) return;
// 	$handler = new php_error\ErrorHandler();
// 	$handler->turnOn();
// 	$handler->reportException($e);
// });

// Register services.
$app->register(new ConfigurationServiceProvider('conf'));
$app->register(new MvcServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new FormServiceProvider());

// Run application.
$app->run();