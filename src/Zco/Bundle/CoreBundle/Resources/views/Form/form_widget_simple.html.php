<input type="<?php echo isset($type) ? $view->escape($type) : 'text' ?>" <?php echo $view['form']->block($form, 'widget_attributes') ?><?php if (!empty($value) || is_numeric($value)): ?> value="<?php echo $view->escape($value) ?>"<?php endif ?> />
<?php if (isset($help)): ?>
    <p class="help-block"><?php echo $view->escape($help) ?></p>
<?php endif ?>
