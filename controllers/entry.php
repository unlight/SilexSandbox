<?php
use Silex\Application;

class EntryController extends Controller {

	public function initialize() {
		$this->app['view']->addCssFile('style.css');
	}
	
	public function register(Application $app) {
		$view = $app['view'];
		$view->form = new Gdn_Form();
		return $view->render();
	}
}