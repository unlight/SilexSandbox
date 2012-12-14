<?php

use Silex\Application;
use Silex\ControllerProviderInterface;

class HomeController implements ControllerProviderInterface
{
	public function connect(Application $app) {
		// creates a new controller based on the default route
		$controllers = $app['controllers_factory'];

		$controllers->get('/', function(Application $app) {
			$request = $app['request'];
			return 'pathInfo:' . $request->getpathInfo();
		});

		return $controllers;
	}
}