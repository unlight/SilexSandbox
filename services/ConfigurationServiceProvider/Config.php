<?php
// Use $app['config']
class Config {

	protected static $app;

	public function app($app = null) {
		if ($app !== null) {
			self::$app = $app;
		}
		return self::$app;
	}

	public static function get($name, $default = false) {
		$result = $default;
		if (isset(self::$app[$name])) {
			$result = self::$app[$name];
		} else {

		}
		return $result;
	}

	public static function __callStatic($name, $arguments) {
		$default = array_key_exists(0, $arguments) ? $arguments[0] : false;
		return self::get($name, $default);
	}
}