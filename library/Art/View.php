<?php
namespace Art;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;

class View extends EventDispatcher {
	
	private $app = null;
	private $blocks = array();
	private $assets = array();

	public function __construct($app) {
		$this->app = $app;
	}

	public function addJsFile($file) {

	}

	public function addCssFile($file) {

	}

	public function addAsset($asset, $name = 'content') {
		$this->assets[$name][] = $asset;
	}

	private function renderAsset($name) {
		$collection =& $this->assets[$name];
		if ($collection) {
			foreach ($collection as $asset) {
				if (is_string($asset)) {
					echo $asset;
				} else {
					throw new \Exception("Unknown type of asset.");
				}
			}
		}
	}

	public function render($vars = array()) {
		if (count($vars) == 0) {
			$vars = get_object_vars($this);
			unset($vars['app'], $vars['blocks']);
		}
		$request = $this->app['request'];
		$parts = explode('/', $request->getPathInfo());
		$controllerName = $parts[1];
		if (!isset($parts[2])) $parts[2] = 'index';
		$methodName = $parts[2];

		return $this->renderLayout('default.master.php', "$controllerName/$methodName.php", $vars);
	}

	public function renderLayout($layout, $template, $vars = array()) {
		$path = $this->app['path.root'] . '/views';
		
		// require_once ROOT . '/helpers.php';
		extract($vars, EXTR_SKIP);
		$app = $this->app;
		
		ob_start();
		require $path . '/' . $template;
		$content = ob_get_clean();

		$this->addAsset($content, 'content');
		
		if ( null == $layout ) {
			return $content;
		}
		
		ob_start();
		require_once $path . '/' . $layout;
		$html = ob_get_clean();

		return $html;
	}
		
	function renderController($uri) {
		$request = $this->app['request'];
		$sign = strpos($uri, "?") ? "&" : "?";
		$uri = "{$uri}{$sign}subrequest=1";

		$subRequest = Request::create(
			$uri, 'get', array(), $request->cookies->all(), 
			array(), $request->server->all()
		);
		
		if ( $request->getSession() ) {
			$subRequest->setSession( $request->getSession() );
		}

		$response = $this->app->handle(
			$subRequest, HttpKernelInterface::SUB_REQUEST, false
		);

		if ( !$response->isSuccessful() ) {
			throw new \RuntimeException(sprintf(
				'Error when rendering "%s" (Status code is %s).', 
				$request->getUri(), $response->getStatusCode()
			));
		}

		return $response->getContent();
	}

}