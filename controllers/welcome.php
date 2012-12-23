<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$welcome = $app['controllers_factory'];

$welcome->get('/hello/{name}', function (Application $app, Request $request) {
	$name = $request->get('name');
	$view = $app['view'];
	$view->name = $name;
	$view->addCssFile('style.css');
	$view->addJsFile('functions.js');
	return $view->render();
});

return $welcome;