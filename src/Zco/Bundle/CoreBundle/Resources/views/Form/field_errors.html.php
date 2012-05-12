<?php if ($errors): ?>
<div class="help-block">
	<?php foreach ($errors as $i => $error): ?>
		<?php if ($i > 0): ?><br /><?php endif ?>
		<?php echo $view['translator']->trans($error->getMessageTemplate(), $error->getMessageParameters(), 'validators') ?>
	<?php endforeach ?>
</div>
<?php endif ?>
