<?php

use Silex\Application;
use Silex\ControllerProviderInterface;

abstract class Controller implements ControllerProviderInterface {
	
	protected $app;

	public function connect(Application $app) {
		$this->app = $app;
		$self =& $this;
		$controller = $app['controllers'];
		foreach (get_class_methods($self) as $method) {
			$controller->get("/{$method}", function() use ($app, $self, $method) {
				$self->initialize();
				return $self->$method($app);
			});
		}
		return $controller;
	}

	public function initialize() {
	}

}