<?php

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController implements ControllerProviderInterface
{
	public function connect(Application $app) {
		// Creates a new controller based on the default route.
		$controllers = $app['controllers_factory'];

		$controllers->get('/welcome', $this->welcome());

		$controllers->get('/', function(Application $app) {
			$request = $app['request'];
			return 'pathInfo:' . $request->getpathInfo();
		});

		return $controllers;
	}

	protected function welcome() {
		return function(Application $app) {
			$request = $app['request'];
			return 'welcome:' . $request->getpathInfo();
		};
	}
}