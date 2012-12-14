<?php
namespace Design;

//use Symfony\Component\EventDispatcher\EventDispatcher;

class BodyClass {

	public $value;
	
	public function __construct($app) {
	}

	public function __toString() {
		return $this->controllerName . '_' . $this->methodName;
	}
}