<?php use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;
$view->extend('::layouts/default.html.php') ?>

<h1>Forum</h1>

<div class="options_forum">
	<ul>
		<li><a href="index.html">Accueil des forums</a></li>
		<?php if(!empty($_GET['trash'])){ ?>
		<li><a href="index.html?trash=1">Accueil de la corbeille</a></li>
		<li><a href="?trash=0">Sortir de la corbeille</a></li>
		<?php } elseif(verifier('corbeille_sujets', $_GET['id'])) {?>
		<li><a href="?trash=1">Accéder à la corbeille</a></li>
		<?php } ?>
    	<?php if(verifier('voir_archives')) : ?>
		<li>
			<?php if(!empty($_GET['archives'])) : ?>
				<a href="?archives=0">Sortir</a> des archives.
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
			<?php if (empty($_GET['trash'])) { ?>
				<th class="cats_colonne_flag"></th>
			<?php } ?>
			<th>Catégories</th>
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
		<?php if ( empty($_GET['archives']) ) : ?>
		<tr class="grosse_cat<?php if(!empty($_GET['trash'])) echo '_trash'; ?>">
			<td colspan="<?php echo $colspan; ?>" class="nom_forum">
				<a href="<?php echo CategoryDAO::FormateURLCategorie($InfosCategorie['cat_id']); if(!empty($_GET['trash'])) echo '?trash=1'; ?>" rel="nofollow">
					<?php echo htmlspecialchars($InfosCategorie['cat_nom']); ?>
				</a>
			</td>
		</tr>
		<?php
		endif;
		foreach($ListerUneCategorie as $clef => $valeur)
		{
			$viewVars = array('i' => $clef, 'forum' => $valeur, 'Lu' => $Lu);
			if ( !empty($_GET['archives']) ) {
				$viewVars['Parent'] = $valeur['parent'];
			}
			
			echo $view->render('ZcoForumBundle::_forum.html.php', $viewVars);
		}
		?>
	</tbody>
</table>