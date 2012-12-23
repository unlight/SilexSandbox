<?php 
return function($app) {
	$dsn = $app['database.engine'] . ':host=' . $app['database.host'] . ';dbname=' . $app['database.name'];
	if (isset($app['database.dsn'])) {
		$dsn = $app['database.dsn'];
	}
	class R extends RedBean_Facade {};
	R::setup($dsn, $app['database.user'], $app['database.password']);
	// R::debug(true);
	// require_once 'vendor/gabordemooij/redbean/RedBean/redbean.inc.php'
	// R::freeze( true ); //will freeze redbeanphp
	// R::setup($dsn, $app['database.user'], $app['database.password']);
	// $app['orm'] = $app->share(function() use ($app) {
	// 	require_once __DIR__ . '/View.php';
	// 	return new Gdn\View($app);
	// });
};