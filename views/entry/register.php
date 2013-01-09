
<h1>Регистрация</h1>

<div class="Row">

	<div class="Column60">
	<?php 
	echo $form->open();
	echo $form->errors();
	?>
	
	<fieldset>
		<?php
			echo $form->label('Имя', 'name', array('class' => 'Required'));
			echo $form->textbox('name');
		?>
	</fieldset>
	
	<fieldset>
		<?php
			echo $form->label('Email', 'email', array('class' => 'Required'));
			echo $form->textbox('email');
		?>
	</fieldset>
	
	<?php 
	echo $form->button('register', array('value' => 'Зарегистрироваться'));
	echo $form->close();
	?>
	</div>

	<div class="Column40">
		<div class="Box">
			<h4>Или...</h4>
		</div>
	</div>

</div>