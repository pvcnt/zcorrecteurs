<?php if ($compound): ?>
    <div <?php echo $view['form']->block($form, 'container_attributes') ?>>
        <?php echo $view['form']->errors($form) ?>
        <?php echo $view['form']->block($form, 'form_rows') ?>
        <?php echo $view['form']->rest($form) ?>
    </div>
<?php else: ?>
    <input
            type="<?php echo isset($type) ? $view->escape($type) : "text" ?>"
            value="<?php echo $view->escape($value) ?>"
        <?php echo $view['form']->block($form, 'attributes') ?>
    />

    <?php if (isset($help)): ?>
        <p class="help-block"><?php echo $view->escape($help) ?></p>
    <?php endif ?>
<?php endif ?>
