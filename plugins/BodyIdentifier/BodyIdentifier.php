<?php

return function($app) {

	$app['body_identifier'] = $app->share(function() use ($app) {
		$requestInfo = $app['request_info'];
		return $requestInfo->controller . '_' . $requestInfo->method;
	});
};