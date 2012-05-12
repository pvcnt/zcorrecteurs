<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Modifier le pourcentage d'un membre</h1>

<p>
	Vous vous apprêtez à changer le niveau d'avertissement d'un membre. Vous 
	devez pour cela indiquer une raison et indiquer le changement. Notez que 
	ce changement peut être positif ou négatif. Si vous indiquez 0 %, ce sera 
	une simple note sur le profil du membre.
</p>

<?php if ($user): ?>
<p>
	Vous vous apprêtez à modifier le pourcentage du membre suivant : 
	<strong><a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>"><?php echo htmlspecialchars($user->getUsername()) ?></a></strong>.
</p>
<?php endif ?>

<form method="post" action="<?php echo $view['router']->generate('zco_user_admin_warn', array('id' => $user ? $user->getId() : null)) ?>" class="form-horizontal">
	<?php echo $view['form']->errors($form) ?>
	<?php if (isset($form['User'])): ?>
		<?php echo $view['form']->row($form['User']) ?>
	<?php endif ?>
	<?php echo $view['form']->row($form['percentage'], array('help' => 'Une valeur négative aura pour effet de diminuer le pourcentage du membre.')) ?>
	<?php echo $view['form']->row($form['link']) ?>
	<?php echo $view['form']->row($form['reason'], array('help' => 'Si le champ est laissé vide, aucun message ne sera envoyé au membre.')) ?>
	<?php echo $view['form']->row($form['admin_reason']) ?>
	<?php echo $view['form']->rest($form) ?>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Envoyer" />
	</div>
</form>