<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Supprimer un quiz</h1>

<form method="post" action="">
		<p>
			Êtes-vous sûr de vouloir supprimer ce quiz intitulé
			<strong><?php echo htmlspecialchars($InfosQuiz['nom']); ?></strong> ?
			Toutes les questions contenues dedans seront également supprimées, ainsi que les scores associés.
		</p>

		<p class="center">
			<input type="submit" class="btn btn-primary" value="Oui" name="confirmer" />
            <a class="btn" href="gestion.html">Non</a>
		</p>
</form>