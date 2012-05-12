<?php $view->extend('::layouts/default.html.php') ?>

<h1>Proposer un billet</h1>

<p>Une fois votre billet rédigé, vous pouvez le soumettre aux administrateurs,
afin qu'il apparaisse sur la page d'accueil et soit visible par l'ensemble des
visiteurs. Nous vous invitons à nous laisser vos éventuels commentaires dans le champ
de texte ci-dessous (ils sont uniquement à destination de la personne qui traitera
votre demande et ne seront pas publiés).</p>

<fieldset>
	<legend>Proposer un billet</legend>
	<form method="post" action="">
		<div class="send">
			<input type="submit" name="submit" value="Proposer" />
		</div>

		<label for="texte">Commentaire destiné à l'administrateur :</label>
		<?php echo $view->render('::zform.html.php', array(
			'upload_utiliser_element' => true,
			'upload_id_formulaire' => $_GET['id'],
		)) ?>

		<div class="send">
			<input type="submit" name="submit" value="Proposer" />
		</div>
	</form>
</fieldset>


