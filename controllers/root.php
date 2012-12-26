<?php
use Silex\Application;
$root = $this->app['controllers_factory'];

$root->get('/', function (Application $app) { 
	return 'Root.'; 
});

return $root;