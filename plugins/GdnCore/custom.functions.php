<?php

function C($Name = FALSE, $Default = FALSE) {
	$result = $Default;
	$newName = strtolower($Name);
	$newName = str_replace('garden.', 'application.', $newName);
	$app = Gdn::Application();
	if (isset($app[$newName])) {
		$result = $app[$newName];
	} else {
		// Fallback.
		$result = Gdn::Config($Name, $Default);
	}
	return $result;
}