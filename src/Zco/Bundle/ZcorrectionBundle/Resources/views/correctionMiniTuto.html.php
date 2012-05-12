<?php $view->extend('::layouts/default.html.php') ?>

<?php /* Ce fichier sert à réaliser la correction d'un mini-tuto (non contenu dans un big-tuto) */
if (empty($InfosTuto['mini_tuto_titre'])) { $InfosTuto['mini_tuto_titre'] = '(vide)'; }
?>

<?php if(!empty($zco_mark)){ ?>
<?php afficher_message('<a href="'.$zco_mark.'"><img src="/img/zcorrection/marque.png" alt="" /> Vous aviez laissé une marque de correction.</a>'); ?>
<?php } ?>

<?php echo $view->render('ZcoZcorrectionBundle::_onglets.html.php', array('s' => $s)) ?>

<?php
echo '<h1 id="titre-mini-'.$InfosTuto['mini_tuto_id'].'">'.str_replace('&amp;euro;', '&euro;', htmlspecialchars($InfosTuto['mini_tuto_titre'])).'</h1>';

echo '<a class="zco_marque" href="#" onclick="return drop_zco_mark('.($s['recorrection_id'] ? $s['recorrection_id'] : $s['correction_id']).', \'texte-mini-intro-'.$InfosTuto['mini_tuto_id'].'\');" title="Déposer la marque de correction ici"><img src="/img/zcorrection/marque.png" alt="Laisser la marque de correction" /></a>';
echo '<h2>Introduction</h2>
<div id="texte-mini-intro-'.$InfosTuto['mini_tuto_id'].'" onMouseOver="this.className = \'hover\';" onMouseOut="this.className = \'\';">'.str_replace('&amp;euro;', '&euro;', $view['messages']->parseSdz(trim($InfosTuto['mini_tuto_introduction']))).'</div>';

if($parties)
{
	foreach ($parties as $p)
	{
		echo '<h2 id="titre-sous_partie-'.$p['sous_partie_id'].'">'.str_replace('&amp;euro;', '&euro;', htmlspecialchars($p['sous_partie_titre'])).'</h2>';
		preg_match_all('`<titre([12])\>(.+?)\<\/titre(?:\1)\>`', $p['sous_partie_texte'], $pma);
		$pos = array(0);
		$i = 0;
		foreach($pma[0] as $v) {
			$pos[] = mb_strpos($p['sous_partie_texte'], $v, $pos[$i++]);
		}
		$pos[] = mb_strlen($p['sous_partie_texte']);
		$paragraphes = array();

		foreach($pos as $k=>$v) {
			if (isset($pos[$k+1])) {
				$paragraphes[] = mb_substr($p['sous_partie_texte'], $v, $pos[$k+1]-$v);
			}
		}
		/*
		$paragraphes = preg_split('`(\r\n|\n|\r){3,}`', trim($p['sous_partie_texte']));
		// */
		$paragraphes = array_map('parse', array_map('trim', $paragraphes));
		foreach($paragraphes as $num=>$pp)
		{
			echo '<a class="zco_marque" href="#" onclick="return drop_zco_mark('.($s['recorrection_id'] ? $s['recorrection_id'] : $s['correction_id']).', \'texte-sous_partie-'.$p['sous_partie_id'].'-'.$num.'\');" title="Déposer la marque de correction ici"><img src="/img/zcorrection/marque.png" alt="Laisser la marque de correction" /></a>';
			echo '<div class="p" id="texte-sous_partie-'.$p['sous_partie_id'].'-'.$num.'" onMouseOver="this.className = \'hover\';" onMouseOut="this.className = \'\';">'.str_replace('&amp;euro;', '&euro;', $pp).'</div>';
		}
	}
}
else
{
	echo '<p>Il n\'y a aucune sous-partie dans ce tutoriel.</p>';
}

echo '<h2>Conclusion</h2>
<a class="zco_marque" href="#" onclick="return drop_zco_mark('.($s['recorrection_id'] ? $s['recorrection_id'] : $s['correction_id']).', \'texte-mini-ccl-'.$InfosTuto['mini_tuto_id'].'\');" title="Déposer la marque de correction ici"><img src="/img/zcorrection/marque.png" alt="Laisser la marque de correction" /></a>
<div class="ccl" id="texte-mini-ccl-'.$InfosTuto['mini_tuto_id'].'" onMouseOver="this.className = \'hover\';" onMouseOut="this.className = \'\';">'.str_replace('&amp;euro;', '&euro;', $view['messages']->parseSdz(trim($InfosTuto['mini_tuto_conclusion']))).'</div>';
echo '<h2>QCM</h2>';
if (count($qcm) == 0)
{
	echo '<p>Ce tutoriel ne contient pas de QCM.</p>';
}
else
{
	foreach($qcm as $q)
	{
		echo '<a class="zco_marque" href="#" onclick="return drop_zco_mark('.($s['recorrection_id'] ? $s['recorrection_id'] : $s['correction_id']).', \'texte-qcm-question-'.$q['question_id'].'\');" title="Déposer la marque de correction ici"><img src="/img/zcorrection/marque.png" alt="Laisser la marque de correction" /></a>';
		echo '<div style="background-color: #fff; border: 1px dashed black; margin-bottom: 1em; padding: 0 0.5em;">
		<p id="texte-qcm-question-'.$q['question_id'].'" onMouseOver="this.className = \'hover\';" onMouseOut="this.className = \'\';">'.str_replace('&amp;euro;', '&euro;', $view['messages']->parseSdz(trim($q['question_label']))).'</p>';
		echo '<ul>';
		foreach($q['reponses'] as $r)
		{
			echo '<li><p id="titre-reponse-'.$r['reponse_id'].'">'.str_replace('&amp;euro;', '&euro;', htmlspecialchars($r['reponse_texte'])).'</p></li>
			';
		}
		echo '</ul>';
		echo '<p id="texte-qcm-explication-'.$q['question_id'].'" onMouseOver="this.className = \'hover\';" onMouseOut="this.className = \'\';">'.str_replace('&amp;euro;', '&euro;', $view['messages']->parseSdz(trim($q['question_explications']))).'</p></div>';
	}
}
?>

<?php echo $view->render('ZcoZcorrectionBundle::_form.html.php') ?>
