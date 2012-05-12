<input
    type="<?php echo isset($type) ? $view->escape($type) : "text" ?>"
    value="<?php echo $view->escape($value) ?>"
    <?php echo $view['form']->renderBlock('attributes') ?>
/>

<?php if (isset($help)): ?>
    <p class="help-block"><?php echo $view->escape($help) ?></p>
<?php endif ?>