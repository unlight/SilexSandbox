<?php

return function($app) {

	$app['view.body.class'] = $app->share(function() use ($app) {
		$requestInfo = $app['request.info'];
		$names = array($requestInfo['controller'], $requestInfo['method']);
		return implode(' ', array_map('ucfirst', $names));
	});

};