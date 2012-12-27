<?php
use Silex\Application;

class EntryController extends Controller {

	public function initialize() {
		$this->addCssFile('style.css');
	}
	
	public function register(Application $app) {
		$this->form =$form = $app['form'];
		// d(1, $app['request']->getBasePath(), GetWebRoot());
		return $this->render();
	}
}