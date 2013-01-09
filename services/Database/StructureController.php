<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class StructureController {

	protected function runUpdate() {
		R::freeze(false);
		// R::debug(true);
		$logger = RedBean_Plugin_QueryLogger::getInstanceAndAttach(R::$adapter);

		$user = R::load('user', -1);
		$user->id = -1;
		$user->name = Column::String();
		$user->password = Column::String();
		$user->email = Column::String();
		$user->hash_method = Column::String();
		$user->gender = Column::String(1);

		$user->provider = Column::String();
		$user->provider_uid = Column::String();
		$user->date_inserted = Column::DateTime();
		$user->date_updated = Column::DateTime();

		R::store($user);

		R::freeze(true);

		// Dump logs.
		return "<pre>" . implode("\n", $logger->getLogs()) . "</pre>";
	}
	
	public function update(Application $app) {
		$access = $app['config']('database.structure.access');
		$request = $app['request'];
		$username = $request->server->get('PHP_AUTH_USER', false);
		$password = $request->server->get('PHP_AUTH_PW');

		if ($username == $access['user'] && $password == $access['password']) {
			return $this->runUpdate();
		}
		$response = new Response();
		$response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', ''));
		$response->setStatusCode(401, 'Please sign in.');
		return $response;
	}

}