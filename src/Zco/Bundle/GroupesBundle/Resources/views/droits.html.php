<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modification des droits d'un groupe</h1>

<fieldset>
	<legend>Choix du groupe et du droit</legend>
	<form method="get" action="">
		<label for="id">Groupe : </label>
		<select name="id" id="id">
			<?php foreach($ListerGroupes as $g)
			{
				$selected = '';
				if($g['groupe_id'] == $_GET['id'])
				{
					$selected = ' selected="selected"';
				}
				echo '<option value="'.$g['groupe_id'].'" style="color: '.$g['groupe_class'].'"'.$selected.'>'.htmlspecialchars($g['groupe_nom']).'</option>';
			}
			?>
		</select><br />
		<label for="id2">Droit : </label>
		<select name="id2" id="id2">
			<?php
			$current = 0;
			$i = 0;
			foreach($ListerDroits as $d)
			{
				if($current != $d['cat_id'])
				{
					$current = $d['cat_id'];
					if($i != 0)
						echo '</optgroup>';
					echo '<optgroup label="'.htmlspecialchars($d['cat_nom']).'">';
					$i++;
				}
			?>
			<option value="<?php echo $d['droit_id']; ?>"<?php if(!empty($_GET['id2']) && $d['droit_id'] == $_GET['id2']) echo ' selected="selected"'; ?>><?php echo htmlspecialchars($d['droit_description']); ?></option>
			<?php } echo '</optgroup>' ?>
		</select><br />

		<div class="send">
			<input type="submit" value="&Eacute;diter" />
		</div>
	</form>
</fieldset>

<?php if(!empty($InfosDroit)){ ?>
<fieldset>
	<legend>&Eacute;dition du droit : <?php echo htmlspecialchars($InfosDroit['droit_description']); ?></legend>

	<form method="post" action="" id="droits">
		<?php if(!$InfosDroit['droit_choix_binaire']){ ?>
		<p class="centre">
			<label for="valeur" class="nofloat gras"><?php echo htmlspecialchars($InfosDroit['droit_description']); ?> : </label>
			<input type="text" size="4" name="valeur" id="valeur" value="<?php if(!empty($ValeurDroit)) echo $ValeurNumerique; ?>" /><br />
		</p>
		<?php } if($InfosDroit['droit_choix_binaire'] && !$InfosDroit['droit_choix_categorie']){ ?>
		<p class="centre">
			<label for="valeur" class="nofloat gras"><?php echo htmlspecialchars($InfosDroit['droit_description']); ?> : </label>
			<input type="checkbox" name="valeur" id="valeur"<?php if(!empty($ValeurDroit) && $ValeurDroit['gd_valeur'] == 1) echo ' checked="checked"'; ?> /><br />
		</p>
		<?php } if($InfosDroit['droit_choix_categorie']){ ?>
		<p><em>Vous pouvez sélectionner plusieurs catégories en maintenant CTRL ou MAJ enfoncée.</em></p>
		<label for="cat">Catégorie(s) : </label>
		<select name="cat[]" id="cat" size="<?php if(count($ListerEnfants) > 15) echo '20'; else echo '10'; ?>" multiple="multiple" style="min-width: 300px;">
			<?php
			foreach($ListerEnfants as $e)
			{
				$marqueur = '';
				$selected = '';

				for($i = 1 ; $i < $e['cat_niveau'] ; $i++)
					$marqueur .= '.....';
				foreach($ValeurDroit as $v)
				{
					if($v['gd_id_categorie'] == $e['cat_id'] && $v['gd_valeur'] > 0)
						$selected = ' selected="selected"';
				}
			?>
			<option value="<?php echo $e['cat_id']; ?>"<?php echo $selected; ?>><?php echo $marqueur.' '.htmlspecialchars($e['cat_nom']); ?></option>
			<?php } ?>
		</select>
		<?php } ?>

		<?php if(!empty($InfosDroit['droit_description_longue'])){ ?>
		<span class="citation">Indication supplémentaire des développeurs :</span>
		<div class="citation2">
			<?php echo $view['messages']->parse($InfosDroit['droit_description_longue']); ?>
		</div>
		<?php } ?>

		<div class="send">
			<input type="submit" value="Changer les droits" name="modifier" />
		</div>
	</form>
</fieldset>
<?php } ?>
