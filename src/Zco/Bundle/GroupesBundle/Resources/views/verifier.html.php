<?php $view->extend('::layouts/default.html.php') ?>

<h1>Vérifier les droits d'un groupe</h1>

<fieldset>
	<legend>Choix du groupe</legend>
	<form method="post" action="">
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
		</select>
		<input type="submit" value="&Eacute;diter" />
	</form>
</fieldset>

<?php if(!empty($InfosGroupe)){ ?>
<h2>Attribution des droits</h2>

<fieldset>
	<legend>Saut rapide</legend>
	<label for="cat">Catégorie : </label>
	<select name="cat" id="cat" onchange="document.location = '#c' + this.value; this.value = 0;">
		<option value="0" class="opt_titre" selected="selected">Sélectionnez</option>
		<?php echo GetListeCategories(); ?>
	</select>
</fieldset>

<?php
$cat = null;
$droit = null;
$nb = 0;
foreach($Droits as $d)
{
	if($droit != $d['droit_id'])
	{
		$droit = $d['droit_id'];
		if($cat != $d['cat_id'])
		{
			if($nb != 0) echo '</div>';
			if($d['cat_niveau'] <= 1 && $nb) echo '<hr style="width: 45%;" />';
			echo '<div class="UI_box" style="width: 45%; margin-left: '.($d['cat_niveau'] > 0 ? 50 * ($d['cat_niveau'] - 1) : 0).'px;" id="c'.$d['cat_id'].'"><h2>'.htmlspecialchars($d['cat_nom']).' <a href="#header"><img src="/bundles/zcogroupes/img/haut.png" alt="Haut" /></a></h2>';
			$cat = $d['cat_id'];
			$nb++;
		}

		//Si on doit afficher le droit
		if(($d['droit_choix_categorie'] && $d['cat_niveau'] > 1) || (!$d['droit_choix_categorie'] && $d['cat_niveau'] <= 1))
		{
			if (isset($_GET['assigned_only']) && !$d['droit_choix_binaire'] || !$d['gd_valeur']) {
				continue;
			}
?>
<a href="droits-<?php echo $_GET['id']; ?>-<?php echo $d['droit_id']; ?>.html" title="&Eacute;diter ce droit"><img src="/img/editer.png" alt="&Eacute;diter" /></a>
<span class="<?php if($d['droit_choix_binaire']) echo ($d['gd_valeur']) ? 'vertf' : 'rouge'; ?>">
	<?php echo htmlspecialchars($d['droit_description']); ?>
</span>
<?php if(!$d['droit_choix_binaire']) echo ' : <strong>'.$d['gd_valeur'].'</strong>'; ?>
<br />
<?php
		}
	}
}
} ?>
