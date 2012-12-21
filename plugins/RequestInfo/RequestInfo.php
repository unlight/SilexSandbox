<?php

return function($app) {

	$app['request_info'] = $app->share(function() use ($app) {
		$routeName = $app['request']->attributes->get('_route');
		$routeName = str_replace(array('GET', 'POST'), '', $routeName);
		$routeParams = array_keys($app['request']->attributes->get('_route_params'));
		$parts = explode('_', $routeName);
		$result = new StdClass();
		$result->controller = isset($parts[1]) ? $parts[1] : 'home';
		$result->method = isset($parts[2]) ? $parts[2] : 'index';

		return $result;
	});
};