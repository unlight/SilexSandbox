<?php

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Session\Session;


class HomeController extends Controller
{
	public function initialize() {
		parent::initialize();
	}

	public function welcome(Application $app) {
		$request = $app['request'];
		return 'welcome:' . $request->getpathInfo();
	}
}