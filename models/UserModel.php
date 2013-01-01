<?php

class UserModel extends Model {

	public function __construct() {
		parent::__construct('user');
	}

	public function validate() {
		// $this->validation->applyRule('name', 'Required');
		// $this->validation->applyRule('email', 'Required');
		// $this->validation->applyRule('email', 'Email');
	}

	public function getByProvider($provider, $provider_uid) {
		$where = array('provider' => $provider, 'provider_uid' => $provider_uid);
		$result = $this->getWhere($where);
		return $result;
	}

	public function getByEmail($email) {
		$where['email'] = $email;
		SqlBuilder::instance()
			->limit(1);
		$result = $this->getWhere($where);
		return $result;
	}

	public function getByIdentifier($user_id) {
		$where['identifier'] = $user_id;
		$result = $this->getWhere($where);
		return $result;
	}

}