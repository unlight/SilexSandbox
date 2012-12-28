<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

// $controller = $this->app['controllers'];
// $app->mount('/news', include 'controllers/news.php');

$app->match('/test2', 'TestTwoController::Index');


/**
 * Test2 Controller
 */
class TestTwoController extends Controller {
	

	public function Index(Application $app) {
		return 'Hello from Index of TestTwoController.';
	}
}
