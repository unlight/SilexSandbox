<?php

use Symfony\Component\HttpFoundation\Response;
use Silex\ServiceProviderInterface;
use Silex\Application;

class DatabaseServiceProvider implements ServiceProviderInterface {

	protected $app;

	public function update(Application $app) {
		$access = $app['config']('database.structure.access');
		$request = $app['request'];
		$username = $request->server->get('PHP_AUTH_USER', false);
		$password = $request->server->get('PHP_AUTH_PW');

		if ($username == $access['user'] && $password == $access['password']) {
			R::freeze(false);
			// R::debug(true);
			$logger = RedBean_Plugin_QueryLogger::getInstanceAndAttach(R::$adapter);
			$structureFile = 'settings/structure.php';
			if (file_exists($structureFile)) {
				include $structureFile;
			}

			R::freeze(true);
			
			// Dump logs.
			return "<pre>" . implode("\n", $logger->getLogs()) . "</pre>";
		}

		$response = new Response();
		$response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', ''));
		$response->setStatusCode(401, 'Please sign in.');
		return $response;
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
		$this->app = $app;
		$app->match('/structure/update', array($this, 'update'));
	}

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registers
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(Application $app) {
		if (isset($app['database']['dsn'])) {
			$dsn = $app['database']['dsn'];
		} else {
			$dsn = $app['database']['engine'] . ':host=' . $app['database']['host'] . ';dbname=' . $app['database']['name'];
		}
		R::setup($dsn, $app['database']['user'], $app['database']['password']);
		R::freeze(true);
		RedBean_ModelHelper::setModelFormatter(new ModelFormatter());
	}
}