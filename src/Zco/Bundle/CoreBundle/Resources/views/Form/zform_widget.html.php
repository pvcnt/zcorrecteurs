<div class="zform">
	<div class="zform-wrapper">
		<textarea <?php echo $view['form']->renderBlock('attributes') ?>><?php echo $view->escape($value) ?></textarea>
	</div>
	<div class="zform-preview-area<?php if (empty($value)) echo ' zform-invisible' ?>">
		<?php echo $view['messages']->parse($value) ?>
	</div>
</div>

<?php $view['javelin']->initBehavior('zform', array('id' => $id)) ?>
<?php $view['javelin']->initBehavior('resizable-textarea', array('id' => $id)) ?>
<?php $view['javelin']->initBehavior('squeezebox', array('selector' => '.zform-squeezebox-link', 'options' => array('handler' => 'iframe'))) ?>
<?php $view['javelin']->initBehavior('twipsy', array('selector' => '.zform-tool-button a')) ?>