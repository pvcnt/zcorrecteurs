<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoOptionsBundle::_tabs.html.php', array(
	'tab' => 'absence', 
	'id' => $own ? null : $user->getId(),
)) ?>

<h1>
	Indiquer une période d'absence 
	<?php if ($own): ?>
		<small>Signalez aux autres utilisateurs lorsque vous vous absentez.</small>
	<?php else: ?>
		de <?php echo htmlspecialchars($user->getUsername()) ?>
	<?php endif ?>
</h1>

<p>
	Vous pouvez indiquer que vous êtes absent. Cette option fera apparaitre dans tous vos messages
	à côté de votre pseudo une icône <img src="/bundles/zcooptions/img/gerer_absence.png" alt="Absent" />
	et la raison de votre absence apparaîtra sur votre profil.
</p>

<?php if (!$user->isAbsent() && $user->hasAbsenceStartDate()): ?>
<div class="alert">
	Une absence est actuellement définie pour  
	<strong><?php echo dateformat($user->getAbsenceStartDate(), MINUSCULE, DATE) ?></strong>
	et prendra fin 
	<?php echo dateformat($user->getAbsenceEndDate(), MINUSCULE, DATE) ?>.
</div>
<?php elseif ($user->isAbsent()): ?>
<div class="alert">
	Vous êtes actuellement absent ! Vous reviendrez parmi nous 
	<strong><?php echo dateformat($user->getAbsenceEndDate(), MINUSCULE, DATE) ?></strong>.
	<?php if ($user->hasAbsenceReason()): ?>
		<?php echo $view['messages']->parse('<citation nom="Motif de l\'absence">'.$user->getAbsenceReason().'</citation>') ?>
	<?php endif ?>
</div>
<?php endif ?>

<form method="post" action="" class="form-horizontal">
	<?php echo $view['form']->errors($form) ?>

	<?php echo $view['form']->row($form['absence_start_date']) ?>
	<?php echo $view['form']->row($form['absence_end_date']) ?>
	<?php echo $view['form']->row($form['absence_reason']) ?>

	<?php echo $view['form']->rest($form) ?>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" name="submit" value="Je m’en vais !" />
		<?php if ($user->isAbsent() || $user->hasAbsenceStartDate()): ?>
		<input type="submit" class="btn" name="delete" value="Supprimer mon absence" />
		<?php endif ?>
	</div>
</form>