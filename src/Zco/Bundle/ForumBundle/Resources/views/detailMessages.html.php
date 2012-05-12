<?php $view->extend('::layouts/default.html.php') ?>

<h1>Détail de l'activité d'un membre sur les forums</h1>

<?php
if(!empty($InfosUtilisateur['utilisateur_avatar']))
{
?>
	<p class="centre"><img src="/uploads/avatars/<?php echo $InfosUtilisateur['utilisateur_avatar']; ?>" alt="Avatar de <?php echo $InfosUtilisateur['utilisateur_pseudo']; ?>" /></p>
<?php
}
?>
<p class="centre">Vous êtes en train de visualiser le détail de l'activité de <a href="/membres/profil-<?php echo $InfosUtilisateur['utilisateur_id']; ?>-<?php echo rewrite($InfosUtilisateur['utilisateur_pseudo']); ?>.html"><strong><?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']); ?></strong></a> sur les forums.</p>
<div class="rmq attention">Certains membres peuvent avoir un total différent de 100&nbsp;%, en raison de messages postés sur des forums privés.</div>
<table class="UI_items">
	<thead>
		<tr>
			<th >Catégories</th>
			<?php
			if(verifier('voir_nb_messages'))
			{
				$colspan = 3;
			?>
				<th >Nombre</th>
			<?php
			}
			else
			{
				$colspan = 2;
			}
			?>
			<th >Pourcentage</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if(!empty($DetailMessages))
	{
		foreach($DetailMessages as $clef => $valeur)
		{
		?>
			<tr style="border: 1px solid #DDDDDD;">
				<td class="centre"><?php echo '<a href="sujets-participe-'.$_GET['id'].'-'.$valeur['cat_id'].'-'.rewrite($valeur['cat_nom']).'.html">'.htmlspecialchars($valeur['cat_nom']).'</a>'; ?></td>
				<?php if(verifier('voir_nb_messages')){	?>
					<td class="centre"><?php echo $valeur['pourcentage']; ?></td>
				<?php }	?>
				<td class="centre"><?php echo str_replace('.', ',', round(($valeur['pourcentage']/$InfosUtilisateur['utilisateur_forum_messages'])*100, 2)); ?> %</td>
			</tr>
		<?php
		}
	}
	else
	{
	?>
		<tr>
			<td class="centre" colspan="<?php echo $colspan; ?>"><a href="/membres/profil-<?php echo $InfosUtilisateur['utilisateur_id']; ?>-<?php echo rewrite($InfosUtilisateur['utilisateur_pseudo']); ?>.html"><?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']); ?></a> n'a posté aucun message dans les forums.</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
<p class="centre">Retour au <a href="/membres/profil-<?php echo $InfosUtilisateur['utilisateur_id']; ?>-<?php echo rewrite($InfosUtilisateur['utilisateur_pseudo']); ?>.html">profil de <strong><?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']); ?></strong></a></p>
