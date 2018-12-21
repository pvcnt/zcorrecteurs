<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des sujets auxquels <?php echo $InfosUtilisateur['utilisateur_pseudo']; ?> a participé</h1>

<?php
if(!empty($InfosUtilisateur['utilisateur_avatar']))
{
?>
	<p class="centre">
        <?php echo $view['messages']->afficherAvatar($InfosUtilisateur) ?>
    </p>
<?php
}
?>
<p class="centre"><a href="/membres/profil-<?php echo $InfosUtilisateur['utilisateur_id']; ?>-<?php echo rewrite($InfosUtilisateur['utilisateur_pseudo']); ?>.html"><strong><?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']); ?></strong></a> a participé à <?php echo $CompterSujetsParticipe; ?> sujet<?php echo pluriel($CompterSujetsParticipe); ?><?php
if(!empty($_GET['id2']))
{
    echo ' dans le forum sélectionné';
}
?>.</p>
<div style="margin-bottom: 20px;" class="rmq attention">Vous ne voyez ici que les sujets que vous avez le droit de voir. Il se peut que <?php echo $InfosUtilisateur['utilisateur_pseudo']; ?> ait participé à plus de sujets.</div>
<table class="liste_cat">
	<thead>
		<tr>
			<td colspan="7">Page :
			<?php
			foreach($tableau_pages as $element)
			{
				echo $element.'';
			}
			?>
			</td>
		</tr>
		<tr>
			<th class="forum_colonne_flag"></th>
			<th class="forum_colonne_flag2"></th>
			<th>Titre du sujet</th>
			<th class="forum_colonne_page">Pages</th>
			<th class="forum_colonne_createur centre">Créateur</th>
			<th class="forum_colonne_reponses centre">Réponses</th>
			<th class="forum_colonne_dernier_msg centre">Dernier message</th>
		</tr>
	</thead>
	<tbody>
<?php
if($ListerSujetsParticipe)
{
	$on_a_fini_dafficher_les_annonces = -1;
	foreach($ListerSujetsParticipe as $clef => $valeur)
	{
		//DÉBUT DU CODE : Vérification de si on vient juste de finir d'afficher les annonces en haut.
		if($valeur["sujet_annonce"]) //Si c'est une annonce
		{
			$on_a_fini_dafficher_les_annonces = 0;
		}
		else
		{
			if($on_a_fini_dafficher_les_annonces == 0)
			{
				$on_a_fini_dafficher_les_annonces = 1;
			}
			else
			{
				$on_a_fini_dafficher_les_annonces = -1;
			}
		}
		/*
		Si on vient de finir d'afficher les annonces en haut,
		on insère une ligne vide de séparation entre les annonces et les sujets normaux.
		*/
		if($on_a_fini_dafficher_les_annonces == 1)
		{
			?><tr class="espace_postit"><td colspan="7">&nbsp;</td></tr><?php
		}
		//FIN DU CODE : Vérification de si on vient juste de finir d'afficher les annonces en haut.
		?>
		<tr class="sous_cat">
			<td class="centre">
				<a href="/forum/
				<?php
				if(!empty($valeur['regardeur_dernier_message_lu']))
				{
					echo rewrite($valeur['sujet_titre']).'-s'.$valeur['sujet_id'].'-m'.$valeur['regardeur_dernier_message_lu'].'.html';
				}
				else
				{
					echo rewrite($valeur['sujet_titre']).'-s'.$valeur['sujet_id'].'.html';
				}
				?>
				">
				<img src="/bundles/zcoforum/img/<?php echo $Lu[$clef]['image']; ?>" title="<?php echo $Lu[$clef]['title']; ?>" alt="<?php echo $Lu[$clef]['title']; ?>" /></a>
				</td>
				<td class="centre">
				<?php
				//Affichage ou non du logo annonce
				if($valeur['sujet_annonce'])
				{
					?>
					<img src="/bundles/zcoforum/img/annonce.png" title="Annonce" alt="Annonce" />
					<?php
				}
				//Affichage ou non du logo sondage
				if($valeur['sujet_sondage'])
				{
					?>
					<img src="/bundles/zcoforum/img/sondages.png" title="Sondage" alt="Sondage" />
					<?php
				}
				//Affichage ou non du logo sujet fermé (cadenas)
				if($valeur['sujet_ferme'])
				{
					?>
					<img src="/bundles/zcoforum/img/cadenas.png" title="Fermé" alt="Fermé" />
					<?php
				}
				//Affichage ou non du logo sujet résolu
				if($valeur['sujet_resolu'])
				{
					?>
					<img src="/bundles/zcoforum/img/resolu.png" title="Résolu" alt="Résolu" />
					<?php
				}
				?>
				</td>
				<td title="Sujet commencé <?php echo mb_strtolower(str_replace(' ', ' à ', $valeur['sujet_date'])); ?>">
				<?php
				if($Lu[$clef]['fleche'])
				{
					echo '<a href="/forum/sujet-'.$valeur['sujet_id'].'-'.$valeur['regardeur_dernier_message_lu'].'-'.rewrite($valeur['sujet_titre']).'.html"><img src="/bundles/zcoforum/img/fleche.png" alt="Aller au dernier message lu" title="Aller au dernier message lu" /></a>';
				}
				?>

				<a href="/forum/sujet-<?php echo $valeur['sujet_id'].'-'.rewrite($valeur['sujet_titre']); ?>.html"><?php echo htmlspecialchars($valeur['sujet_titre']); ?></a></td>

				<td class="centre">
					<?php
					$i = 0;
					foreach($Pages[$clef] as $element)
					{
						$i++;
						echo $element;
						if($i == 3)
						{
							$i = 0;
							echo '<br />';
						}
					}
					?>
				</td>
				<td class="centre">
				<?php if(!empty($valeur['sujet_auteur_pseudo_existe'])) {?>
				<a href="/membres/profil-<?php echo $valeur['sujet_auteur']; ?>-<?php echo rewrite($valeur['sujet_auteur_pseudo']); ?>.html">
				<?php } ?>
				<?php echo htmlspecialchars($valeur['sujet_auteur_pseudo']); ?>
				<?php if(!empty($valeur['sujet_auteur_pseudo_existe'])) {?>
				</a>
				<?php } ?>
				</td>
				<td class="centre"><?php echo $valeur['sujet_reponses']; ?></td>
				<td class="dernier_msg centre">
				<?php
				echo '<a href="/forum/sujet-'.$valeur['sujet_id'].'-'.$valeur['regardeur_dernier_message_lu'].'-'.rewrite($valeur['sujet_titre']).'.html">'.dateformat($valeur['message_date']).'</a><br />';
				if(!empty($valeur['sujet_dernier_message_pseudo_existe']))
				{
					echo '<a href="/membres/profil-'.$valeur['sujet_dernier_message_auteur_id'].'-'.rewrite($valeur['sujet_dernier_message_pseudo']).'.html">';
				}
				echo htmlspecialchars($valeur['sujet_dernier_message_pseudo']);
				if(!empty($valeur['sujet_dernier_message_pseudo_existe']))
				{
					echo '</a>';
				}
				?>
				</td>
		</tr>
		<?php
	}
}
//Si il n'y a aucun sujet à lister, on affiche un message.
else
{
?>
	<tr class="sous_cat vide">
		<td colspan="7" class="centre">Ce membre n'a participé à aucun sujet.</td>
	</tr>
<?php
}
?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7">Page :
			<?php
			foreach($tableau_pages as $element)
			{
				echo $element.'';
			}
			?>
			</td>
		</tr>
	</tfoot>
</table>
<p class="centre">Retour au <a href="/membres/profil-<?php echo $InfosUtilisateur['utilisateur_id']; ?>-<?php echo rewrite($InfosUtilisateur['utilisateur_pseudo']); ?>.html">profil de <strong><?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']); ?></strong></a></p>
