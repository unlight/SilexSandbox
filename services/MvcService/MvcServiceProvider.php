<?php

use Silex\ServiceProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

class MvcServiceProvider implements ServiceProviderInterface {

	protected $app;

	// Defaults.
	protected $views = 'views';
	protected $controllers = 'controllers';
	protected $models = 'models';

	protected $pathinfo;

	/**
	 * Returns pathInfo of request.
	 * @return string
	 */
	protected function getPathinfo() {
		if ($this->pathinfo === null) {
			if (array_key_exists('PATH_INFO', $_SERVER)) {
				$this->pathinfo = $_SERVER['PATH_INFO'];
			} elseif (array_key_exists('SCRIPT_NAME', $_SERVER) && array_key_exists('PHP_SELF', $_SERVER)) {
				$this->pathinfo = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']));
			}
		}
		return $this->pathinfo;
	}

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app, $configuration = array()) {
		$this->app = $app;
		if (is_array($configuration)) {
			foreach ($configuration as $key => $value) {
				
			}
		}

		$this->registerDefaultController();
		$this->registerControllers();
		$this->registerRequestInfo();
		$this->registerBodyIdentifier();
		$this->registerBodyClass();
	}

	/**
	 * Регистрирует контроллер в зависимости от запроса.
	 * Первая часть запроса до слэша будет именем контроллера.
	 * Нет необходимости инитиализировать все контроллеры.
	 * Но тогда не будет возможности забиндить имя.
	 * @return null
	 */
	protected function registerControllers() {
		$path = trim($this->getPathinfo(), '/');
		$slashPos = strpos($path, '/');
		if ($slashPos === false) return;
		$name = substr($path, 0, $slashPos);
		if (!$name) {
			$controllerFile = $this->controllers . '/' . $name. '.php';
			if (file_exists($controllerFile)) {
				$module = require $controllerFile;
				if ($module == 1) {
					$class = ucfirst($name) . 'Controller';
					$module = new $class;
				}
				$this->app->mount($name, $module);
			}
		}
	}

	/**
	 * [registerDefaultController description]
	 * @return [type] [description]
	 */
	protected function registerDefaultController() {
		$controllerFile = $this->controllers . '/' . 'root' . '.php';
		if (file_exists($controllerFile)) {
			$this->app->mount('/', require $controllerFile);
		}
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

	/**
	 * [registerRequestInfo description]
	 * @return [type] [description]
	 */
	protected function registerRequestInfo() {
		$app = $this->app;
		$app['request.info'] = $app->share(function() use ($app) {
			$routeName = $app['request']->attributes->get('_route');
			$routeName = str_replace(array('GET', 'POST'), '', $routeName);
			$routeParams = array_keys($app['request']->attributes->get('_route_params'));
			$parts = explode('_', $routeName);
			foreach ($routeParams as $routeParam) {
				$key = array_search($routeParam, $parts);
				if ($key !== false) unset($parts[$key]);
			}
			$parts = array_values($parts);
			
			$result['controller'] = isset($parts[0]) ? $parts[0] : 'home';
			$result['method'] = isset($parts[1]) ? $parts[1] : 'index';

			return $result;
		});
	}

	/**
	 * [registerBodyIdentifier description]
	 * @return [type] [description]
	 */
	protected function registerBodyIdentifier() {
		$app = $this->app;
		$app['body.identifier'] = $app->share(function() use ($app) {
			$info = $app['request.info'];
			return $info['controller'] . '_' . $info['method'];
		});
	}


	/**
	 * [registerBodyClass description]
	 * @return [type] [description]
	 */
	protected function registerBodyClass() {
		$app = $this->app;
		$app['body.class'] = $app->share(function() use ($app) {
			$info = $app['request.info'];
			$names = array($info['controller'], $info['method']);
			return implode(' ', array_map('ucfirst', $names));
		});
	}
}