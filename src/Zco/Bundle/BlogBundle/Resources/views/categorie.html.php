<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($InfosCategorie['cat_nom']); ?></h1>

<p class="centre">
	<strong><?php echo $NombreDeBillet ?> billet<?php echo pluriel($NombreDeBillet) ?></strong>
	<?php echo pluriel($NombreDeBillet, 'ont', 'a') ?> été trouvé<?php echo pluriel($NombreDeBillet) ?>.
</p>

<?php echo $view->render('ZcoBlogBundle::_liste_categories.html.php', array('categorieId' => $InfosCategorie['cat_id'])) ?>

<?php if(!empty($ListerBillets)){ ?>
<p>Page : <?php echo implode($ListePage); ?></p>
<?php
foreach($ListerBillets as $billet)
{
	echo $view->render('ZcoBlogBundle::_intro.html.php', array(
		'InfosBillet' => $billet,
		'Auteurs' => $BilletsAuteurs[$billet['blog_id']]));
}
?>
<p>Page : <?php echo implode($ListePage); ?></p>
<?php } else{ ?>
<p>Aucun billet n'a encore été publié dans cette catégorie.</p>
<?php } ?>
