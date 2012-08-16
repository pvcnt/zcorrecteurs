<?php if ($user->isAbsent()): ?>
	<div class="alert alert-error">
		<?php echo htmlspecialchars($user->getUsername()) ?> est actuellement absent 
		<?php if (!$user->getAbsenceEndDate()): ?>
		pour une durée indéterminée.
		<?php else: ?>
		et revient <strong><?php echo dateformat($user->getAbsenceEndDate(), DATE, MINUSCULE) ?></strong>.
		<?php endif ?>
	</div>
	<?php if ($user->hasAbsenceReason()): ?>
		<p><?php echo $view['messages']->parse($user->getAbsenceReason()) ?></p>
		<hr />
	<?php endif ?>
<?php endif ?>
<?php if ($user->hasBiography()): ?>
	<?php echo $view['messages']->parse($user->getBiography(), array('core.anchor_prefix' => 'bio')) ?>
<?php else: ?>
	<div class="alert alert-info">
		<?php echo htmlspecialchars($user->getUsername()) ?> n’a pas encore écrit sa présentation personnelle.
	</div>
<?php endif ?>

<?php if ($user->hasSignature()): ?>
	<hr style="margin-bottom: 10px;" />
	<div style="background-color: #FCF8E3; padding: 10px; border-radius: 5px;">
		<?php echo $view['messages']->parse($user->getSignature()) ?>
	</div>
<?php endif ?>