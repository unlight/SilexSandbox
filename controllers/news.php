<?php
use Silex\Application;

$root = $app['controllers'];



$root->get('/', function (Application $app) { 
	return 'News Index.';
});

return $root;