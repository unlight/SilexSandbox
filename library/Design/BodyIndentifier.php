<?php
namespace Design;

//use Symfony\Component\EventDispatcher\EventDispatcher;

class BodyIndentifier {

	public $controllerName;
	public $methodName;
	
	public function __construct($app) {
		$routeName = $app['request']->attributes->get('_route');
		$routeParams = array_keys($app['request']->attributes->get('_route_params'));
		$parts = explode('_', $routeName);
		$newParts = array();
		for ($i = 1, $count = count($parts); $i < $count; $i++) {
			if (in_array($parts[$i], $routeParams)) continue;
			$newParts[] = $parts[$i];
		}
		$this->controllerName = $newParts[0];
		$this->methodName = isset($newParts[1]) ? $newParts[1] : '';
	}

	public function __toString() {
		return $this->controllerName . '_' . $this->methodName;
	}
}