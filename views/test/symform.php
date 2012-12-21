<?php

?>

<h1>SymForm</h1>

<?php foreach ($form as $element) {
	echo $app['form.renderer']->render($element);
} ?>