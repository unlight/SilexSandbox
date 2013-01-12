<?php

/**
 * Configuration service provider for Silex.
 * Usage:
 * $app->register(new ConfigurationServiceProvider('conf'))
 * conf - is a directory where your config files are stored.
 */

use Silex\ServiceProviderInterface;
use Silex\Application;

class ConfigurationServiceProvider implements ServiceProviderInterface {

	protected $path = 'conf';

	public function __construct($path = null) {
		loadFunctions('silex');
		if ($path !== null) {
			$this->path = $path;
		}
	}

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app) {
		if (!isset($app['path.conf'])) {
			$app['path.conf'] = $this->path;
		}

		$path = $app['path.conf'];
		if (empty($path)) throw new Exception("Application value 'path.conf' is not defined.");
		if (!is_dir($path)) throw new Exception("Directory '$path' specified in 'path.conf' does not exists.");

		$configuration = array();
		if (file_exists("$path/config-defaults.php")) {
			$configuration = require "$path/config-defaults.php";
		}
		if (file_exists("$path/config.php")) {
			$configuration = mergeArrays(require "$path/config.php", $configuration);
		}
		// Loading configuration.
		foreach ($configuration as $key => $value) {
			$app[$key] = $value;
		}

		// Function to get config value.
		$app['config'] = $app->share(function($app) {
			return function($name, $default = false) use ($app) {
				$value = $default;
				if (isset($app[$name])) {
					$value = $app[$name];
				} else {
					$value = getValueR($name, $app, $default);
					return $value;
				}
				return $value;
			};
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