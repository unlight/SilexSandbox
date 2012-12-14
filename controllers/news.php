<?php
use Silex\Application;

$root = $app['controllers_factory'];

$root->get('/', function (Application $app) { 
	return 'News Index.';
});

return $root;