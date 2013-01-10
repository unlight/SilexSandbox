<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class TController extends Controller {

	public function Index1(Application $app) {
		// d(new R());
	}

	public function Index2(Application $app) {
		$User = R::dispense('user');
		$UserModel = new UserModel();
		$FoundUser = $UserModel->GetID(1);
		$UserModel->defineColumns();

		$User->importValues(array(
			'family' => 'XXX',
			'name' => 'Joe'
		));

		R::store($User);

		d($User);

		d($FoundUser);
		
		// $obj = RedBean_ModelHelper::factory('UserModel');
		var_dump($obj);die;
		// $obj->loadBean($bean);
		// getWhere
		// d('$user', $user);
	}
}


$controller = new TController($app);
$app->match('/t/1', array($controller, 'Index1'));
$app->match('/t/2', array($controller, 'Index2'));