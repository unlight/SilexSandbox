<?php

class SqlBuilder extends Sparrow {

	protected static $instance;
	protected $selects = array();

	public static function create() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function reset() {
		parent::reset();
		$this->selects = array();
		return $this;
	}

	public function addselect($fields) {
		$this->selects[] = $fields;
		return $this;
	}

	public function select($fields = '*', $limit = null, $offset = null) {
		if (count($this->selects) > 0) {
			$result = parent::select($this->selects, $limit, $offset);
		} else {
			$result = parent::select($fields, $limit, $offset);
		}
		return $result;
	}

	// protected $db;
	// protected static $instance;

	// public function __construct($db) {
	// 	$this->db = $db;
	// }

	// public function start

	// public static function __callStatic($name, $arguments) {
	// 	if (self::$sparrow === null) {
	// 		self::$sparrow = new Sparrow();
	// 	}
	// 	return call_user_func_array(array(self::$sparrow, $name), $arguments);
	// }
}


return function($app) {

};