<?php

use Silex\ServiceProviderInterface;
use Silex\Application;

class FormServiceProvider implements ServiceProviderInterface {

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app) {
		// Form.
		$app['form'] = function() use ($app) {
			$session_handler = $app['session.handler'];
			$form = new Form($app, $session_handler);
			$form->construct();
			return $form;
		};
		// Validation.
		$app['validation'] = function() use ($app) {
			$validation = new Validation($app);
			$validation->construct();
			return $validation;
		};
	}

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registers
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(Application $app) {
		// Load custom functions.
		LoadFunctions('Request');
	}
}