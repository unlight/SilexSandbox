<?php

return function($app) {

	$app['view'] = $app->share(function() use ($app) {
		require_once __DIR__ . '/View.php';
		require_once __DIR__ . '/HeadModule.php';
		return new GdnView($app);
	});

};