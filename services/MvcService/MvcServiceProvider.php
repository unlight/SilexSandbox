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
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app) {
		$this->app = $app;
		loadFunctions('request');
		loadFunctions('string');
		$this->registerRequestInfo();
		$this->registerBodyIdentifier();
		$this->registerBodyClass();
		$this->registerDefaultController();
		$this->registerControllersB();
	}

	/**
	 * [registerControllersB description]
	 * @return [type] [description]
	 */
	protected function registerControllersB() {
		$pathinfo = explode('/', StaticRequest('PathInfo'));
		$pathinfo = array_values(array_filter($pathinfo));
		$controller = ucfirst($pathinfo[0]) . 'Controller';
		$action = getValue(1, $pathinfo, 'index');
		$controllerFile = $this->controllers . '/' . $pathinfo[0] . '.php';
		$app = $this->app;
		$app->match(implode('/', $pathinfo), function() use ($app, $controller, $action) {
			$controller = new $controller($app);
			$controller->initialize();
			$controllerMethod = array($controller, $action);
			$arguments = $app['resolver']->getArguments($app['request'], $controllerMethod);
			return call_user_func_array($controllerMethod, $arguments);
		});
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
		if ($name) {
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
			$parts = array_filter($parts);
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