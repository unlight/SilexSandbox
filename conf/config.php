<?php
return array(
	'debug' => true,
	'database' => array(
		'engine' => 'mysql',
		'user' => 'root',
		'name' => 'silex',
		'password' => '',
		'structure' => array(
			'access' => array(
				'user' => 'root',
				'password' => 'fok'
			)
		)
	),
	'application' => array(
		'title' => 'Silex 1',
		'cookie' => array(
			'salt' => '',
			'name' => 'silex',
			'path' => '/',
			'domain' => ''
		)
	)
);