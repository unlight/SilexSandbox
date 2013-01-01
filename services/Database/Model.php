<?php

// class Model extends RedBean_SimpleModel {
class Model extends RedBean_SimpleModel {

	protected $name;
	protected $validation;

	public function __construct($name) {
		$this->name = $name;
		$this->validation = new Validation();
	}

	public function getId($id) {
		$sql = SqlBuilder::create()
			->from($this->name)
			->select()
			->where('id', $id)
			->sql();
		$result = R::getRow($sql);
		return $result;
	}

	public function getWhere($where) {
		$sql = SqlBuilder::create()
			->from($this->name)
			->select()
			->where($where)
			->sql();
		$result = R::getAll($sql);
		return $result;
	}

	public function get($conditions = false, $offset = null, $limit = null)  {
		$queryCount = getValue('queryCount', $conditions);
		$sqlBuilder = SqlBuilder::instance();
		$sqlBuilder->from($this->name);
		if ($queryCount) {
			$sqlBuilder->select('count(*) as count');
		} else {
			$sqlBuilder->select();
		}
		$sql = $sqlBuilder->sql();
		$result = R::getAll($sql);
		return $result;
	}

	public function after_update() {
		d(__METHOD__, func_get_args());
	}

}