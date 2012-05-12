<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Modifier le titre</h1>

<form action="<?php echo $view['router']->generate('zco_user_editTitle', array('id' => $user->getId())) ?>" method="post" class="form-horizontal">
	<div class="control-group">
		<label for="user-title" class="control-label">Nouveau titre</label>
		<div class="controls">
			<input type="text" name="user_title" id="user-title" value="<?php echo htmlspecialchars($user->getTitle()) ?>" />
			<p class="help-block">Le titre est généralement affiché sous le pseudonyme du membre.</p>
		</div>
	</div>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" name="submit" value="Envoyer" />
	</div>
</form>
