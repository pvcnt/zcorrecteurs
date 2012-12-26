<?php $view->extend('::layouts/default.html.php') ?>

<?php if(!empty($_GET['trash'])) { ?>
	<h1><?php echo 'Corbeille du forum <em>'.htmlspecialchars($InfosForum['cat_nom']).'</em>'; ?></h1>
<?php } else { ?>
	<h1><?php echo htmlspecialchars($InfosForum['cat_nom']); ?></h1>
<?php } ?>

<h2><?php echo htmlspecialchars($InfosForum['cat_description']); ?></h2>

<?php if(!empty($InfosForum['cat_reglement'])) echo '<div class="reglement">'.$view['messages']->parse($InfosForum['cat_reglement']).'</div>'; ?>

<div class="options_forum">
	<ul>
		<?php if(verifier('code') || verifier('mettre_sujet_favori')){ ?>
		<li>
			Voir les sujets…
			<?php if(verifier('code')){ ?>
			<a href="?solved=0">non résolus</a> (et <a href="?solved=0&closed=0">ouverts</a>),
			<?php } ?>
			<a href="?favori=1">en favoris</a>, <a href="?">tous</a>
		</li>

		<?php } if(verifier('corbeille_sujets', $_GET['id'])){ ?>
		<li>
			<?php if(!empty($_GET['trash'])){ ?>
			<a href="<?php echo FormateURLCategorie($InfosForum['cat_id']); ?>">Sortir</a> de la corbeille.
			<?php } else{ ?>
			Accéder à la <a href="?trash=1">corbeille de ce forum</a>.
			<?php } ?>
		</li>
		<?php } ?>
		<li>
    		<img src="/pix.gif" class="fff feed" alt="" /> S'abonner…
    		<a href="/forum/messages-flux-<?php echo $_GET['id'] ?>.html">au flux du forum «&nbsp;<?php echo htmlspecialchars($InfosForum['cat_nom']) ?>&nbsp;»</a>, 
    		<a href="/forum/messages-flux-<?php echo $Parent['cat_id'] ?>.html">au flux de la catégorie «&nbsp;<?php echo htmlspecialchars($Parent['cat_nom']) ?>&nbsp;»</a>
    	</li>
    	<?php if(verifier('voir_archives')) : ?>
		<li>
			<?php if(!empty($_GET['archives'])) : ?>
				<a href="<?php echo FormateURLCategorie($InfosForum['cat_id']); ?>">Sortir</a> des archives.
			<?php else : ?>
			<a href="?archives=1">Voir les forums archivés</a>
			<?php endif; ?>
		</li>
		<?php endif; ?>
	</ul>
</div>

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
			<?php } else{ $colspan = 1; } ?>
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
			
			echo $view->render('ZcoForumBundle::_forum.html.php', $viewVars);
		}
		?>
	</tbody>
</table><br />
<?php } ?>

<?php echo $SautRapide ?>

<?php if (verifier('creer_sujets', $_GET['id'])){ ?>
<p class="reponse_ajout_sujet">
	<a href="nouveau-<?php echo $_GET['id']; ?>.html<?php if(!empty($_GET['trash'])) echo '?trash=1'; ?>">
		<img src="/bundles/zcoforum/img/nouveau.png" alt="Nouveau sujet" title="Nouveau sujet" />
	</a>
</p>
<?php
}
if($action_etendue_a_plusieurs_messages_actif AND $ListerSujets)
{
	$colspan = 8;
}
else
{
	$colspan = 7;
}

if($action_etendue_a_plusieurs_messages_actif AND $ListerSujets)
{
?>
	<form name="action_etendue" id="action_etendue" action="" method="post">
<?php
}

if($action_etendue_a_plusieurs_messages_actif)
{
?>
	<table class="liste_cat" onclick="InverserEtat(event);" onmouseover="InverserEtat(event);" onmouseout="InverserEtat(event);">
<?php
}
else
{
?>
	<table class="liste_cat">
<?php
}
?>	<thead>
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
			<?php
				if($action_etendue_a_plusieurs_messages_actif AND $ListerSujets)
				{
				?>
					<th> </th>
				<?php
				}
			?>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="<?php echo $colspan; ?>">
				<?php echo $view->render('ZcoForumBundle::_liste_connectes.html.php', array('ListerVisiteurs' => $ListerVisiteurs)) ?>
			</td>
		</tr>
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

						<?php /*if(!empty($Tags[$valeur['sujet_id']])){ ?>
						<?php foreach($Tags[$valeur['sujet_id']] as $tag){ ?>
						<a href="/tags/tag-<?php echo $tag['tag_id']; ?>-<?php echo rewrite($tag['tag_nom']); ?>.html" class="tag">
							<?php echo htmlspecialchars($tag['tag_nom']); ?>
						</a>
						<?php } ?>
						<?php }*/ ?>
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

				<?php
				if($action_etendue_a_plusieurs_messages_actif)
				{
				?>
					<td class="centre"><input type="checkbox" name="sujet[<?php echo $valeur['sujet_id']; ?>]" id="sujet[<?php echo $valeur['sujet_id']; ?>]" onclick="ColorerLigneOncheck(event)"/></td>
				<?php
				}
				?>
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
				<td colspan="<?php echo $colspan;?>" class="centre">La corbeille de ce forum est vide.</td>
			<?php
			}
			else
			{
			?>
				<td colspan="<?php echo $colspan;?>" class="centre">Ce forum ne contient pas de sujet.</td>
			<?php
			}
			?>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
<?php
if($action_etendue_a_plusieurs_messages_actif AND $ListerSujets)
{
	include(dirname(__FILE__).'/action_etendue_plusieurs_sujets.html.php');
	echo '</form>';
}

if(!empty($_GET['trash'])){ ?>
<p class="centre"><strong>Retour <a href="index.html?trash=1">à l'accueil de la corbeille</a></strong><br />
OU<br />
<strong>Retour <a href="<?php echo FormateURLCategorie($InfosForum['cat_id']); ?>">au forum "<?php echo htmlspecialchars($InfosForum['cat_nom']); ?>"</a> ou <a href="/forum/">à la liste des forums</a></strong></p>
<?php } else{ ?>
<p class="centre"><strong>
Retour
<?php if(isset($Parent)): ?>
	<a href="<?php echo FormateURLCategorie($Parent['cat_id']); ?>">au forum <em><?php
	echo htmlspecialchars($Parent['cat_nom']); ?></em></a>
	ou
<?php endif ?>
à la <a href="index.html">liste des catégories</a></strong></p>
<?php
}

if(!empty($_GET['archives'])) : ?>

<?php if(sizeof($ListerUneCategorie) == 0) : ?>
	<center>Aucun forum archivé.</center><br/>
<?php endif; ?>	
<p class="centre"><strong>Retour <a href="index.html?archives=1">à l'accueil des archives</a></strong><br />
OU<br />
<strong>Retour <a href="<?php echo FormateURLCategorie($InfosForum['cat_id']); ?>">au forum "<?php echo htmlspecialchars($InfosForum['cat_nom']); ?>"</a> ou <a href="/forum/">à la liste des forums</a></strong></p>
	
<?php endif;
echo $SautRapide;

if(verifier('creer_sujets', $_GET['id']))
{
?>
<p class="reponse_ajout_sujet">
	<a href="nouveau-<?php echo $_GET['id']; ?>.html<?php if(!empty($_GET['trash'])) echo '?trash=1'; ?>">
		<img src="/bundles/zcoforum/img/nouveau.png" alt="Nouveau sujet" title="Nouveau sujet" />
	</a>
</p>
<?php }	?>
