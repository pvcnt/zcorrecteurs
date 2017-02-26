<?php if (isset($style) && 'checkbox' === $style): ?>
<label class="checkbox">
	<?php echo $view['form']->widget($form, array('help' => isset($help) ? $help : null, 'attr' => (isset($widget_attr) ? $widget_attr : array()))) ?>
	<?php echo $view['form']->label($form, isset($label) ? $label : null, array('style' => 'checkbox', 'attr' => (isset($label_attr) ? $label_attr : array()))) ?>
	<?php echo $view['form']->errors($form) ?>
</label>
<?php else: ?>
<div class="control-group<?php if (count($errors)) echo ' error' ?>">
	<?php echo $view['form']->label($form, isset($label) ? $label : null, array('attr' => (isset($label_attr) ? $label_attr : array()))) ?>
	<div class="controls">
		<?php if (isset($prepend) || isset($append)): ?>
		<div class="<?php if (isset($prepend)): ?>input-prepend<?php endif ?><?php if (isset($append)): ?>input-append<?php endif ?>">
		<?php endif ?>
		<?php if (isset($prepend)): ?><span class="add-on"><?php echo $prepend ?></span><?php endif ?><?php echo $view['form']->widget($form, array('help' => isset($help) ? $help : null, 'attr' => (isset($widget_attr) ? $widget_attr : array()))) ?><?php if (isset($append)): ?><span class="add-on"><?php echo $append ?></span><?php endif ?>
        <?php if (isset($prepend) || isset($append)): ?>
	    </div>
        <?php endif ?>
		<?php echo $view['form']->errors($form) ?>
	</div>
</div>
<?php endif ?>