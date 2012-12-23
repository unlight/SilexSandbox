<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['path.root'] = __DIR__;

if (file_exists('conf/bootstrap.before.php')) {
	require_once 'conf/bootstrap.before.php';
}

// Loading configuration.
$configuration = require 'conf/config-defaults.php';
if (file_exists('conf/config.php')) {
	$configuration = array_merge($configuration, require 'conf/config.php');
}
foreach ($configuration as $key => $value) {
	$app[$key] = $value;
}

// Set error handler.
$app->error(function(\Exception $e, $code) use ($app) {
	if (!$app['debug']) return;
	$handler = new php_error\ErrorHandler();
	$handler->turnOn();
	$handler->reportException($e);
});

// Include plugins.
foreach ($app['enabled.plugins'] as $pluginName) {
	$pluginFile = "plugins/{$pluginName}/{$pluginName}.php";
	$plugin = include $pluginFile;
	$plugin($app);
}

// Do not include unnecessary controllers.
$request = isset($_GET['p']) ? $_GET['p'] : '';
$parts = array_filter(explode('/', $request));
if (count($parts) > 0) {
	$controllerPath = $parts[0];
	$controllerFile = "controllers/{$controllerPath}.php";
	if (file_exists($controllerFile)) {
		$app->mount($controllerPath, require $controllerFile);	
	}
	// require_once __DIR__ . '/controllers/HomeController.php';
	// $app->mount('/home', new HomeController());
} else {
	$app->mount('/', include 'controllers/root.php');	
}

$app->run();
