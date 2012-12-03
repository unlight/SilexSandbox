<?php
$autoloader = require_once __DIR__.'/vendor/autoload.php';

$autoloader->addClassMap(array('php_error\ErrorHandler' => __DIR__ . '/library/PHP-Error/src/php_error.php'));



$app = new Silex\Application();
$app['debug'] = true; 

$app->error(function (\Exception $e, $code) use ($app) {
	if (!$app['debug']) return;
	$handler = new \php_error\ErrorHandler();
	$handler->turnOn();	
	$handler->reportException($e);
});

$app->get('/hello/{name}', function($name) use($app) { 
    return 'Hello '.$app->escape($name); 
}); 

$app->run(); 