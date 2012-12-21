<?php
use Silex\Application;
use Form\Dummy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Templating\PhpEngine;

$controller = $app['controllers_factory'];

$controller->get('/', function (Application $app) { 
	return 'Test Index.';
});

$controller->get('/form', function (Application $app) {
	$form = new Dummy();
	$form->setRequest($app['request']);
});

$app['form.renderer'] = $app->share(function() {
	
	class FormViewRender {

		public function input($element) {
			$name = $element->get('name');
			$id = $element->get('id');
			$value = $element->get('value');
			$type = $element->vars['block_prefixes'][2];
			switch ($type) {
				case 'text': {
					$result = "\n<input type=\"$type\" name=\"$name\" value=\"$value\" />";
				} break;
				case 'choice': {
					$expanded = $element->get('expanded');
					$multiple = $element->get('multiple');
					if ($expanded && $multiple == false) {
						$result = "\n<input type=\"checkbox\" name=\"$name\" />";
					}
				} break;
				default: throw new \Exception("Unknown type of input '$type'.");
			}
			return $result;
		}

		public function label($element) {
			$id = $element->get('id');
			$label = $element->get('label');
			return "<label for=\"$id\">$label:</label>";
		}

		public function render($element) {
			$name = $element->get('name');
			$id = $element->get('id');
			$value = $element->get('value');
			$type = $element->vars['block_prefixes'][2];
			$data = $this->get('data');
			switch ($type) {
				case 'text': {
					$result = $this->label($element);
					$result .= "\n<input type=\"$type\" name=\"$name\" value=\"$value\" />";
				} break;
				case 'choice': {
					$choices = $element->get('choices');
					$expanded = $element->get('expanded');
					$multiple = $element->get('multiple');
					$label = $element->get('label');
					$input = $this->input($element);
					if ($expanded && $multiple == false) {
						foreach ($choices as $choice) {
							// d($choice);
						}
						d($choices, $element);
						//$result = "<label for=\"$id\">$label: $input</label>";
					}
				} break;
				case 'hidden': {
					$result = "\n<input type=\"hidden\" name=\"$name\" value=\"$value\" />";
				} break;
				default: throw new \Exception("Unknown how to render this type '$type'.", 1);
			}
			return $result;
		}
	}
	return new FormViewRender();
});


$controller->get('/symform', function (Request $request) use ($app) {
	// some default data for when the form is displayed the first time
	$data = array(
		'name' => 'Your name',
		'email' => 'Your email',
	);

	$form = $app['form.factory']->createBuilder('form', $data)
		->add('name', 'text', array('label' => 'Name'))
		->add('email', 'text', array('label' => 'Email'))
		->add('gender', 'choice', array(
			'label' => 'Gender',
			'choices' => array(1 => 'male', 2 => 'female'),
			'expanded' => true,
		))
		->getForm();

	if ('POST' == $request->getMethod()) {
		$form->bind($request);

		if ($form->isValid()) {
			$data = $form->getData();

			// do something with the data

			// redirect somewhere
		}
	}

	return $app['twig']->render('test/symform.twig', array('form' => $form->createView()));

	return $app['view']->render(array(
		'form' => $form->createView()
	));
});

$controller->get('/{name}', function (Application $app) { 
	d($app['request_info']);
	$request = $app['request'];
	$view = $app['view'];
	$name = $request->get('name');
	$view->name = $name;
	return $view->render();
});



return $controller;