<?php
use Silex\Application;

class EntryController extends Controller {

	public function initialize() {
		$this->addCssFile('style.css');
	}
	
	public function register(Application $app) {
		$form = $this->form = $app['form'];
		$validation = $app['validation'];
		$validation->applyRule('name', 'Required', 'Ваше имя.');
		$validation->applyRule('email', 'Required');
		$validation->applyRule('email', 'Email');

		if ($form->isPostBack()) {
			$values = $form->formValues();
			$isValid = $validation->validate($values);
			if ($isValid) {

			} else {
				$form->setValidationResults($validation->results());
			}
			// d($isValid, $validation->results());
		}
		// d(1, $app['request']->getBasePath(), GetWebRoot());
		return $this->render();
	}
}