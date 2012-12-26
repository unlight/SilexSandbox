<?php
use Silex\Application;

class EntryController extends Controller {

	public function initialize() {
		$this->addCssFile('style.css');
	}
	
	public function register(Application $app) {
		d($_SERVER);
		$form = $app['form'];
		return $this->render();
	}
}