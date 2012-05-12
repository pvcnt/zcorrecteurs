<div class="control-group<?php if (count($errors)) echo ' error' ?>">
	<?php echo $view['form']->label($form, isset($label) ? $label : null) ?>
	<div class="controls">
    	<?php echo $view['form']->widget($form, array('help' => isset($help) ? $help : null)) ?>
		<?php echo $view['form']->errors($form) ?>
	</div>
</div>