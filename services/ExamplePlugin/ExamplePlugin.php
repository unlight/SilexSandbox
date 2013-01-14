<?php

class ExamplePlugin extends Plugin {

	public function EntryController_After_Body_Handler($Sender) {
		$Class =& $Sender->EventArguments['BodyClass'];
		$Class .= ' New';
	}
}