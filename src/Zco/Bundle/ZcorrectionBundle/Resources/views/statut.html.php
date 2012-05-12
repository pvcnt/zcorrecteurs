<?php $view->extend('::layouts/default.html.php') ?>
<?php $titre = htmlspecialchars($s[($s['soumission_type_tuto'] == MINI_TUTO ? 'mini' : 'big').'_tuto_titre']) ?>

<h1><?php echo $titre ?></h1>

<ul>
	<li><strong>Type de tutoriel :</strong>
		<?php echo ($s['soumission_type_tuto'] == MINI_TUTO ? 'Mini' : 'Big') ?>
	</li>
	<li><strong>Titre :</strong>
		<?php echo $titre ?>
	</li>
	<li>
		<strong>Envoi en zCorrection :</strong>
		<?php echo dateformat($s['soumission_date'], MAJUSCULE) ?>
	</li>
</ul>
<ul style="margin-top: 20px">
	<li><strong>État :</strong> <?php
		if ($s['soumission_etat'] == REFUSE)
		{
			$etat = 'refuse';
			echo 'Refusé';
		}
		elseif ($s['correction_abandonnee'] || !$s['correction_date_debut'])
		{
			$etat = 'attente_correction';
			echo 'En attente de correction';
		}
		elseif (!$s['correction_date_fin'])
		{
			$etat = 'correction';
			echo 'En cours de correction';
		}
		elseif ($s['soumission_recorrection'] && (!$s['recorrection_date_debut'] ||
		                                          $s['recorrection_abandonnee']))
		{
			$etat = 'attente_recorrection';
			echo 'En attente de recorrection';
		}
		elseif ($s['soumission_recorrection'] && !$s['recorrection_date_fin'])
		{
			$etat = 'recorrection';
			echo 'En cours de recorrection';
		}
		else
		{
			$etat = 'validation';
			echo 'Terminé';
		}
	?></li>

	<?php if (!$s['correction_abandonnee'] && $s['pseudo_correcteur'] && !$s['correcteur_invisible']): ?>
	<li><strong>Correcteur :</strong> <?php echo htmlspecialchars($s['pseudo_correcteur']) ?></li>
	<?php endif;

	if (!$s['recorrection_abandonnee'] && $s['pseudo_recorrecteur'] && !$s['recorrecteur_invisible']): ?>
	<li><strong>Recorrecteur :</strong> <?php echo htmlspecialchars($s['pseudo_recorrecteur']) ?></li>
	<?php endif ?>
</ul>

<?php $positions = array(
	'attente_correction'   => array(473,131),
	'correction'           => array(513,123,38),
	'attente_recorrection' => array(552,74),
	'recorrection'         => array(513,8,28),
	'validation'           => array(168,8,28)
) ?>

<?php if(isset($positions[$etat])): ?>
<div style="margin:auto;width:616px;height:328px;background-image:url(/img/zcorrection/processus.png)">
<?php if(count($positions[$etat]) === 2): /* Point */ ?>
	<img src="/img/zcorrection/tutoriel.png" alt="" style="position:relative;<?php
	echo 'left: '.$positions[$etat][0].'px; top: '.$positions[$etat][1].'px' ?>"/>
<?php else: /* Fond */ ?>
	<div style="background:green;opacity:0.3;position:relative;left:<?php
	echo $positions[$etat][0].'px;'
		.'top:'.$positions[$etat][1].'px;'
		.'width: 98px;'
		.'height:'.$positions[$etat][2].'px;' ?>"></div>
</div>
<?php endif; endif ?>
