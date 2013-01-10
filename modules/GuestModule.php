<?php

class GuestModule extends Module {

	public function assetTarget() {
		return 'panel';
	}

	public function toString() {
		ob_start();
		?>
<div class="Box GuestBox">
<h4>Здравствуйте.</h4>

<p>Вы можете зарегистрироваться.</p>
<?php $this->dispatch('GuestModule'); ?>
</div>
		<?php
		$string = ob_get_contents();
		ob_end_clean();
		return $string;
	}

}