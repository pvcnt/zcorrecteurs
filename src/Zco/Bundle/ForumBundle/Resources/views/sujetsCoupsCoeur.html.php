<?php include_once(__DIR__.'/../../../UserBundle/modeles/utilisateurs.php') ?>
<?php $view->extend('::layouts/default.html.php') ?>

<h1>Gérer les sujets en coups de c&oelig;ur</h1>

<p>Il vous est possible à partir de cette page de gérer les sujets en coups de c&oelig;ur.</p>
<?php if($ListerSujets){ ?>

<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 20%;">Titre</th>
			<th style="width: 20%;">Auteur</th>
			<th style="width: 10%;">Réponses</th>
			<th style="width:  5%;">Actions</th>
		</tr>
	</thead>

	<tbody>
		<?php
		foreach($ListerSujets as $cle => $valeur){
			$Auteur = InfosUtilisateur($valeur['sujet_auteur']);
		?>
		<tr>
			<td>
				<a href="/forum/<?php echo 'sujet-'.$valeur['sujet_id'].'-'.rewrite($valeur['sujet_titre']).'.html'; ?>"><?php echo $valeur['sujet_titre']; ?></a>
			</td>
			<td class="centre">
				<a href="/membres/profil-<?php echo $Auteur['utilisateur_id'] ; ?>-<?php echo rewrite($Auteur['utilisateur_pseudo']); ?>.html" style="color: <?php echo $Auteur['groupe_class']; ?>;"><?php echo htmlspecialchars($Auteur['utilisateur_pseudo']); ?></a>
			</td>
			<td class="centre">
				<?php echo $valeur['sujet_reponses']; ?>
			</td>
			<td class="centre">
				<a href="?action=changer_coup_coeur&id_sujet=<?php echo $valeur['sujet_id']; ?>" title="Enlever ce sujet des coups de c&oelig;ur"><img src="/img/supprimer.png" alt="Supprimer" /></a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php } else { ?>
<p>Il n'y a actuellement pas de sujets en coups de c&oelig;ur.</p>
<?php } ?>