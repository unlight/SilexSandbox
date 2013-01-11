<?php

namespace Unlight;

use Silex\ServiceProviderInterface;
use Silex\Application;
use Unlight\SessionHandler;

class SessionHandlerServiceProvider implements ServiceProviderInterface {

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app) {
		if (!isset($app['session'])) {
			$app->register(new Silex\Provider\SessionServiceProvider());
		}
		$app['session.handler'] = $app->share(function ($app) {
			return new SessionHandler($app);
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