<?php $view->extend('::layouts/default.html.php') ?>

<?php if(isset($s)){ ?>
<?php echo $view->render('ZcoZcorrectionBundle::_onglets.html.php', array('s' => $s)) ?>
<?php } ?>

<?php
//Titre
echo '<h1>'.htmlspecialchars($InfosTuto['big_tuto_titre']).'</h1>';

//Saut rapide
echo $SautRapide;

//Saut de versions
echo $SautVersions;

//Introduction
echo '<h2>Introduction générale du tutoriel</h2>';
echo '<div>'.$view['messages']->parseSdz(trim($InfosTuto['big_tuto_introduction'])).'</div>';

//Parties
foreach($ListeParties as $v)
{
	//$ListeMiniTutos = ListeTutosPartie($v['partie_id']);
	echo '<br /><h3>'.htmlspecialchars($v['partie_titre']).'</h3><br />';

	echo '<br /><h4>Introduction de la partie</h4>';
	echo '<div>'.$view['messages']->parseSdz(trim($v['partie_introduction'])).'</div>';

	echo '<fieldset style="margin: 10px;"><legend>Chapitres</legend>';
	if($ListeMiniTutos[$v['partie_id']])
	{
		echo '<ul>';
		foreach($ListeMiniTutos[$v['partie_id']] as $t)
		{
			echo '<li><a href="/zcorrection/voir-big-tuto-'.$_GET['id'].'-'.$t['mini_tuto_id'].'.html'.(isset($_GET['cid']) ? '?cid='.$_GET['cid'] : '').'">'.htmlspecialchars($t['mini_tuto_titre']).'</a></li>';
		}
		echo '</ul>';
	}
	else
		echo '<p>Cette partie ne contient aucun chapitre.</p>';
	echo '</fieldset>';

	echo '<h4>Conclusion de la partie</h4>';
	echo '<div>'.$view['messages']->parseSdz(trim($v['partie_conclusion'])).'</div>';
}

//Conclusion
echo '<h2>Conclusion générale du tutoriel</h2>';
echo '<div>'.$view['messages']->parseSdz(trim($InfosTuto['big_tuto_conclusion'])).'</div>';

//Re-saut rapide
echo $SautRapide;

//Re-saut de versions
echo $SautVersions;
?>
