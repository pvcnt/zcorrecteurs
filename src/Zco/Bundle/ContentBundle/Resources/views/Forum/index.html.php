<?php use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
$view->extend('::layouts/default.html.php') ?>

<h1>Forum</h1>

<div class="options_forum">
	<ul>
		<?php if((verifier('corbeille_sujets')) && (empty($_GET['trash']))){ ?>
    	<li>
    		<a href="<?php echo $view['router']->path('zco_forum_index', ['trash' => 1]) ?>">Accéder à la corbeille</a>
    	</li>
		<?php } elseif (verifier('corbeille_sujets')){ ?>
		<li>
			<a href="<?php echo $view['router']->path('zco_forum_index') ?>">Sortir de la corbeille</a>
		</li>
		<?php } ?>
		<?php if((verifier('voir_archives'))) : ?>
		<li>
			<?php if(!empty($_GET['archives'])) : ?>
				<a href="<?php echo $view['router']->path('zco_forum_index') ?>">Sortir</a> des archives.
			<?php else : ?>
			<a href="<?php echo $view['router']->path('zco_forum_index', ['archives' => 1]) ?>">Voir les forums archivés</a>
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
				<tr class="grosse_cat<?php if(!empty($_GET['trash'])) echo '_trash'; ?>">
					<td colspan="<?php echo $colspan ?>" class="nom_forum">
						<h2><?php echo htmlspecialchars($valeur['cat_nom']) ?></h2>
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
				echo $view->render('ZcoContentBundle:Forum:_forum.html.php', $viewVars);
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
