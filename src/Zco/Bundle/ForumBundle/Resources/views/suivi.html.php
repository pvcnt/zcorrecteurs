<?php $view->extend('::layouts/default.html.php') ?>

<div class="options_forum">
	<ul>
		<li>
			<a href="index.html">Accueil des forums</a>
		</li>
		<li>
			Voir les sujets…
			<?php if(verifier('code')){ ?>
			<a href="?solved=0">non résolus</a> (et <a href="?solved=0&amp;closed=0">ouverts</a>),
			<?php } ?>
			<?php if(verifier('mettre_sujet_favori')){ ?><a href="?favori=1">en favoris</a>, <?php } ?><a href="?epingle=1">épinglés</a>, <a href="?coeur=1">coup de cœur</a>.
		</li>
	</ul>
</div>

<?php
	$colspan = 7;
?>
	<table class="liste_cat">
	<thead>
		<tr>
			<td colspan="<?php echo $colspan;?>">Page :
			<?php
			foreach($tableau_pages as $element)
			{
				echo $element;
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

	<tfoot>
		<tr>
			<td colspan="<?php echo $colspan;?>">Page :
			<?php
			foreach($tableau_pages as $element)
			{
				echo $element;
			}
			?>
			</td>
		</tr>
	</tfoot>

	<tbody>
	<?php
	//Ici on fait une boucle qui va nous lister tous les sujets du forum.
	if($ListerSujets) //Si il y a au moins un sujet à lister, on liste !
	{
		$on_a_fini_dafficher_les_annonces = -1;
		foreach($ListerSujets as $clef => $valeur)
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
				?><tr class="espace_postit"><td colspan="<?php echo $colspan;?>">&nbsp;</td></tr><?php
			}
			//FIN DU CODE : Vérification de si on vient juste de finir d'afficher les annonces en haut.
			?>
			<tr class="sous_cat">
				<td class="centre">
					<a href="/forum/
					<?php
					if(!empty($valeur['lunonlu_message_id']))
					{
						echo 'sujet-'.$valeur['sujet_id'].'-'.$valeur['lunonlu_message_id'].'-'.rewrite($valeur['sujet_titre']).'.html';
					}
					else
					{
						echo 'sujet-'.$valeur['sujet_id'].'-'.rewrite($valeur['sujet_titre']).'.html';
					}
					?>
					">
                    <?php
                        switch($Lu[$clef]['image']) {
                            case 'pas_nouveau_message.png':         $image = 'lightbulb_off'; break;
                            case 'nouveau_message.png':             $image = 'lightbulb'; break;
                            case 'repondu_pas_nouveau_message.png': $image = 'lightbulb_off_add'; break;
                            case 'repondu_nouveau_message.png':     $image = 'lightbulb_add'; break;
                            default: $image= 'cross';
                        }
                    ?>
					<img src="/pix.gif" class="fff <?php echo $image; ?>" title="<?php echo $Lu[$clef]['title']; ?>" alt="<?php echo $Lu[$clef]['title']; ?>" /></a>
				</td>
				<td class="centre">
					<?php
					//Affichage ou non du logo annonce
					if($valeur['sujet_annonce'])
					{
						?>
						<img src="/pix.gif" class="fff flag_red" title="Annonce" alt="Annonce" />
						<?php
					}
					//Affichage ou non du logo sondage
					if($valeur['sujet_sondage'])
					{
						?>
						<img src="/pix.gif" class="fff chart_bar" title="Sondage" alt="Sondage" />
						<?php
					}
					//Affichage ou non du logo sujet fermé (cadenas)
					if($valeur['sujet_ferme'])
					{
						?>
						<img src="/pix.gif" class="fff lock" title="Fermé" alt="Fermé" />
						<?php
					}
					//Affichage ou non du logo sujet résolu
					if($valeur['sujet_resolu'])
					{
						?>
						<img src="/pix.gif" class="fff accept" title="Résolu" alt="Résolu" />
						<?php
					}
					//Affichage ou non du logo coup de coeur
					if($valeur['sujet_coup_coeur'])
					{
						?>
						<img src="/pix.gif" class="fff heart" title="Sujet coup de c&oelig;ur" alt="Coup de c&oelig;ur" />
						<?php
					}
					//Affichage ou non du logo favori
					if($valeur['lunonlu_favori'])
					{
						?>
						<img src="/pix.gif" class="fff award_star_gold_1" alt="Favoris" title="Sujet dans mes favoris" />
						<?php
					}
					?>
				</td>
				<td title="Sujet commencé <?php echo dateformat($valeur['sujet_date'], MINUSCULE); ?>">
					<?php
					if($Lu[$clef]['fleche'])
					{
						echo '<a href="sujet-'.$valeur['sujet_id'].'-'.$valeur['lunonlu_message_id'].'-'.rewrite($valeur['sujet_titre']).'.html"><img src="/pix.gif" class="fff bullet_go" alt="Aller au dernier message lu" title="Aller au dernier message lu" /></a>';
					}
					?>
					<a href="<?php echo 'sujet-'.$valeur['sujet_id'].'-'.rewrite($valeur['sujet_titre']); ?>.html"><?php echo htmlspecialchars($valeur['sujet_titre']); ?></a>

					<span class="sous_titre"><br />
						<?php if(!empty($valeur['sujet_sous_titre'])){ ?>
						<?php echo htmlspecialchars($valeur['sujet_sous_titre']); ?>
						<?php } ?>
					</span>
				</td>

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
					<a href="/membres/profil-<?php echo $valeur['sujet_auteur']; ?>-<?php echo rewrite($valeur['sujet_auteur_pseudo']); ?>.html" rel="nofollow" style="color: <?php echo $valeur['class_auteur']; ?>;">
						<?php } ?>
						<?php echo htmlspecialchars($valeur['sujet_auteur_pseudo']); ?>
						<?php if(!empty($valeur['sujet_auteur_pseudo_existe'])) {?>
					</a>
					<?php } ?>
				</td>

				<td class="centre"><?php echo $valeur['sujet_reponses']; ?></td>

				<td class="dernier_msg centre">
					<?php
					echo '<a href="sujet-'.$valeur['sujet_id'].'-'.$valeur['message_id'].'-'.rewrite($valeur['sujet_titre']).'.html">'.dateformat($valeur['message_date']).'</a><br /> ';
					if(!empty($valeur['sujet_dernier_message_pseudo_existe']))
					{
						echo '<a href="/membres/profil-'.$valeur['sujet_dernier_message_auteur_id'].'-'.rewrite($valeur['sujet_dernier_message_pseudo']).'.html" rel="nofollow"><span style="color: '.$valeur['class_dernier_message'].';">';
					}
					echo htmlspecialchars($valeur['sujet_dernier_message_pseudo']);
					if(!empty($valeur['sujet_dernier_message_pseudo_existe']))
					{
						echo '</span></a>';
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
			<td colspan="<?php echo $colspan;?>" class="centre">Il n'y a aucun sujet.</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
<p class="centre"><strong>Retour à la <a href="index.html">liste des catégories</a></strong></p>
