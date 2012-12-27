<?php

namespace Unlight;

use Silex\Application;

class SessionHandler {

	protected $app;
	
	public function __construct(Application $app) {
		$this->app = $app;
	}

	/**
	 * [transientKey description]
	 * @param  [type] $newKey [description]
	 * @return [type]         [description]
	 */
	public function transientKey($newKey = NULL) {
		$session = $this->app['session'];
		if ($newKey !== NULL) {
			$session = $this->app['session'];
			$session->set('transientKey', $newKey);
		}
		if ($session->has('transientKey')) {
			return $session->get('transientKey');
		} else {
			return randomString(12);
		}
	}
}