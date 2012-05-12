<?php $view->extend('::layouts/default.html.php') ?>

<?php /* Ce fichier sert à réaliser la correction d'un big-tuto */ ?>

<?php if(!empty($zco_mark)){ ?>
<?php afficher_message('<a href="'.$zco_mark.'"><img src="/img/zcorrection/marque.png" alt="" /> Vous aviez laissé une marque de correction.</a>'); ?>
<?php } ?>

<?php echo $view->render('ZcoZcorrectionBundle::_onglets.html.php', array('s' => $s)) ?>

<?php
//Titre
echo '<h1 id="titre-big-'.$InfosTuto['big_tuto_id'].'">'.str_replace('&amp;euro;', '&euro;', htmlspecialchars($InfosTuto['big_tuto_titre'])).'</h1>';

//Saut rapide
echo $SautRapide;

//Introduction
echo '<h2>Introduction générale du tutoriel</h2>';
echo '<div id="texte-big-intro-'.$InfosTuto['big_tuto_id'].'" onMouseOver="this.className = \'hover\';" onMouseOut="this.className = \'\';">'.str_replace('&amp;euro;', '&euro;', $view['messages']->parseSdz(trim($InfosTuto['big_tuto_introduction']))).'</div>';

//Parties
foreach($ListeParties as $v)
{
	echo '<br /><h3 id="titre-partie-'.$v['partie_id'].'">'.htmlspecialchars($v['partie_titre']).'</h3><br />';

	echo '<br /><h4>Introduction de la partie</h4>';
	echo '<div id="texte-partie-intro-'.$v['partie_id'].'" onMouseOver="this.className = \'hover\';" onMouseOut="this.className = \'\';">'.str_replace('&amp;euro;', '&euro;', $view['messages']->parseSdz(trim($v['partie_introduction']))).'</div>';

	echo '<fieldset style="margin: 10px;"><legend>Chapitres</legend><ul>';
	if($ListeMiniTutos[$v['partie_id']]){
		foreach($ListeMiniTutos[$v['partie_id']] as $t)
		{
			echo '<li><a href="/zcorrection/corriger-'.$_GET['id'].'-'.$t['mini_tuto_id'].'.html">'.str_replace('&amp;euro;', '&euro;', htmlspecialchars($t['mini_tuto_titre'])).'</a></li>';
		}
	}
	else{
		echo '<p>Il n\'y a aucun mini-tutoriel dans cette partie.</p>';
	}
	echo '</ul></fieldset>';

	echo '<h4>Conclusion de la partie</h4>';
	echo '<div id="texte-partie-ccl-'.$v['partie_id'].'" onMouseOver="this.className = \'hover\';" onMouseOut="this.className = \'\';">'.str_replace('&amp;euro;', '&euro;', $view['messages']->parseSdz(trim($v['partie_conclusion']))).'</div>';
}

//Conclusion
echo '<h2>Conclusion générale du tutoriel</h2>';
echo '<div id="texte-big-ccl-'.$InfosTuto['big_tuto_id'].'" onMouseOver="this.className = \'hover\';" onMouseOut="this.className = \'\';">'.str_replace('&amp;euro;', '&euro;', $view['messages']->parseSdz(trim($InfosTuto['big_tuto_conclusion']))).'</div>';

//Re-saut rapide
echo $SautRapide;
?>

<?php echo $view->render('ZcoZcorrectionBundle::_form.html.php') ?>
