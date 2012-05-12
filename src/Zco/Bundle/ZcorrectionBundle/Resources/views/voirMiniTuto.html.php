<?php $view->extend('::layouts/default.html.php') ?>

<?php if(isset($s)){ ?>
<?php echo $view->render('ZcoZcorrectionBundle::_onglets.html.php', array('s' => $s)) ?>
<?php } ?>

<?php
//Titre
if (empty($InfosTuto['mini_tuto_titre'])) { $InfosTuto['mini_tuto_titre'] = '(vide)'; }
echo '<h1>'.htmlspecialchars($InfosTuto['mini_tuto_titre']).'</h1>';

//Saut rapide si besoin
if(isset($SautRapide))	echo $SautRapide;

//Saut de versions
echo $SautVersions;

//Introduction
echo '<h2>Introduction</h2>
<div>'.$view['messages']->parseSdz(trim($InfosTuto['mini_tuto_introduction'])).'</div>';

//Parties
if($parties){
	foreach ($parties as $p)
	{
		echo '<h2>'.htmlspecialchars($p['sous_partie_titre']).'</h2>';
		echo '<p>'.$view['messages']->parseSdz($p['sous_partie_texte']).'</p>';
	}
}
else{
	echo '<p>Il n\'y a aucune sous-partie dans ce tutoriel.</p>';
}

//Conclusion
echo '<h2>Conclusion</h2>
<div class="ccl">'.$view['messages']->parseSdz(trim($InfosTuto['mini_tuto_conclusion'])).'</div>';

//QCM
echo '<h2>QCM</h2>';
if (count($qcm) == 0)
{
	echo '<p>Ce tutoriel ne contient pas de QCM.</p>';
}
else
{
	foreach($qcm as $q)
	{
		echo '<div style="background-color: #fff; border: 1px dashed black; margin-bottom: 1em; padding: 0 0.5em;">
		<p>'.$view['messages']->parseSdz(trim($q['question_label'])).'</p>';
		echo '<ul>';
		foreach($q['reponses'] as $r)
		{
			echo '<li><p>'.htmlspecialchars($r['reponse_texte']).'</p></li>
			';
		}
		echo '</ul>';
		echo '<p>'.$view['messages']->parseSdz(trim($q['question_explications'])).'</p></div>';
	}
}

//Re-saut rapide si besoin
if(isset($SautRapide)) echo $SautRapide;

//Re-saut de versions
echo $SautVersions;
?>
