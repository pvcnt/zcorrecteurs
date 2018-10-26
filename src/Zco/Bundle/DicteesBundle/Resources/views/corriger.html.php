<?php $view->extend('::layouts/default.html.php') ?>

<h1>Correction de la dictée</h1>
<?php
	$extra = '<img alt="" src="/bundles/zcodictees/img/img_correction.gif"/>';
	echo $view->render('ZcoDicteesBundle::_dictee.html.php', compact(
		'Dictee', 'DicteeEtats', 'DicteeDifficultes', 'extra'));
?>

<p class="gros centre">Votre note : <?php echo $note ?> / 20</p>

<?php
	$commentaires = array(
		/* 0 - 3 */   'Concentrez-vous un peu !',
		/* 4 - 7 */   'C\'est un peu faible…',
		/* 8 - 11 */  'Vous êtes proche de la moyenne ; vous pouvez très certainement progresser.',
		/* 12 - 15 */ 'C\'est bien, mais perfectible. Vous êtes sur la bonne voie.',
		/* 16 - 19 */ 'Vous approchez du sans-faute, encore quelques efforts !',
		/* 20 */      'Tout bonnement parfait. Félicitations !'
	);
	$nb = count($commentaires);
	$commentaire = $commentaires[(int)($note / 20 * ($nb - 1))];
?>

<p class="rmq information">Vous avez fait <?php echo $diff->fautes() ?> faute<?php echo pluriel($diff->fautes()) ?>.
<?php echo $commentaire ?></p>

<p class="italique"><a href="https://twitter.com/share?text=<?php echo urlencode('J\'ai fait '.$diff->fautes().' faute'.pluriel($diff->fautes()).' à cette dictée des @zCorrecteurs. Pourrez-vous faire mieux ? Relevez le défi ici : ') ?>&url=<?php echo URL_SITE ?>/dictees/dictee-<?php echo $Dictee->id ?>-<?php echo rewrite($Dictee->titre) ?>.html">
    <img src="/img/oiseau_16px.png" alt="Twitter" />
    Partager mon résultat sur Twitter
</a></p>

<?php if ($Dictee->commentaires): ?>
	<h2>Commentaires</h2>
	<p><?php echo $view['messages']->parse($Dictee->commentaires, array(
	    'core.anchor_prefix' => $Dictee['id'],
	    'files.entity_id' => $Dictee['id'],
	    'files.entity_class' => 'Dictee',
		'files.part' => 2,
	)) ?></p>
<?php endif ?>

<h2>Correction</h2>
<p><em>Les erreurs de typographie sont également affichées, mais n'entrent pas dans le total des fautes.</em></p>
<p class="dictee-correction"><?php echo nl2br($diff) ?></p>
