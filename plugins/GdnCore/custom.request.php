<?php

require_once __DIR__ . '/library/class.request.php';

/**
 * Custom Gdn_Request class.
 */

class CustomRequest extends Gdn_Request {

	public function __construct() {
	}

	protected static function GetRequest() {
		$app = Gdn::Application();
		return $app['request'];
	}

	public function __call($Method, $Arguments) {
		$ArgumentsString = var_export($Arguments, TRUE);
		throw new Exception("__call: Error Processing Request for ($Method) $ArgumentsString");
	}


	public function RequestMethod() {
		return self::GetRequest()->getMethod();
	}
	

	protected function _ParsedRequestElement($Key, $Value = NULL) {
		$request = self::GetRequest();
		switch ($Key) {
			case 'WebRoot': return $request->getBasePath();
			default: break;
		}
		throw new Exception("Error Processing _ParsedRequestElement, unknown key: $Key.");
	}

	protected function _EnvironmentElement($Key, $Value = NULL) {
		$request = self::GetRequest();
		switch ($Key) {
			case 'ConfigWebRoot': return $request->getBasePath();
			case 'ConfigStripUrls': return FALSE;
			default: break;
		}
		throw new Exception("Error Processing _EnvironmentElement, unknown key: $Key.");
	}

	public function Url($Path = '', $WithDomain = FALSE, $SSL = NULL) {
		$request = self::getRequest();
		if ($Path == '') $Path = $request->getPathInfo();
		if ($WithDomain) {
			$Result = $request->getUriForPath($Path);
		} else {
			$Result = $request->getBaseUrl() . $Path;
		}
		return $Result;
	}

	public function IpAddress() {
		return self::getRequest()->getClientIp();
	}
}