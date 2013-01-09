<?php

namespace Unlight;

use Silex\Application;

class SessionHandler {

	protected $app;
	public $userId;
	
	public function __construct(Application $app) {
		$this->app = $app;
	}

	/**
	 * [start description]
	 * @param  mixed $userId [description]
	 * @return [type]          [description]
	 */
	public function start($userId = false) {
		$this->userId = ($userId !== false) ? $userId : 0;
		if ($this->userId > 0) {
			$session = $this->app['session'];
			$session->set('userId', $this->userId);
			$this->transientKey(randomString(12));
		}
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