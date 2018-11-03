<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1><?php echo htmlspecialchars($InfosGroupe['groupe_nom']) ?></h1>

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
			echo '<div class="box" style="width: 45%; margin-left: '.($d['cat_niveau'] > 0 ? 50 * ($d['cat_niveau'] - 1) : 0).'px;"><h2>'.htmlspecialchars($d['cat_nom']).'</h2>';
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
<a href="<?php echo $view['router']->path('zco_groups_changeCredentials', ['id' => $InfosGroupe['groupe_id'], 'credential' => $d['droit_id']]) ?>" title="&Eacute;diter ce droit"><img src="/img/editer.png" alt="&Eacute;diter" /></a>
<span style="color:<?php if($d['droit_choix_binaire']) echo ($d['gd_valeur']) ? 'green' : 'red'; ?>;">
	<?php echo htmlspecialchars($d['droit_description']); ?>
</span>
<?php if(!$d['droit_choix_binaire']) echo ' : <strong>'.$d['gd_valeur'].'</strong>'; ?>
<br />
<?php
		}
	}
} ?>
