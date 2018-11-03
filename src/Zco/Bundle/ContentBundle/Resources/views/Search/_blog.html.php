<p>Page : <?php echo implode($Pages); ?></p>
<?php
$Auteurs = $Resultats[1];
$Resultats = $Resultats[0];
foreach ($Resultats as $result)
{
	echo $view->render('ZcoBlogBundle::_intro_module.html.php', array(
		'InfosBillet' => $result,
		'Auteurs' => $Auteurs[$result['blog_id']],
		'nb' => 0,
	));
}
?>
<p>Page : <?php echo implode($Pages); ?></p>
