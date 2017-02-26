<?php $view->extend('::layouts/default.html.php') ?>

<h1>Éditer un auteur</h1>

<p>Vous pouvez maintenant éditer l'auteur <strong><a href="/membres/profil-<?php echo $InfosUtilisateur['utilisateur_id']; ?>-<?php echo rewrite($InfosUtilisateur['utilisateur_pseudo']); ?>.html"><?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']); ?></a></strong>, en lui attribuant un nouveau statut, ou en changeant de personne.</p>

<fieldset>
	<legend>Éditer un auteur</legend>
	<form action="" method="post">
		<label for="pseudo">Pseudo : </label>
		<input type="text" name="pseudo" id="pseudo" value="<?php echo $InfosUtilisateur['utilisateur_pseudo']; ?>" /><br />
		
		<?php $view['javelin']->initBehavior('autocomplete', array(
		    'id' => 'pseudo', 
		    'callback' => $view['router']->path('zco_user_api_searchUsername'),
		)) ?>
		
		<label for="statut">Statut : </label>
		<select name="statut" id="statut">
			<?php
			foreach($BlogStatuts as $cle=>$valeur)
			{
				echo '<option value="'.$cle.'"';
				if($InfosUtilisateur['auteur_statut'] == $cle) echo ' selected="selected"';
				echo '>'.htmlspecialchars($valeur).'</option>';
			}
			?>
		</select><br />
		<input type="submit" name="send" value="Envoyer" accesskey="s" />
	</form>
</fieldset>

