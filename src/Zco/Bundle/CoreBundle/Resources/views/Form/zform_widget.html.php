<div class="zform" id="<?php echo $id ?>_zform">
	<div class="zform-wrapper">
		<textarea <?php echo $view['form']->block($form, 'attributes') ?>><?php echo $view->escape($value) ?></textarea>
	</div>
	<div class="zform-preview-area<?php if (empty($value)) echo ' zform-invisible' ?>">
		<?php echo $view['messages']->parse($value) ?>
	</div>
</div>
<?php $view['javelin']->initBehavior('zform', array('id' => $id)) ?>