<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoOptionsBundle::_tabs.html.php', array(
	'tab' => 'profile', 
	'id' => $own ? null : $user->getId(),
)) ?>

<?php if ($own): ?>
<h1>
	Modifier mon profil
	<small>Changez les informations apparaissant sur votre page de profil.</small>
</h1>

<div class="alert alert-info">
	<strong>Note :</strong> pour changer votre pseudonyme sur ce site, vous devez
	<a href="<?php echo $view['router']->path('zco_user_newPseudo') ?>">procéder à une demande de changement de pseudo</a>.
</div>
<?php else: ?>
<h1>Modifier le profil de <?php echo htmlspecialchars($user->getUsername()) ?></h1>

<div class="alert alert-error">
	Vous êtes en train de modifier le profil de 
	<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>"><?php echo htmlspecialchars($user->getUsername()) ?></a>.
	Assurez-vous de ne pas renseigner d'information personnelle contre le gré du membre.
</div>
<?php endif ?>

<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
	<?php echo $view['form']->errors($form) ?>

	<fieldset>
		<legend>Paramétrage de <?php echo $own ? 'ma' : 'sa' ?> page de profil</legend>
		<?php echo $view['form']->row($form['job'], array('widget_attr' => array('class' => 'input-xxlarge'))) ?>
		<?php echo $view['form']->row($form['hobbies'], array('widget_attr' => array('class' => 'input-xxlarge'))) ?>

		<div class="control-group">
			<label class="control-label">Afficher…</label>
			<div class="controls">
				<?php echo $view['form']->row($form['email_displayed'], array('style' => 'checkbox')) ?>
				<?php echo $view['form']->row($form['country_displayed'], array('style' => 'checkbox')) ?>
			</div>
		</div>

		<?php echo $view['form']->row($form['biography'], array('help' => 'Ce champ constitue le cœur de la page de profil, n\'hésitez surtout pas à le remplir comme bon vous semble !')) ?>
		<?php echo $view['form']->row($form['website'], array('widget_attr' => array('class' => 'input-xxlarge'))) ?>
		<?php echo $view['form']->row($form['twitter'], array('prepend' => '@')) ?>
		<?php echo $view['form']->row($form['birth_date']) ?>
        <?php echo $view['form']->row($form['sexe']) ?>
	</fieldset>

	<fieldset>
		<legend><?php echo $own ? 'Mon' : 'Son' ?> apparence sur le site</legend>
		<?php echo $view['form']->row($form['citation'], array('help' => 'La citation sera affichée au-dessus de votre avatar', 'widget_attr' => array('class' => 'input-xxlarge'))) ?>
		<?php echo $view['form']->row($form['signature']) ?>
	</fieldset>

	<?php echo $view['form']->rest($form) ?>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Mettre à jour mon profil"/>
	</div>
</form>
