<input type="checkbox"
    <?php echo $view['form']->block($form, 'attributes') ?>
    <?php if ($value): ?> value="<?php echo $view->escape($value) ?>"<?php endif ?>
    <?php if ($checked): ?> checked="checked"<?php endif ?>
/>

<?php if (isset($help)): ?>
    <span class="help_text"><?php echo $view->escape($help) ?></span>
<?php endif ?>