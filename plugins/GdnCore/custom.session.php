<?php

/**
 * Custom Gdn_Session class.
 */

require_once __DIR__ . '/library/class.session.php';

class CustomSession extends Gdn_Session {

	protected static function getSession() {
		$app = Gdn::Application();
		if (!isset($app['session'])) {
			$app->register(new Silex\Provider\SessionServiceProvider());
		}
		$session = $app['session'];
		return $session;
	}

	public function TransientKey($NewKey = NULL) {
		if ($NewKey !== NULL) {
			$session = self::getSession();
			$this->_TransientKey = $NewKey;
			$session->set('TransientKey', $this->_TransientKey);
		}
		if ($this->_TransientKey !== FALSE) {
			return $this->_TransientKey;
		} else {
			return RandomString(12);
		}
	}
}