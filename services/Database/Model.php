<?php

class Model extends RedBean_SimpleModel {

	protected $name;
	protected $columns;
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

	public function importValues($values) {
		if (!is_array($values)) $values = (array) $values;
		$this->defineColumns();
		$values = array_intersect_key($values, $this->columns);
		$this->bean->import($values);
	}

	public function defineColumns() {
		if (is_null($this->columns)) {
			$this->columns = R::getColumns($this->name);
		}
		return $this->columns;
	}

	public function update() {
		$this->defineColumns();
		if (array_key_exists('date_updated', $this->columns)) {
			$this->bean->date_updated = date('Y-m-d H:i:s');
		}
		if ($this->bean->getID() == 0) {
			if (array_key_exists('date_inserted', $this->columns)) {
				$this->bean->date_inserted = date('Y-m-d H:i:s');
			}
		}
	}

	public function validate() {
	}

	public function getId($id) {
		$sql = SqlBuilder::init()
			->from($this->name)
			->where('id', $id)
			->limit(1)
			->select()
			->sql();
		$beans = $this->beans($sql);
		$result = reset($beans);
		return $result;
	}

	public function getWhere($where) {
		$sql = SqlBuilder::init()
			->from($this->name)
			->where($where)
			->select()
			->sql();
		$result = $this->beans($sql);
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

		if ($queryCount) {
			$result = R::getCell($sql);
		} else {
			$result = $this->beans($sql);
		}
		return $result;
	}

}