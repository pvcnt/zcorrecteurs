<?php $view->extend('::layouts/default.html.php') ?>

<h1>Changer un membre de groupe</h1>

<fieldset>
	<legend>Sélectionner le membre</legend>
	<form method="post" action="">
		<label for="pseudo">Pseudo : </label>
		<input type="text" name="pseudo" id="pseudo" value="<?php echo $pseudo; ?>" />
		<input type="submit" value="Envoyer" />
		
		<?php $view['javelin']->initBehavior('autocomplete', array(
		    'id' => 'pseudo', 
		    'callback' => $view['router']->generate('zco_user_api_searchUsername'),
		)) ?>
	</form>
</fieldset>

<?php if(!empty($ListerGroupes)){ ?>
<fieldset>
	<legend>Sélectionner le groupe de destination</legend>
	<form method="post" action="">
		<label for="groupe">Groupe principal : </label>
		<select id="groupe" name="groupe">
		<?php foreach($ListerGroupes as $g)
		{
			if($g['groupe_id'] != GROUPE_VISITEURS)
			{
				if($g['groupe_id'] == $InfosUtilisateur['utilisateur_id_groupe'])
					$selected = ' selected="selected"';
				else
					$selected = '';
				echo '<option value="'.$g['groupe_id'].'" style="color: '.$g['groupe_class'].';"'.$selected.'>'.htmlspecialchars($g['groupe_nom']).' ('.$g['groupe_effectifs'].')</option>';
			}
		}
		?>
		</select>

		<input type="submit" value="Envoyer" />
	</form>
</fieldset>
<?php } ?>

<?php if(!empty($ListerGroupesSecondaires)){ ?>
<fieldset>
	<legend>Sélectionner les groupes secondaires</legend>
	<form method="post" action="">
		<select id="groupes"
		        name="groupes_secondaires[]"
		        multiple="multiple"
		        size="<?php echo count($ListerGroupesSecondaires) ?>">
			<?php
			foreach($ListerGroupesSecondaires as $g)
			{
				if($g['groupe_id'] != GROUPE_VISITEURS)
				{
					if(in_array($g['groupe_id'], $GroupesSecondaires))
						$selected = ' selected="selected"';
					else
						$selected = '';
					echo '<option value="'.$g['groupe_id'].'" style="color: '.$g['groupe_class'].';"'.$selected.'>'.htmlspecialchars($g['groupe_nom']).' ('.$g['groupe_effectifs'].')</option>';
				}
			}
			?>
		</select>

		<input type="hidden" name="changement_groupes_secondaires" value="1" />
		<p><input type="submit" value="Envoyer" /></p>
	</form>
</fieldset>
<?php } ?>
