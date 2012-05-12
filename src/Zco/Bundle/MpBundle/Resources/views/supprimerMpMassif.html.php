<?php $view->extend('::layouts/default.html.php') ?>

<h1>Suppression massive de MP</h1>

<fieldset>
	<legend>Confirmation de la suppression massive de MP</legend>
	<p>Confirmez-vous la suppression des MP sélectionnés ? <br />
	<ul>
		<?php
		$donnees = NomsMP($_POST['MP']);
		foreach($donnees as $donnee)
		{
			echo '<li><a href="lire-' . $donnee['mp_id'] . '.html">'. htmlspecialchars($donnee['mp_titre']) . '</a><br />' .
			'<em>' . htmlspecialchars($donnee['mp_sous_titre']) . '</em></li>';
		}
		?>
	</ul>
	</p>

	<p>La suppression est irréversible.</p>

	<form action="" method="post">
		<input type="hidden" name="action" value="supprimer" />
		<?php
		foreach($_POST['MP'] as $valeur)
		{
			echo '<input type="hidden" name="MP[]" value="'.$valeur.'" />';
		}
		?>
		<div class="centre">
			<input type="submit" name="confirmation" value="Oui" />
			<input type="submit" name="annuler" value="Non" />
		</div>
	</form>
</fieldset>
