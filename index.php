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

// Include plugins.
foreach ($app['enabled.plugins'] as $pluginName) {
	$Path = 'plugins' . '/' . $pluginName;
	$File = $Path . '/' . $pluginName . '.php';
	if (!file_exists($File)) {
		$File = $Path . '/default.php';
	}
	$IncludeResult = include_once($File);
	$IncludeResult($app);
}

// Set error handler.
$app->error(function(\Exception $e, $code) use ($app) {
	if (!$app['debug']) return;
	$handler = new php_error\ErrorHandler();
	$handler->turnOn();
	$handler->reportException($e);
});

// Services.
// $app->register(new Silex\Provider\UrlGeneratorServiceProvider());
// $app->register(new Silex\Provider\FormServiceProvider());
// $app->register(new Silex\Provider\ValidatorServiceProvider());

//Twig.
// $app->register(new Silex\Provider\TwigServiceProvider(), array(
// 	'twig.path' => 'views'
// ));


$app->register(new Silex\Provider\SessionServiceProvider());

// View.
$app['view'] = $app->share(function() use ($app) {
	return new Art\View($app);
});

// Routes.
$app->mount('/', include 'controllers/root.php');
$app->mount('/news', include 'controllers/news.php');
$app->mount('/test', include 'controllers/test.php');

$app->run();