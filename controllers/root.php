<?php
use Silex\Application;
$root = $app['controllers_factory'];

$root->get('/', function (Application $app) { 
	return 'Root.'; 
});

return $root;