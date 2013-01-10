
<h1>Регистрация</h1>

<div class="Row">

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
	
	<fieldset>
		<?php
			echo $form->label('Пароль', 'password', array('class' => ''));
			echo $form->input('password', 'password');
		?>
	</fieldset>
	
	<?php 
	echo $form->button('register', array('value' => 'Зарегистрироваться'));
	echo $form->close();
	?>

<!-- 	<div class="Column40">
		<div class="Box ConnectWithBox">
			<h4>Или вы можете …</h4>
			<div class="ConnectBox">
			<a class="Button ConnectButton" href="<?php echo Url('/entry/connect/facebook') ?>">
				<span aria-hidden="true" data-icon="facebook"></span>
					Войти с помощью Facebook
			</a>
			</div>
			
			<div class="ConnectBox">
			<a class="Button ConnectButton" href="<?php echo Url('/entry/connect/twitter') ?>">
				<span aria-hidden="true" data-icon="twitter"></span>
					Войти с помощью Twitter
			</a>
			</div>
		</div>
	</div> -->

</div>