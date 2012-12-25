<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['path.root'] = __DIR__;

if (file_exists('conf/bootstrap.before.php')) {
	require_once 'conf/bootstrap.before.php';
}

function MergeArrays($Arr1, $Arr2) {
  foreach($Arr2 as $key => $Value) {
    if(array_key_exists($key, $Arr1) && is_array($Value))
      $Arr1[$key] = MergeArrays($Arr1[$key], $Arr2[$key]);
    else $Arr1[$key] = $Value;
  }
  return $Arr1;
}

// Loading configuration.
$configuration = require 'conf/config-defaults.php';
if (file_exists('conf/config.php')) {
	$configuration = MergeArrays($configuration, require 'conf/config.php');
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
foreach ($app['enabledplugins'] as $pluginName => $enabled) {
	if ($enabled !== true) continue;
	$pluginFile = "plugins/{$pluginName}/{$pluginName}.php";
	$plugin = include $pluginFile;
	if (is_callable($plugin)) $plugin($app);
}

// Do not include unnecessary controllers.
$request = isset($_GET['p']) ? $_GET['p'] : '';
$parts = array_filter(explode('/', $request));
if (count($parts) > 0) {
	$controllerPath = $parts[0];
	$controllerFile = "controllers/{$controllerPath}.php";
	if (file_exists($controllerFile)) {
		$module = require $controllerFile;
		if ($module == 1) {
			$class = ucfirst($controllerPath) . 'Controller';
			$module = new $class;
		}
		$app->mount($controllerPath, $module);
	}
	// require_once __DIR__ . '/controllers/HomeController.php';
	// $app->mount('/home', new HomeController());
} else {
	$app->mount('/', include 'controllers/root.php');	
}

$app->run();
