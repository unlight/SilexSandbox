<?php

use Silex\ServiceProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\GenericEvent;


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
		$app['event'] = new GenericEvent();
		
		loadFunctions('silex');
		loadFunctions('request');
		loadFunctions('string');

		$this->registerRequestInfo();
		$this->registerBodyIdentifier();
		$this->registerBodyClass();
		$this->registerDefaultController();
		$this->registerOtherControllers();
	}

	/**
	 * [registerOtherControllers description]
	 * @return [type] [description]
	 */
	protected function registerOtherControllers() {
		// TODO: Keep one
		// $this->registerControllers();
		// $this->registerControllersC();
		// $this->registerControllersAnnotation();
		$this->registerControllersRoute();
	}

	public function registerControllersRoute() {
		$pathinfo = explode('/', StaticRequest('PathInfo'));
		$pathinfo = array_filter($pathinfo);
		$name = array_shift($pathinfo);
		// $routeFile = $this->app['config']('routes')
		$routeFile = 'settings/routes.php';
		if (file_exists($routeFile)) {
			$routes = require $routeFile;
			$routes = getValue($name, $routes, array());
			foreach ($routes as $match => $controller) {
				$this->app->match($match, $controller);
			}
		}
	}

	/**
	 * [registerControllersAnnotation description]
	 * Annotations.
	 * @return [type] [description]
	 */
	protected function registerControllersAnnotation() {
		$pathinfo = explode('/', StaticRequest('PathInfo'));
		$pathinfo = array_filter($pathinfo);
		$file = array_shift($pathinfo);
		$method = array_shift($pathinfo);
		$controllerFile = $this->controllers . '/' . $file . '.php';
		if (file_exists($controllerFile)) {
			require $controllerFile;
			$class = ucfirst($file) . 'Controller';
			$cacheFile = 'cache/annotations/' . md5_file($controllerFile);
			$controller = new $class($this->app);
			if (!file_exists($cacheFile)) {
				$reflectionAnnotatedMethod = new ReflectionAnnotatedMethod($class, $method);
				$annotations = $reflectionAnnotatedMethod->getAnnotations();
				$match = getValueR('match', $annotations, false, true);
				$appController = $this->app->match($match->getAnnotation(), array($controller, $method));
				foreach ($annotations as $annotation) {
					$name = $annotation->getName();
					if (method_exists($appController->getRoute(), $name)) {
						$value = $annotation->value;
						if (is_null($value)) $value = $annotation->getAnnotation();
						if (!is_array($value)) $value = array($value);
						call_user_func_array(array($appController, $name), $value);
					}
				}
			}
		}
	}

	/**
	 * [registerControllersC description]
	 * @return [type] [description]
	 */
	protected function registerControllersC() {
		$pathinfo = explode('/', StaticRequest('PathInfo'));
		$pathinfo = array_filter($pathinfo);
		$firstpart = array_shift($pathinfo);
		if ($firstpart) {
			$controllerFile = $this->controllers . '/' . $firstpart . '.php';
			if (file_exists($controllerFile)) {
				$class = ucfirst($firstpart) . 'Controller';
				$controller = new $class($this->app);
				$controller->initialize();
				if (isset($routes) && is_array($routes)) {
					foreach ($routes as $match => $method) {
						$this->app->match($match, array($controller, $method));
					}
				}
				require $controllerFile;
			}
		}
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