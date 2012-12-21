<?php
namespace Route;

use Silex\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Silex\Application;

class RequestInfo implements ServiceProviderInterface {

	public $controller;
	public $method;

	protected function getInfo(Application $app) {
		$routeName = $app['request']->attributes->get('_route');
		$routeParams = array_keys($app['request']->attributes->get('_route_params'));
		$parts = explode('_', $routeName);
		$newParts = array();
		for ($i = 1, $count = count($parts); $i < $count; $i++) {
			if (in_array($parts[$i], $routeParams)) continue;
			$newParts[] = $parts[$i];
		}
		$this->controller = isset($newParts[0]) ? $newParts[0] : 'home';
		$this->method = isset($newParts[1]) ? $newParts[1] : 'index';
		return $this;
	}

	public function register(Application $app) {
		$app['controller_request_info'] = $app->share(function() use ($app) {
			return $this->getInfo($app);
		});
	}

	public function boot(Application $app) {
	}

}