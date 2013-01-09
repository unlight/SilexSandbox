<?php

class Model extends RedBean_SimpleModel {

	protected $name;
	protected $validation;

	public function __construct($name) {
		$this->name = $name;
		$this->validation = new Validation();
	}

	protected function beans($rows) {
		if (is_string($rows)) {
			$rows = R::getAll($rows);
		}
		$redbean = R::getRedBean();
		$beans = $redbean->convertToBeans($this->name, $rows);
		return $beans;
	}

	public function getId($id) {
		$sql = SqlBuilder::init()
			->from($this->name)
			->select()
			->where('id', $id)
			->limit(1)
			->sql();
		$beans = $this->beans($sql);
		$result = reset($beans);
		return $result;
	}

	public function getWhere($where) {
		$sql = SqlBuilder::init()
			->from($this->name)
			->select()
			->where($where)
			->sql();
		$result = R::getAll($sql);
		return $result;
	}

	public function get($conditions = false, $offset = null, $limit = null)  {
		$queryCount = getValue('queryCount', $conditions);
		$sqlBuilder = SqlBuilder::init();
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