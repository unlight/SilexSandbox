<?php

return function($app) {

	$app['request.info'] = $app->share(function() use ($app) {
		$routeName = $app['request']->attributes->get('_route');
		$routeName = str_replace(array('GET', 'POST'), '', $routeName);
		$routeParams = array_keys($app['request']->attributes->get('_route_params'));
		$parts = explode('_', $routeName);
		foreach ($routeParams as $routeParam) {
			$key = array_search($routeParam, $parts);
			if ($key !== false) unset($parts[$key]);
		}
		$parts = array_values($parts);
		
		$result['controller'] = isset($parts[0]) ? $parts[0] : 'home';
		$result['method'] = isset($parts[1]) ? $parts[1] : 'index';

		return $result;
	});
};