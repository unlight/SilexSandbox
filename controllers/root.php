<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$root = $this->app['controllers'];

$root->match('/', function (Application $app, Request $rq) { 
	return 'Root.'; 
});

return $root;