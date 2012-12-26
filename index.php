<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['path.root'] = __DIR__;

if (file_exists('conf/bootstrap.before.php')) {
	require_once 'conf/bootstrap.before.php';
}

$app->register(new ConfigurationServiceProvider('conf'));
$app->register(new MvcServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

// Set error handler.
$app->error(function(\Exception $e, $code) use ($app) {
	if (!$app['debug']) return;
	$handler = new php_error\ErrorHandler();
	$handler->turnOn();
	$handler->reportException($e);
});

// Include plugins.
foreach ($app['enabledplugins'] as $pluginName => $enabled) {
	if ($enabled !== true) continue;
	$pluginFile = "plugins/{$pluginName}/{$pluginName}.php";
	$plugin = include $pluginFile;
	if (is_callable($plugin)) $plugin($app);
}

$app->run();