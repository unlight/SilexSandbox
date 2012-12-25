<?php
return array(
	'debug' => true,
	'database' => array(
		'engine' => 'mysql',
		'user' => 'root',
		'name' => 'silex',
		'password' => '',
	),
	'application' => array(
		'title' => 'Silex 1',
		'cookie' => array(
			'salt' => '',
			'name' => 'silex',
			'path' => '/',
			'domain' => ''
		)
	),
	'enabledplugins' => array(
		'Controller' => true, 
		'GdnCore' => true, 
		'GdnForm' => true, 
		'GdnView' => true, 
		'BodyIdentifier' => true, 
		'RequestInfo' => true, 
		'RedBean' => true, 
		'ViewBodyClass' => true, 
		'SqlBuilder' => true,
	),
);