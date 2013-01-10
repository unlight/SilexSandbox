<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['path.root'] = __DIR__;
$app['path.cache'] = $app['path.root'] . '/cache';

if (file_exists('conf/bootstrap.before.php')) {
	require_once 'conf/bootstrap.before.php';
}

// Register services.
$app->register(new ConfigurationServiceProvider('settings'));
$app->register(new MvcServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new \SessionHandlerServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new DatabaseServiceProvider());

# Set error handler.
// if ($app['debug']) {
// 	$handler = new php_error\ErrorHandler();
// 	$handler->turnOn();
// 	$app->error(function(\Exception $e, $code) use ($app, $handler) {
// 		$handler->reportException($e);
// 	});
// }

// Run application.
$app->run();