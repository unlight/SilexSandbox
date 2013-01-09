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
	'facebook' => array(
		'id' => '112828485553107',
		'secret' => 'ee2d4d3ce64ef059fa5b0e5b2d639460'
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