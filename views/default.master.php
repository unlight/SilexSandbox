<!DOCTYPE html>
<html>
<head>
	<?php $this->renderAsset('head') ?>
</head>
<body id="<?php echo $app['body.identifier'] ?>" class="<?php echo $app['body.class'] ?>">
	<?php 
		// $app['event']->setArgument('class', 'Dummy');
		$this->dispatch('before.body') ;
		// echo $app['event']['class'];
	?>
	<div id="Frame">
	<div class="Head" id="Head">
		<div class="Row">
			<strong class="SiteTitle"></strong>
			 <span class="Logo">&#0160;
			 </span>
				<?php 
					//$this->Menu->HtmlId = 'SiteMenu';
					// $this->Menu->CssClass = 'SiteMenu';
					//$this->Menu->Render();
				?>
		</div>
	</div>
	<div id="Body">
		<div class="Row">
			<div class="PanelColumn" id="Panel">
				<?php 
				$this->renderAsset('panel');
				?>
			</div>
			<div class="ContentColumn">
				<?php
					//$Breadcrumbs = $this->Data('Breadcrumbs');
					//if ($Breadcrumbs && is_array($Breadcrumbs)) {
						//echo Wrap(Gdn_Theme::Breadcrumbs($Breadcrumbs, FALSE), 'div', array('class' => 'BreadcrumbsWrapper'));
					//}
				?>
				<div id="Content">
					<?php $this->renderAsset('content') ?>
				</div>
			</div>
		 </div>
	</div>

	<div id="Foot">
		<?php $this->renderAsset('foot') ?>
	</div>

</div>
<?php
	// $Class = $app['body.class'];
	// $this->EventArguments['Dummy'] =& $Class;
	$this->fireEvent('after_body');
	// echo $Class;
?>
</body>
</html>