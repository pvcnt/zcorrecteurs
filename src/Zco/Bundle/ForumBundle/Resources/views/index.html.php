<?php use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;
$view->extend('::layouts/default.html.php') ?>

<h1>Forum</h1>

<div class="options_forum">
	<ul>
		<?php if((verifier('corbeille_sujets')) && (empty($_GET['trash']))){ ?>
    	<li>
    		<a href="?trash=1">Accéder à la corbeille</a>
    	</li>
		<?php } elseif (verifier('corbeille_sujets')){ ?>
		<li>
			<a href="index.html">Sortir de la corbeille</a>
		</li>
		<?php } ?>
		<li>
			Voir les sujets…
			<?php if(verifier('mettre_sujet_favori')){ ?><a href="suivi.html?favori=1">en favoris</a>, <?php } ?>
			<a href="suivi.html?epingle=1">épinglés</a>, <a href="suivi.html?coeur=1">coup de cœur</a>.
		</li>
		<li>
			Marquer tous les forums comme
			<a href="/forum/marquer-lu-1.html" onclick="return confirm('Tout marquer comme lu ?');">lus</a>,
			<a href="/forum/marquer-lu-2.html" onclick="return confirm('Tout marquer comme non lu ?');">non lus</a>
		</li>
		<?php if((verifier('voir_archives'))) : ?>
		<li>
			<?php if(!empty($_GET['archives'])) : ?>
				<a href="index.html">Sortir</a> des archives.
			<?php else : ?>
			<a href="?archives=1">Voir les forums archivés</a>
			<?php endif; ?>
		</li>
		<?php endif; ?>
	</ul>
</div>

<table class="liste_cat">
	<thead>
		<tr>
			<?php if(empty($_GET['trash'])) { ?>
			<th class="cats_colonne_flag"></th>
			<?php } ?>
			<th >Catégories</th>
			<?php
			if(empty($_GET['trash']))
			{
				$colspan = 3;
				echo '<th class="cats_colonne_dernier_msg centre">Dernier message</th>';
			}
			else
			{
				$colspan = 2;
				echo '<th class="cats_colonne_dernier_msg centre">Nombre de sujets</th>';
			}
			?>
		</tr>
	</thead>
	<tbody>
	<?php
	if ($ListerCategories)
	{
		foreach ($ListerCategories as $clef => $valeur)
		{
			if ($valeur['cat_niveau'] == 2)
			{
			?>
				<tr class="grosse_cat<?php if(!empty($_GET['trash'])) echo '_trash'; ?>" id="c<?php
					echo $valeur['cat_id']; ?>">
					<td colspan="<?php echo $colspan ?>" class="nom_forum">
						<h2>
							<a href="<?php
							echo CategoryDAO::FormateURLCategorie($valeur['cat_id']);
							if (!empty($_GET['trash']))
								echo '?trash=1'; ?>" rel="nofollow"><?php
							echo htmlspecialchars($valeur['cat_nom']) ?></a>
						</h2>
					</td>
				</tr>
			<?php
			}
			else
			{
				$viewVars = array('i' => $clef, 'forum' => $valeur, 'Lu' => $Lu);
				if ( !empty($_GET['archives']) ) {
					$viewVars['Parent'] = $valeur['parent'];
				}
				echo $view->render('ZcoForumBundle::_forum.html.php', $viewVars);
			}
		}
	}
	else
	{
	?>
		<tr class="sous_cat">
			<td colspan="<?php echo $colspan;?>" class="centre">Il n'y a aucun forum.</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
