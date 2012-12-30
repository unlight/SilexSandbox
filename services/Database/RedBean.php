<?php 

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class R extends RedBean_Facade {};

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