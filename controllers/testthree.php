<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$controllers = $this->app['controllers'];

$controllers->match('/', function() {
	return 'Ok)';
});

$app->mount('/testthree', $controllers);

/**
 * Test3 Controller
 */
class TestThreeController extends Controller {
	

	public function Index(Application $app) {
		return 'Hello from Index of TestThreeController.';
	}
}
