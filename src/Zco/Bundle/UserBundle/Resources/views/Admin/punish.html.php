<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Sanctionner un membre</h1>

<p>La sanction va changer le membre de groupe, pour une durée déterminée par vous. Elle prendra effet immédiatement et s'arrêtera automatiquement au bout du temps prescrit. Ces durées sont mises à jour toutes les nuits.</p>

<?php if ($user): ?>
<p>
	Vous vous apprêtez à sanctionner le membre suivant : 
	<strong><a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>"><?php echo htmlspecialchars($user->getUsername()) ?></a></strong>.
</p>
<?php endif ?>

<form method="post" action="<?php echo $view['router']->path('zco_user_admin_punish', array('id' => $user ? $user->getId() : null)) ?>" class="form-horizontal">
	<?php echo $view['form']->errors($form) ?>
	<?php if (isset($form['User'])): ?>
		<?php echo $view['form']->row($form['User']) ?>
	<?php endif ?>
	<?php echo $view['form']->row($form['Group']) ?>
	<?php echo $view['form']->row($form['duration'], array('help' => 'Indiquez 0 pour une sanction définitive.')) ?>
	<?php echo $view['form']->row($form['link']) ?>
	<?php echo $view['form']->row($form['reason'], array('help' => 'Si le champ est laissé vide, aucun message ne sera envoyé au membre.')) ?>
	<?php echo $view['form']->row($form['admin_reason']) ?>
	<?php echo $view['form']->rest($form) ?>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Envoyer" />
	</div>
</form>