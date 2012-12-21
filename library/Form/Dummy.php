<?php
namespace Form;


use Novuso\Component\Form\Form;
use Novuso\Component\Form\Element\Label;
use Novuso\Component\Form\Element\Text;

class Dummy extends Form {

	public function __construct() {
		parent::__construct('dummy');

        $textbox = new Text('name');
        $textbox->setLabel('Name:');
        $this->addElement($textbox);
	}

	public function render() {
		$output = '<'.$this->getTagname().$this->getAttrString().'>';
		foreach ($this->queue as $element) {
			$output .= '<fieldset>' . $this->renderElement($element->getName()) . '</fieldset>';
		}
		$output .= '</'.$this->getTagname().'>';

		return $output;
	}
}