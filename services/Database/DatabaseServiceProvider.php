<?php

use Silex\ServiceProviderInterface;
use Silex\Application;

class DatabaseServiceProvider implements ServiceProviderInterface {

	protected $app;

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app) {
		$this->app = $app;
		$config = $app['config']('database');
		$app->match('/structure/update', 'StructureController::Update');
	}

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registers
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(Application $app) {
	}
}