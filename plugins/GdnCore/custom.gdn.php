<?php

/* Custom Gdn class */

class Gdn {

	public static function Application(Silex\Application $NewApplication = NULL) {
		static $Application;
		if ($NewApplication !== NULL) {
			$Application = $NewApplication;
		}
		return $Application;
	}

	protected static $Configuration;
	protected static $Instances;

	public static function GetConfiguration() {
		if (self::$Configuration === NULL) {
			self::$Configuration = require __DIR__ . '/conf/config-defaults.php';
		}
		return self::$Configuration;
	}

	public static function Config($Name = FALSE, $Default = FALSE) {
		// TODO: Allow add custom config.
		$Configuration = self::GetConfiguration();
		$Result = GetValueR($Name, $Configuration, $Default);
		return $Result;
	}

	protected static function LoadInstance($Name) {
		if (!isset(self::$Instances[$Name])) {
			require_once __DIR__ . '/custom.' . strtolower($Name) . '.php';
			$ClassName = 'Custom' . $Name;
			self::$Instances[$Name] = new $ClassName;
		}
		return self::$Instances[$Name];
	}

	public static function Request() {
		return self::LoadInstance('Request');
	}

	public static function Session() {
		return self::LoadInstance('Session');
	}

	public static function Translate($Code, $Default = FALSE) {
		return $Code;
	}
}