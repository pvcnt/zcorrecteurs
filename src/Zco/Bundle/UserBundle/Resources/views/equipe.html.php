<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des membres de l'équipe</h1>

<p class="UI_descr">
	Vous pouvez voir la liste des tous les membres qui s'occupent du site jour après jour, que ce soit en corrigeant vos
	écrits, en codant les nouvelles fonctionnalités ou en ajoutant du contenu au site.
</p>

<?php if (verifier('voir_adresse')){ ?>
<p class="gras centre">
	<a href="geolocalisation.html">Voir la géolocalisation de tous les membres de l'équipe</a>
</p><br />
<?php } ?>

<?php
$listeGroupes = '';
$done = array();
foreach($ListerGroupesEquipe as &$grp)
{
	if($grp['groupe_id'] == GROUPE_ANCIENS)
		continue;
	if(in_array($grp['groupe_nom'], $done))
		continue;
	$nom = htmlspecialchars($grp['groupe_nom']);
	$listeGroupes .= ' <a href="#'.rewrite($grp['groupe_nom']).'" title="'
		.$nom.'" style="color:'.$grp['groupe_class'].'">'.$nom.'</a> -';
	$done[] = &$grp['groupe_nom'];
}
// On enlève le dernier -
$listeGroupes = substr($listeGroupes, 1, -2);

$groupeNom = '';
$anciens = array();
foreach($ListerEquipe as &$m)
{
	if($m['groupe_id'] == GROUPE_ANCIENS)
	{
		$anciens[] = &$m;
		continue;
	}
	if($m['groupe_nom'] != $groupeNom)
	{
		if($groupeNom != '')
			echo '</tbody></table>';
		?>
		<p style="text-align : center; font-size : 14px;">
			<?php echo $listeGroupes; ?>
		</p>
		<h2><?php echo htmlspecialchars($m['groupe_nom']); ?></h2>
		<p><?php echo htmlspecialchars($m['groupe_description']); ?></p>
		<table class="UI_items noborder details" id="<?php echo rewrite($m['groupe_nom']); ?>">
			<thead>
				<tr>
					<th class="avatar">Avatar</th>
					<th>Pseudo</th>
					<th>Citation</th>
					<th>Dernière action</th>
					<?php if(verifier('stats_zcorrecteur') || verifier('stats_zcorrecteurs')){ ?>
					<th>Statistiques</th>
					<?php } ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="avatar">Avatar</th>
					<th>Pseudo</th>
					<th>Citation</th>
					<th>Dernière action</th>
					<?php if(verifier('stats_zcorrecteur') || verifier('stats_zcorrecteurs')){ ?>
					<th>Statistiques</th>
					<?php } ?>
				</tr>
			</tfoot>
			<tbody>
	<?php } ?>
	<?php $groupeNom = $m['groupe_nom']; ?>
	<tr>
		<td class="avatar"><?php if($m['utilisateur_avatar']){ ?><img src="/uploads/avatars/<?php echo $m['utilisateur_avatar']; ?>" class="equipe_avatar" alt="Avatar de <?php echo htmlspecialchars($m['utilisateur_pseudo']); ?>" /><?php } ?></td>
		<td><?php if($m['utilisateur_absent']==1) { ?>
					<span class="commandes_textuelles"><a href="/membres/profil-<?php echo $m['utilisateur_id']; ?>-<?php echo rewrite($m['utilisateur_pseudo']); ?>.html#absence" rel="nofollow"><img src="/bundles/zcooptions/img/gerer_absence.png" alt="Absent" title="Membre absent. Fin : <?php
					if(is_null($m['utilisateur_fin_absence']))
						echo 'indéterminée';
					else
						echo dateformat($m['utilisateur_fin_absence'], DATE, MINUSCULE);
					?>" /></a></span>
				<?php } ?><a href="/membres/profil-<?php echo $m['utilisateur_id'].'-'.rewrite($m['utilisateur_pseudo']); ?>.html" style="color: <?php echo $m['groupe_class']; ?>;" title="Groupe : <?php echo $m['groupe_nom']; ?>"><img src="/img/<?php echo $m['statut_connecte']; ?>.png" alt="<?php echo $m['statut_connecte_label']; ?>" title="<?php echo $m['statut_connecte_label']; ?>" /> <?php echo htmlspecialchars($m['utilisateur_pseudo']); ?></a>
		<?php if($m['utilisateur_titre']){ ?><br /><div class="title"><span class="ttpetit"><?php echo htmlspecialchars($m['utilisateur_titre']); ?></span></div><?php } ?></td>
		<td><?php echo htmlspecialchars($m['utilisateur_citation']); ?></td>
		<td><?php echo dateformat($m['derniere_action']); ?></td>
		<?php if(verifier('stats_zcorrecteurs') || (verifier('stats_zcorrecteur') && $m['utilisateur_id'] == $_SESSION['id'])){ ?>
		<td><a href="/statistiques/zcorrecteur-<?php echo $m['utilisateur_id']; ?>.html">Voir</a></td>
		<?php } elseif(verifier('stats_zcorrecteur')) echo '<td>-</td>' ?>
	</tr>
<?php } ?>
</tbody></table>

<h2>zAnciens</h2>

<p class="UI_descr">
	Les zAnciens sont d'anciens membres de l'équipe qui sont partis depuis.
	Ils ont droit à ce statut honorifique en remerciement de leur engagement
	passé, ainsi que pour leur permettre de rester en contact avec l'équipe
	présente. Il peut s'agir aussi bien d'anciens zCorrecteurs que d'anciens
	rédacteurs, développeurs ou administrateurs.<br /><br />

	Voici la liste de ces membres qui ont tous apporté leur pierre à l'édifice :
	<?php
	$out = '';
	foreach($anciens as &$ancien)
		$out .= ' <a href="/membres/profil-'.$ancien['utilisateur_id'].'-'.rewrite($ancien['utilisateur_pseudo']).'.html" style="color: purple;" title="Groupe : zAnciens">'
			.htmlspecialchars($ancien['utilisateur_pseudo']).'</a>, ';
	echo substr($out, 0, -2);
	?>.<br /><br />
</p>

<h2>Technologies utilisées par le site</h2>

<p class="UI_descr">
	Le serveur hébergeant ce site (surnommé affectueusement Artémis) tourne sous Linux, distribution Debian 5 « Lenny ».<br />
	Nous utilisons le serveur web Apache, PHP 5, MySQL comme système de gestion de bases de données relationnelles ainsi que le gestionnaire de versions Git pour le développement.<br />
	<br />
	Certaines icônes du site proviennent du site <a href="http://www.famfamfam.com/lab/icons/silk/" title="Ce site utilise les superbes icônes de FamFamFam">FamFamFam</a>, que nous remercions au passage.
</p>
