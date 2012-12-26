<?php
return array(
	'debug' => false,
	'database' => array(
		'engine' => 'mysql',
		'host' => 'localhost',
		'name' => 'dbname',
		'user' => 'dbuser',
		'password' => '',
	),
	'application' => array(
		'title' => 'Silex',
		'cookie' => array(
			'salt' => '',
			'name' => 'silex',
			'path' => '/',
			'domain' => ''
		),
		'session' = array(
			'length' => '15 minutes'
		),
		'forms' => array(
			'honeypotname' => 'hpt'
		)
	),
);