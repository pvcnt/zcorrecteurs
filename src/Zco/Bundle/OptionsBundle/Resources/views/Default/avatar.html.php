<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoOptionsBundle::_tabs.html.php', array(
	'tab' => 'avatar', 
	'id' => $own ? null : $user->getId(),
)) ?>

<?php if ($own): ?>
<h1>
	Modifier mon avatar 
	<small>Changez la façon dont les autres utilisateurs vous voient.</small>
</h1>
<?php else: ?>
<h1>Modifier l’avatar de <?php echo htmlspecialchars($user->getUsername()) ?></h1>

<div class="alert alert-error">
	Vous êtes en train de modifier l’avatar de 
	<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>"><?php echo htmlspecialchars($user->getUsername()) ?></a>.
</div>
<?php endif ?>

<form action="" method="post" class="form-horizontal" enctype="multipart/form-data">
	<div class="control-group">
		<label class="control-label">Avatar actuel</label>
		<div class="controls">
			<div class="avatar" style="float: left;">
				<img src="<?php echo htmlspecialchars($user->getAvatar()) ?>" alt="Avatar actuel" />
			</div>
			<div style="margin-left: 110px">
				<p>
					<?php if ($user->hasAvatar()): ?>
					<strong>Cet avatar provient de votre ordinateur.</strong>
					<?php else: ?>
					<em>Cet avatar est l’image par défaut.</em>
					<?php endif ?>
				</p>
				<p>
					Vous pouvez envoyer une image mesurant jusqu'à 100x100 pixels 
					aux formats PNG, JPEG ou GIF.<br />
					Si celle-ci est trop grande elle sera automatiquement 
					redimensionnée à la taille maximale autorisée.
				</p>
			</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			Choisir un nouvel avatar
		</label>
		<div class="controls">
			<input type="file" name="avatar" />
		</div>
	</div>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" name="submit" value="Modifier <?php echo $own ? 'mon' : 'son' ?> avatar" />
		<?php if ($user->hasAvatar()): ?>
		<input type="submit" class="btn" name="delete" value="Supprimer <?php echo $own ? 'mon' : 'son' ?> avatar" />
		<?php endif ?>
	</div>
</form>
