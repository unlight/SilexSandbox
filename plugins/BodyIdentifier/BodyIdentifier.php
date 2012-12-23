<?php

return function($app) {

	$app['body.identifier'] = $app->share(function() use ($app) {
		$requestInfo = $app['request.info'];
		return $requestInfo['controller'] . '_' . $requestInfo['method'];
	});
};