<!DOCTYPE html>
<html>
<head>
	<?php $this->renderAsset('head'); ?>
</head>
<body>
	<?php $this->dispatch('before.body'); ?>
	<?php $this->renderAsset('content'); ?>
	<?php $this->dispatch('after.body'); ?>
</body>
</html>