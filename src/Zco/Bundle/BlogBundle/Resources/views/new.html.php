<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter un billet</h1>

<?php if(verifier('blog_choisir_etat')){ ?>
<p>
	Rédigez dans l'espace ci-dessous votre billet. Donnez-lui un titre et
	remplissez-en le contenu. N'oubliez pas qu'il ne sera pas publié tout de
	suite, vous pourrez donc le modifier autant de fois que nécessaire avant
	de le mettre en ligne.<br /><br />

	<em>
		Note : vous pourrez ajouter des auteurs à votre billet après sa
		création, pour permettre à d'autres membres de contribuer à sa
		rédaction.
	</em>
</p>
<?php } else{ ?>
<p>
	Rédigez dans l'espace ci-dessous votre billet (s'il est validé par un
	administrateur, il apparaîtra sur la page d'accueil du site !). Donnez-lui
	un titre et remplissez-en le contenu. Le billet ne sera pas immédiatement
	envoyé, mais restera à l'état de brouillon, accessible dans l'espace
	« Mes billets ». Vous pourrez le modifier autant de fois que nécessaire,
	avant de le soumettre à la validation des
	administrateurs.<br /><br />

	<em>
		Note : vous pourrez ajouter des auteurs à votre billet après sa
		création, pour permettre à d'autres membres de contribuer à sa
		rédaction.
	</em>
</p>

<div class="rmq attention">
	N'oubliez pas de bien vous relire avant d'envoyer la proposition. En effet,
	si le billet contient quelques erreurs, nous pourrons nous en charger,
	mais si ces dernières sont trop fréquentes, nous risquons de vous le refuser
	(ce qui serait dommage).<br />

	Quoi qu'il en soit (billet accepté ou refusé), vous serez averti par mail à
	l'adresse qui se trouve dans votre profil (indiquée à l'inscription et
	modifiable dans « Mes options »).
</div>
<?php } ?>

<form action="" method="post">
	<div class="send">
		<input type="submit" name="submit" value="Sauvegarder" accesskey="s" tabindex="6" />
	</div>

	<fieldset>
		<legend>Modifier les informations</legend>
		<label for="titre">Titre (obligatoire) : </label>
		<input type="text" name="titre" id="titre" size="35" tabindex="1" /><br />

		<label for="sous_titre">Sous-titre : </label>
		<input type="text" name="sous_titre" id="sous_titre" size="35" tabindex="2" /><br />

		<label for="categorie">Catégorie : </label>
		<select name="categorie" id="categorie" style="min-width: 150px;" tabindex="3">
			<?php foreach($Categories as $cat){ ?>
			<option value="<?php echo $cat['cat_id']; ?>">
				<?php echo htmlspecialchars($cat['cat_nom']); ?>
			</option>
			<?php }	?>
		</select>
	</fieldset>

	<fieldset>
		<legend>Modifier le corps du billet</legend>
		<label for="intro">Introduction (obligatoire) : </label>
		<?php echo $view->render('::zform.html.php', array('id' => 'intro', 'tabindex' => 4, 'upload_utiliser_element' => true)) ?>
		<br /><br />

		<label for="texte">Contenu (obligatoire) : </label>
		<?php echo $view->render('::zform.html.php', array('id' => 'texte', 'tabindex' => 5)) ?>
	</fieldset>

	<div class="send">
		<input type="submit" name="submit" value="Sauvegarder" accesskey="s" tabindex="7" />
	</div>
</form>
