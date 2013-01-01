<?php

class SqlBuilder extends Sparrow {

	protected static $instance;
	protected $selects = array();

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function create() {
		return self::instance();
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
}