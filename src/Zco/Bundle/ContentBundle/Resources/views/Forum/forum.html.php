<?php use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
$view->extend('::layouts/default.html.php') ?>

<?php if(!empty($_GET['trash'])) { ?>
	<h1><?php echo 'Corbeille du forum <em>'.htmlspecialchars($InfosForum['cat_nom']).'</em>'; ?></h1>
<?php } else { ?>
	<h1><?php echo htmlspecialchars($InfosForum['cat_nom']); ?></h1>
<?php } ?>

<h2><?php echo htmlspecialchars($InfosForum['cat_description']); ?></h2>

<?php if(verifier('corbeille_sujets', $InfosForum['cat_id']) || verifier('voir_archives')): ?>
<div class="options_forum">
	<ul>
		<?php if(verifier('corbeille_sujets', $InfosForum['cat_id'])){ ?>
		<li>
			<?php if(!empty($_GET['trash'])){ ?>
			<a href="<?php echo $view['router']->path('zco_forum_show', ['id' => $InfosForum['cat_id'], 'slug' => rewrite($InfosForum['cat_nom'])]) ?>">Sortir</a> de la corbeille.
			<?php } else{ ?>
			Accéder à la <a href="<?php echo $view['router']->path('zco_forum_show', ['id' => $InfosForum['cat_id'], 'slug' => rewrite($InfosForum['cat_nom']), 'trash' => 1]) ?>">corbeille de ce forum</a>.
			<?php } ?>
		</li>
		<?php } ?>
    	<?php if(verifier('voir_archives')) : ?>
		<li>
			<?php if(!empty($_GET['archives'])) : ?>
				<a href="<?php echo $view['router']->path('zco_forum_show', ['id' => $InfosForum['cat_id'], 'slug' => rewrite($InfosForum['cat_nom'])]) ?>">Sortir</a> des archives.
			<?php else : ?>
			<a href="<?php echo $view['router']->path('zco_forum_show', ['id' => $InfosForum['cat_id'], 'slug' => rewrite($InfosForum['cat_nom']), 'archives' => 1]) ?>">Voir les forums archivés</a>
			<?php endif; ?>
		</li>
		<?php endif; ?>
	</ul>
</div>
<?php endif ?>

<?php if (!empty($ListerUneCategorie)){ ?>
<table class="liste_cat">
	<thead>
		<tr>
			<?php if (empty($_GET['trash'])) { ?>
				<th class="cats_colonne_flag"></th>
			<?php } ?>
			<th>Forums</th>
			<?php if (empty($_GET['trash'])) { $colspan = 3; ?>
				<th class="cats_colonne_dernier_msg centre">Dernier message</th>
			<?php } else{ $colspan = 2; ?>
                <th class="centre">Sujets</th>
            <?php } ?>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="<?php echo $colspan; ?>"> </td>
		</tr>
	</tfoot>

	<tbody>
		<?php 
		foreach($ListerUneCategorie as $clef => $valeur)
		{
			$viewVars = array('i' => $clef, 'forum' => $valeur, 'Lu' => $LuForum);
			if ( !empty($_GET['archives']) ) {
				$viewVars['Parent'] = $valeur['parent'];
			}
			
			echo $view->render('ZcoContentBundle:Forum:_forum.html.php', $viewVars);
		}
		?>
	</tbody>
</table><br />
<?php } ?>

<?php echo $SautRapide ?>

<?php if (verifier('creer_sujets', $InfosForum['cat_id'])){ ?>
<p class="reponse_ajout_sujet">
    <a href="<?php echo $view['router']->url('zco_topic_new', ['id' => $InfosForum['cat_id'], 'trash' => $_GET['trash'] ?? null]) ?>">
		<img src="/bundles/zcocontent/img/nouveau.png" alt="Nouveau sujet" title="Nouveau sujet" />
	</a>
</p>
<?php } ?>

	<table class="liste_cat">
	<thead>
		<tr>
            <td colspan="7">Page : <?php echo implode($tableau_pages) ?></td>
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
			<td colspan="7">Page : <?php echo implode($tableau_pages) ?></td>
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
				?><tr class="espace_postit"><td colspan="7">&nbsp;</td></tr><?php
			}
			//FIN DU CODE : Vérification de si on vient juste de finir d'afficher les annonces en haut.
			?>
			<tr class="sous_cat">
				<td class="centre">
					<a href="<?php
					if(!empty($valeur['lunonlu_message_id']))
					{
					    echo $view['router']->path('zco_topic_show', ['id' => $valeur['sujet_id'], 'c' => $valeur['lunonlu_message_id'], 'slug' => rewrite($valeur['sujet_titre'])]);
					}
					else
					{
                        echo $view['router']->path('zco_topic_show', ['id' => $valeur['sujet_id'], 'slug' => rewrite($valeur['sujet_titre'])]);
					} ?>">
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
					?>
				</td>
				<td title="Sujet commencé <?php echo dateformat($valeur['sujet_date'], MINUSCULE); ?>">
					<?php
					if($Lu[$clef]['fleche'])
					{
						echo '<a href="' . $view['router']->path('zco_topic_show', ['id' => $valeur['sujet_id'], 'c' => $valeur['lunonlu_message_id'], 'slug' => rewrite($valeur['sujet_titre'])]) . '">'
						    . '<img src="/pix.gif" class="fff bullet_go" alt="Aller au dernier message lu" title="Aller au dernier message lu" /></a>';
					}
					?>
					<a href="<?php echo $view['router']->path('zco_topic_show', ['id' => $valeur['sujet_id'], 'slug' => rewrite($valeur['sujet_titre'])]); ?>"><?php echo htmlspecialchars($valeur['sujet_titre']); ?></a>

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
					<a href="/membres/profil-<?php echo $valeur['sujet_auteur']; ?>-<?php echo rewrite($valeur['sujet_auteur_pseudo']); ?>.html" rel="nofollow">
						<?php } ?>
						<?php echo htmlspecialchars($valeur['sujet_auteur_pseudo']); ?>
						<?php if(!empty($valeur['sujet_auteur_pseudo_existe'])) {?>
					</a>
					<?php } ?>
				</td>

				<td class="centre"><?php echo $valeur['sujet_reponses']; ?></td>

				<td class="dernier_msg centre">
					<?php
					echo '<a href="' . $view['router']->path('zco_topic_show', ['id' => $valeur['sujet_id'], 'c' => $valeur['message_id'], 'slug' => rewrite($valeur['sujet_titre'])]) . '">'.dateformat($valeur['message_date']).'</a><br /> ';
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
			<?php
			if(!empty($_GET['trash']))
			{
			?>
				<td colspan="7" class="centre">La corbeille de ce forum est vide.</td>
			<?php
			}
			else
			{
			?>
				<td colspan="7" class="centre">Ce forum ne contient pas de sujet.</td>
			<?php
			}
			?>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>

<?php echo $SautRapide ?>

<?php if(verifier('creer_sujets', $InfosForum['cat_id']))
{
?>
<p class="reponse_ajout_sujet">
    <a href="<?php echo $view['router']->url('zco_topic_new', ['id' => $InfosForum['cat_id'], 'trash' => $_GET['trash'] ?? null]) ?>">
		<img src="/bundles/zcocontent/img/nouveau.png" alt="Nouveau sujet" title="Nouveau sujet" />
	</a>
</p>
<?php }	?>
