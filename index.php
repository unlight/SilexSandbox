<?php
require_once __DIR__.'/vendor/autoload.php';
spl_autoload_register(function($className) {
	$path = str_replace('\\', '/', $className);
	$parts = explode('\\', $className);
	foreach (array('library', 'controllers') as $directory) {
		$file = $directory . '/' . $path . '.php';
		if (file_exists($file)) {
			require_once $file;
			return true;
		}
	}
});

// Loading configuration.
require_once __DIR__ . '/conf/config-defaults.php';
if (file_exists(__DIR__ . '/conf/config.php')) {
	require_once __DIR__ . '/conf/config.php';
}
foreach ($configuration as $key => $value) {
	$app[$key] = $value;
}

$app = new Silex\Application();

$app['path.root'] = __DIR__;

// Set error handler.
$app->error(function(\Exception $e, $code) use ($app) {
	if (!$app['debug']) return;
	static $handler;
	if ($handler === null) {
		$handler = new \php_error\ErrorHandler();
		$handler->turnOn();
	}
	$handler->reportException($e);
});

// Services.
// $app->register(new Silex\Provider\UrlGeneratorServiceProvider());
// $app->register(new Silex\Provider\FormServiceProvider());
// $app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

// View.
$app['view'] = $app->share(function() use ($app) {
  return new Art\View($app);
});


$app->get('/hello/index/{name}', function($name) use($app) {
	$request = $app['request'];
	$view = $app['view'];
	$name = $request->get('name');
	$view->name = $name;
	return $view->render();
}); 

$app->run(); 