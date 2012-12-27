<?php

use Silex\Application;
// use Silex\ControllerProviderInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;

// abstract class Controller extends EventDispatcher implements ControllerProviderInterface {
abstract class Controller extends EventDispatcher {
	
	protected $app;
	protected $assets = array();
	protected $view = '';
	protected $masterView = '';
	public $data = array();
	protected $cssFiles = array();
	protected $jsFiles = array();
	public $head;

	public function __construct(Application $app) {
		$this->app = $app;
	}

	// public function connect(Application $app) {
	// 	$this->app = $app;
	// 	$self =& $this;
	// 	$controller = $app['controllers'];
	// 	// TODO: Do it by Reflection
	// 	// TODO: Extract require params...
	// 	foreach (get_class_methods($self) as $method) {
	// 		$controller->match($method, function() use ($app, $self, $method) {
	// 			$self->initialize();
	// 			return $self->$method($app);
	// 		});
	// 	}
	// 	$app['controller'] = $this;
		
	// 	return $controller;
	// }

	public function initialize() {
	}

	/**
	 * [transientKey description]
	 * @param  [type] $NewKey [description]
	 * @return [type]         [description]
	 */
	public function transientKey($NewKey = NULL) {
		$session = $this->app['session'];
		if ($NewKey !== NULL) {
			$session = $this->app['session'];
			$session->set('TransientKey', $NewKey);
		}
		if ($session->has('TransientKey')) {
			return $session->get('TransientKey');
		} else {
			return RandomString(12);
		}
	}


	public function addJsFile($file) {
		$info = array(
			'file' => $file
		);
		$this->jsFiles[] = $info;
	}

	public function addCssFile($file) {
		$info = array(
			'file' => $file
		);
		$this->cssFiles[] = $info;
	}

	private function getControllerName() {
		return $this->app['request.info']['controller'];
	}

	private function getMethodName() {
		return $this->app['request.info']['method'];	
	}

	public function addAsset($asset, $name = 'content') {
		$this->assets[$name][] = $asset;
	}

	private function renderAsset($name) {
		$collection =& $this->assets[$name];
		if ($collection) {
			foreach ($collection as $asset) {
				echo strval($asset);
			}
		}
	}

	public function config($name, $default = false) {
		return $this->app['config']($name, $default);
	}

	public function render($vars = array()) {
		if (count($vars) == 0) {
			$vars = get_object_vars($this);
		}
		if (!$this->view) $this->view = $this->getControllerName() . '/' . $this->getMethodName();
		$view = 'views/' . $this->view . '.php';
		if (!$this->masterView) $this->masterView = 'default.master.php';
		$masterView = 'views/' . $this->masterView;
		// d($masterView, $view);
		return $this->renderMaster($vars, $view, $masterView);
	}

	public function renderMaster($vars, $viewPath, $masterViewPath) {

		if (!$this->head) $this->head = new HeadModule($this);
		foreach ($this->cssFiles as $cssFileInfo) {
			$file = $cssFileInfo['file'];
			$this->head->addCss("design/$file");	
		}
		foreach ($this->jsFiles as $jsFileInfo) {
			$file = $jsFileInfo['file'];
			$this->head->addScript("js/$file");	
		}
		$this->addAsset($this->head, 'head');
		
		extract($vars);
		
		ob_start();
		require $viewPath;
		$content = ob_get_clean();
		
		$this->addAsset($content, 'content');

		ob_start();
		require $masterViewPath;
		$html = ob_get_clean();

		return $html;
	}

	/**
	* Set data from a method call.
	*
	* @param string $key The key that identifies the data.
	* @param mixed $value The data.
	* @param mixed $addproperty Whether or not to also set the data as a property of this object.
	* @return mixed The $value that was set.
	*/
	public function setData($key, $value = NULL, $addproperty = FALSE) {
		if (is_array($key)) {
			$this->data = array_merge($this->data, $key);

			if ($addproperty === TRUE) {
				foreach ($key as $name => $value) {
					$this->$name = $value;
				}
			}
			return;
		}
		
		$this->data[$key] = $value;
		if ($addproperty === TRUE) {
			$this->$key = $value;
		}
		return $value;
	}

	/** Get a value out of the controller's data array.
	*
	* @param string $path The path to the data.
	* @param mixed $default The default value if the data array doesn't contain the path.
	* @return mixed
	* @see GetValueR()
	*/
	public function data($path, $default = '') {
		$Result = GetValueR($path, $this->data, $default);
		return $Result;
	}
}