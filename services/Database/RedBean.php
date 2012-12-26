<?php 

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class R extends RedBean_Facade {};

class Column {
	
	public static function String($Length = 250) {
		return str_repeat('*', $Length);
	}

	public static function Text() {
		return self::String(65536 - 1);
	}

	public static function LongText() {
		return self::String(65536 + 1);
	}

	public static function Float() {
		return 9.99;
	}

	public static function Int() {
		return 1000;
	}

	public static function Date() {
		return date('Y-m-d');
	}

	public static function DateTime() {
		return date('Y-m-d H:i:s');
	}
}

return function($app) {
	if (isset($app['database']['dsn'])) {
		$dsn = $app['database']['dsn'];
	} else {
		$dsn = $app['database']['engine'] . ':host=' . $app['database']['host'] . ';dbname=' . $app['database']['name'];
	}
	R::setup($dsn, $app['database']['user'], $app['database']['password']);
	R::freeze(true);

	$app->get('/redbean/structure/p{password}', function(Application $app, Request $request) {
		$password = $request->get('password');
		if ($password != '123') return $app->redirect($request->getBaseUrl());

		R::freeze(false);
		// R::debug(true);
		$logger = RedBean_Plugin_QueryLogger::getInstanceAndAttach(R::$adapter);

		$user = R::load('user', -1);
		$user->id = -1;
		$user->name = Column::String();
		$user->password = Column::String();
		$user->email = Column::String();
		$user->hash_method = Column::String();
		$user->gender = Column::String(1);
		R::store($user);

		R::freeze(true);

		// Dump logs.
		echo "<pre>", implode("\n", $logger->getLogs()) . "</pre>";
		
		return '';
	});
};