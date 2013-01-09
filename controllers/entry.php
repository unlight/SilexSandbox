<?php
use Silex\Application;

class EntryController extends Controller {

	public function initialize() {
		$this->addCssFile('style.css');
		$this->addCssFile('entry.css');
	}

	public function register(Application $app) {
		$this->addCssFile('icons.css');
		$this->form = $app['form'];
		return $this->render();
	}

	public function connectEndPoint() {
		Hybrid_Endpoint::process();
	}

	public function connect(Application $app, $with) {
		$sessionHandler = $app['session.handler'];
		$keys = $app[$with];
		$providers = array('enabled' => true, 'keys' => $keys);
		$config = array(
			'base_url' => geturl('/entry/connect-end-point', true),
			'providers' => array($with => $providers)
		);
		// $config['debug_mode'] = true;
		// $config['debug_file'] = 'debug_log.txt';
		$hybridauth = new Hybrid_Auth($config);
		$adapter = $hybridauth->authenticate($with);
		$profile = $adapter->getUserProfile();

		$userModel = new UserModel();
		$user = $userModel->getByProvider($with, $profile->identifier);
		if ($user) {
			d($user, 'User exists.');
		}
		if ($profile->email) {
			$user = $userModel->getByEmail($profile->email);
			if ($user) {
				// Well! the email returned by the provider already exist in our database
				// so in this case you might use the 
				// <a href="index.php?route=users/login">Sign-in</a> 
				// to login using your email and password.</b>
				return $this->redirect('entry/login');
			}
		}
		$newUser = R::dispense('user');
		$newUser->importValues($profile);
		R::store($newUser);

		$sessionHandler->start($newUser->getId());

		return $this->redirect("users/profile");
	}

	public function load1(Application $app) {
		// $user = R::load('user', 1);
		// $user = R::dispense('user', 1);
		$userModel = new UserModel();
		$user = $userModel->getId(1);
		d($user);
	}
	
	public function testregister(Application $app) {
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

$controller = new EntryController($app);

$app->match('entry/testregister', array($controller, 'testregister'));
$app->match('entry/register', array($controller, 'register'));
$app->match('entry/connect/{with}', array($controller, 'connect'));
$app->match('entry/connect-end-point', array($controller, 'connectEndPoint'));
$app->match('entry/load1', array($controller, 'load1'));