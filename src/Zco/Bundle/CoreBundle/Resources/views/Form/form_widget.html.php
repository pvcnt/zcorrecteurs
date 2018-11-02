<?php if ($compound): ?>
    <div <?php echo $view['form']->block($form, 'widget_container_attributes') ?>>
        <?php if (!$form->parent && $errors): ?>
            <?php echo $view['form']->errors($form) ?>
        <?php endif ?>
        <?php echo $view['form']->block($form, 'form_rows') ?>
        <?php echo $view['form']->rest($form) ?>
    </div>
<?php else: ?>
    <?php echo $view['form']->block($form, 'form_widget_simple')?>
<?php endif ?>
