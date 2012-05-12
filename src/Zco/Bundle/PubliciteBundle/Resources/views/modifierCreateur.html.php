<?php $view->extend('::layouts/default.html.php') ?>

<h1>Changer le créateur de la campagne</h1>
<h2><?php echo htmlspecialchars($campagne['nom']) ?></h2>

<p>
	Seul le créateur d'une campagne a accès aux options de modification du
	ciblage, au lancement, à la mise en pause d'une campagne et à la
	visualisation des statistiques.
</p>

<?php if ($campagne['utilisateur_id'] == $_SESSION['id']){ ?>
<p class="gras">
	Si vous changez le créateur de la campagne, vous perdrez tout
	contrôle sur votre campagne ! - <a href="campagne-<?php echo $campagne['id'] ?>.html">Annuler le changement</a>
</p>
<?php } ?>

<form method="post" action="">
	<fieldset>
		<legend>Choisir un nouveau reponsable</legend>

		<label for="pseudo">Nouveau créateur :</label>
		<input type="text" name="pseudo" id="pseudo" size="40" value="<?php echo htmlspecialchars($campagne->Utilisateur['pseudo']) ?>" />
		<input type="submit" value="Envoyer" />
		
		<?php $view['javelin']->initBehavior('autocomplete', array(
		    'id' => 'pseudo', 
		    'callback' => $view['router']->generate('zco_user_api_searchUsername'),
		)) ?>
	</fieldset>
</form>