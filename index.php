<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['path.root'] = __DIR__;

if (file_exists('conf/bootstrap.before.php')) {
	require_once 'conf/bootstrap.before.php';
}

// Loading configuration.
require_once 'conf/config-defaults.php';
if (file_exists('conf/config.php')) {
	require_once 'conf/config.php';
}
foreach ($configuration as $key => $value) {
	$app[$key] = $value;
}

// Set error handler.
$app->error(function(\Exception $e, $code) use ($app) {
	if (!$app['debug']) return;
	static $handler;
	if ($handler === null) {
		$handler = new php_error\ErrorHandler();
		$handler->turnOn();
	}
	$handler->reportException($e);
});

// Services.
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

// Twig.
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => 'views'
));


// View.
$app['view'] = $app->share(function() use ($app) {
  return new Art\View($app);
});

// Routes.
$app->mount('/', include 'controllers/root.php');
$app->mount('/news', include 'controllers/news.php');

// Test.
$app->get('/hello/index/{name}', function($name) use($app) {
	$request = $app['request'];
	$view = $app['view'];
	$name = $request->get('name');
	$view->name = $name;
	return $view->render();
});

// $app->get('/hello/{name}', function ($name) use ($app) {
// 	return $app['twig']->render('hello.html', array(
// 		'name' => $name,
// 	));
// });

$app->run(); 