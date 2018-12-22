<?php if ($user->hasBiography()): ?>
	<?php echo $view['messages']->parse($user->getBiography(), array('core.anchor_prefix' => 'bio')) ?>
	<div class="cleaner">&nbsp;</div>
<?php else: ?>
	<div class="alert alert-info">
		<?php echo htmlspecialchars($user->getUsername()) ?> n’a pas encore écrit sa présentation personnelle.
	</div>
<?php endif ?>

<?php if ($user->hasSignature()): ?>
	<hr style="margin-bottom: 10px;" />
	<div style="background-color: #FCF8E3; padding: 10px; border-radius: 5px;">
		<?php echo $view['messages']->parse($user->getSignature()) ?>
		<div class="cleaner">&nbsp;</div>
	</div>
<?php endif ?>