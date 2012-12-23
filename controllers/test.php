<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$controller = $app['controllers_factory'];

$controller->match('/', function (Application $app) { 
	return 'Test Index.';
});

$controller->match('redbean/tag', function(Application $app) {
	$book = R::dispense('book');
	$book->title = 'Gifted Programmers';
    $book->author = 'Charles Xavier';
    $book->price = rand();
    $book->IsDeleted = true;
    $book->id = -1;
    $id = R::store($book);
    // $tag = R::tag($book, array('topsecret', 'mi6'));
    // R::addTags( $book, array('funny','hilarious') );
    // d(R::taggedAll('book', array('funny','hilarious') ));
	d($id);
});

$controller->match('redbean/model', function(Application $app) {
	class Model_Bandmember extends RedBean_SimpleModel {
	}
    $bandmember = R::dispense('bandmember');
    d($bandmember);
    $bandmember->name = 'Fatz Waller';
    $id = R::store($bandmember);
    $bandmember = R::load('bandmember',$id);
    R::trash($bandmember);
});

$controller->match('redbean/user', function(Application $app) {
	$user = R::dispense('user');
	$user->name = 'Joe';
	$user->email = 'joe@mail.ru';
	$user->invitedBy = 52000;
    $id = R::store($user);
    $user = R::load('user', $id);
    d($user);
	d($id, $user, R::count('user', 'id = ?', array(0 => 4)));
});

$controller->match('redbean/shared/sel', function(Application $app) {
	$user = R::load('user', 3);
	$roles = $user->sharedRole;
	d($roles, $user);
});

$controller->match('redbean/shared', function(Application $app) {
	$user = R::dispense('user');
	$role = R::dispense('role');
	$role->name = 'Administrator';
	$user->sharedRole[] = $role;
	$id = R::store($user);
	d($id);
});

$controller->match('redbean/trash/{id}', function(Application $app, $id) {
	$user = R::load('user', $id);
	R::trash($user);
});



$controller->match('redbean/load/{id}', function(Application $app, $id) {
	// $book = R::load('book', $id);
	$books = R::batch('book', array($id, $id + 1));
});


$controller->match('/gdnform', function (Application $app) {
	$Form = $app['form'];
	if ($Form->IsPostback()) {
		$FormValues = $Form->FormValues();
		d('$FormValues', $FormValues);
	} else {
		Gdn::Session()->TransientKey(Gdn::Session()->TransientKey());
	}
	ob_start();
	echo $Form->Open();
	echo $Form->Label('Name');
	echo $Form->TextBox('Name', 'Name');
	echo $Form->Button('Save');
	echo $Form->Close();
	$String = ob_get_clean();
	// d($String);
	return $String;
});

$controller->get('/form', function (Application $app) {
	$form = new Dummy();
	$form->setRequest($app['request']);
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