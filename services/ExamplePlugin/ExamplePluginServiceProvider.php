<?php

use Silex\ServiceProviderInterface;
use Silex\Application;


require_once __DIR__ . '/ExamplePlugin.php';

class ExamplePluginServiceProvider implements ServiceProviderInterface {

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app) {
		$dispatcher = $app['dispatcher'];
		$dispatcher->addListener('before.body', function($event) {
			// d($event);
		});
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