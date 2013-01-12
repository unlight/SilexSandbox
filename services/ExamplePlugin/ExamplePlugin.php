<?php

class ExamplePlugin extends Plugin {

	public function entrycontroller_after_body_handler($sender) {
		$class =& $sender->EventArguments['BodyClass'];
		$class .= ' New';
	}
}