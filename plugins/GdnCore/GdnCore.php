<?php

return function($app) {

	define('APPLICATION', 'Application');
	define('PATH_CACHE', './cache');
	define('HANDLER_TYPE_NORMAL', 'NORMAL');
	define('DS', '/');

	require_once __DIR__ . '/custom.functions.php';
	require_once __DIR__ . '/custom.gdn.php';
	require_once __DIR__ . '/custom.request.php';
	require_once __DIR__ . '/custom.session.php';

	require_once __DIR__ . '/library/functions.general.php';
	require_once __DIR__ . '/library/functions.render.php';
	require_once __DIR__ . '/library/class.url.php';
	require_once __DIR__ . '/library/class.format.php';
	require_once __DIR__ . '/library/class.pluggable.php';

	Gdn::Application($app);

};