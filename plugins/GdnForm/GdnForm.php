<?php

return function($app) {

	require_once __DIR__ . '/library/class.form.php';

	$app['form'] = function() {
		return new Gdn_Form();
	};
};