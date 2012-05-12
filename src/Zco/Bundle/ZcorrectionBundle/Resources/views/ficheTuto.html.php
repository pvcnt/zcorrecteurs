<?php $view->extend('::layouts/default.html.php') ?>

<?php if(verifier('zcorriger') || verifier('voir_tutos_correction') || verifier('voir_tutos_attente')){ ?>
<?php echo $view->render('ZcoZcorrectionBundle::_onglets.html.php', array('s' => $s)) ?>
<?php } ?>

<h1><?php echo $s['soumission_type_tuto'] == MINI_TUTO ? htmlspecialchars($s['mini_tuto_titre']) : htmlspecialchars($s['big_tuto_titre']); ?></h1>

<h2 id="soumission">Descriptif de la soumission</h2>
<fieldset>
	<legend>Informations générales sur le tutoriel</legend>
	<ul>
		<li><strong>Tutoriel soumis par le Validateur : </strong><a href="http://www.siteduzero.com/membres-294-<?php echo $s['valido_idsdz']; ?>.html"><?php echo htmlspecialchars($s['valido_pseudo']); ?></a></li>
		<li><strong>Tutoriel créé par : </strong><a href="http://www.siteduzero.com/membres-294-<?php echo $s['tutoteur_idsdz']; ?>.html"><?php echo htmlspecialchars($s['tutoteur_pseudo']); ?></a></li>
		<?php if(verifier('ips_analyser')){ ?>
		<li><strong>IP : </strong><a href="/ips/analyser.html?ip=<?php echo long2ip($s['soumission_ip']); ?>"><?php echo long2ip($s['soumission_ip']); ?></a> (l'iP du SdZ devrait être 80.248.219.123)</li>
		<?php } ?>
		<li><strong>Date d'envoi du tutoriel : </strong><?php echo dateformat($s['soumission_date']); ?></li>
		<li><strong>Titre du tutoriel envoyé : </strong><?php echo $s['soumission_type_tuto'] == MINI_TUTO ? htmlspecialchars($s['mini_tuto_titre']) : htmlspecialchars($s['big_tuto_titre']); ?></li>
		<li><strong>Message du Validateur aux zCorrecteurs : </strong><br />
			<span class="citation">Citation : <?php echo htmlspecialchars($s['valido_pseudo']); ?></span>
			<div class="citation2"><?php echo $view['messages']->parseSdz($s['soumission_description']); ?></div>
		</li>
	</ul>
</fieldset>

<fieldset>
	<legend>Le tutoriel</legend>
	<ul>
		<li><strong>Type de tutoriel : </strong><?php if($s['soumission_type_tuto'] == MINI_TUTO) echo 'mini'; else echo 'big'; ?>-tuto</li>
		<li><strong>Copie de sauvegarde du push : </strong><a href="/tutos/<?php echo $s['soumission_sauvegarde']; ?>">récupérer en .xml</a> (<?php echo round(@filesize(BASEPATH.'/web/tutos/'.$s['soumission_sauvegarde'])/1000, 2); ?> ko)</li>
		<?php if(verifier('zcorriger')){ ?>
		<li><a href="exporter-<?php echo $s['soumission_id']; ?>.html">Exporter la dernière version du tutoriel</a></li>
		<li><strong>Prioritaire : </strong><?php echo $s['soumission_prioritaire'] ? 'oui' : 'non'; ?>
			<?php if(verifier('zcorrection_priorite') && empty($s['recorrection_date_fin'])){ ?>
			<ul><li><img src="/img/zcorrection/<?php if($s['soumission_prioritaire']) echo 'non'; ?>prioritaire.png" alt="" /> <a href="gestion.html?<?php if($s['soumission_prioritaire']) echo 'non'; ?>prioritaire=<?php echo $_GET['id']; ?>">Changer la priorité</a></li></ul>
			<?php } ?>
		</li>
		<?php } ?>
		<?php if(verifier('zcorrection_supprimer')){ ?>
		<li><img src="/img/supprimer.png" alt="" /> <a href="gestion.html?supprimer=<?php echo $_GET['id']; ?>">Supprimer la soumission</a></li>
		<?php } ?>
		<?php if($s['correction_date_debut'] != '0000-00-00 00:00:00' && verifier('zcorriger')){ ?>
		<li><strong>Commentaires du correcteur au recorrecteur : </strong><br />
		<span class="citation">Citation</span>
		<div class="citation2"><?php echo $view['messages']->parseSdz($s['soumission_commentaire']); ?></div></li>
		<?php } ?>
	</ul>
</fieldset>

<h2 id="corrections">Descriptif des corrections</h2>

<fieldset>
	<legend>En attente</legend>
	<?php if (verifier('zcorriger') || verifier('voir_tutos_attente') || verifier('voir_tutos_corriges') || verifier('voir_tutos_correction')){ ?>
	<ul>
		<?php if (verifier('zcorriger') || verifier('voir_tutos_attente') || verifier('voir_tutos_corriges') || verifier('voir_tutos_correction')){ ?>
		<li><img src="/img/zcorrection/voir.png" alt="" /> <a href="/zcorrection/voir-<?php if($s['soumission_type_tuto'] == MINI_TUTO) echo 'mini'; else echo 'big'; ?>-tuto-<?php echo $s['soumission_id_tuto']; ?>.html?cid=<?php echo $_GET['id']; ?>">Voir le tutoriel envoyé</a></li>
		<?php } ?>
	</ul>
	<?php } else{ ?>
	<p>Aucune action n'est disponible.</p>
	<?php } ?>
</fieldset>

<fieldset>
	<legend>Correction</legend>
	<?php if(!empty($s['id_correcteur'])){ ?>
	<ul>
		<?php if(!empty($s['correction_date_debut'])){ ?>
		<li><strong>Correction débutée : </strong><?php echo dateformat($s['correction_date_debut']); ?></li>
		<?php } if(!empty($s['correction_date_fin'])){ ?>
		<li><strong>Correction achevée : </strong><?php echo dateformat($s['correction_date_fin']); ?></li>
		<?php } ?>
		<?php if((((!empty($s['correction_date_fin']) && $s['soumission_recorrection'] == 0) || (!empty($s['recorrection_date_fin']) && $s['soumission_recorrection'] == 1)) && !$s['correcteur_invisible_correction']) || verifier('zcorriger')){ ?>
		<li><strong>Correcteur : </strong><a href="/membres/profil-<?php echo $s['id_correcteur']; ?>-<?php echo rewrite($s['pseudo_correcteur']); ?>.html"><?php echo htmlspecialchars($s['pseudo_correcteur']); ?></a> <?php if($s['correcteur_invisible_correction']) echo '(pseudo caché)'; ?></li>
		<?php } ?>
		<?php if(verifier('zcorriger')){ ?>
			<li><img src="/img/zcorrection/voir.png" alt="" /> <a href="voir-<?php if($s['soumission_type_tuto'] == MINI_TUTO) echo 'mini'; else echo 'big'; ?>-tuto-<?php echo $s['id_tuto_correction']; ?>.html?cid=<?php echo $_GET['id']; ?>">Voir le tuto en correction</a></li>
		<?php } ?>
		<?php if((verifier('zcorrection_editer_tutos') || $s['id_correcteur'] == $_SESSION['id']) && empty($s['correction_date_fin'])){ ?>
		<li><img src="/img/editer.png" alt="" /> <a href="corriger-<?php echo $_GET['id']; ?>.html">Éditer le tutoriel en correction</a></li>
		<?php } ?>
		<?php if(((!empty($s['correction_date_fin']) && $s['soumission_recorrection'] == 0) || (!empty($s['recorrection_date_fin']) && $s['soumission_recorrection'] == 1)) || verifier('zcorriger')){ ?>
		<li><strong>Commentaires du correcteur : </strong><br />
		<span class="citation">Citation <?php if(!$s['correcteur_invisible_correction'] || verifier('zcorriger')) echo ': '.htmlspecialchars($s['pseudo_correcteur']); ?></span>
		<div class="citation2"><?php echo $view['messages']->parseSdz($s['commentaire_correction']); ?></div></li>
		<?php } ?>
	</ul>
	<?php } else{ ?>
	<p>Aucune correction n'a été effectuée.</p>
	<?php } ?>
</fieldset>

<fieldset>
	<legend>Recorrection</legend>
	<?php if(!empty($s['id_recorrecteur'])){ ?>
	<ul>
		<?php if(!empty($s['recorrection_date_debut'])){ ?>
		<li><strong>Recorrection débutée : </strong><?php echo dateformat($s['recorrection_date_debut']); ?></li>
		<?php } if(!empty($s['recorrection_date_fin'])){ ?>
		<li><strong>Recorrection achevée : </strong><?php echo dateformat($s['recorrection_date_fin']); ?></li>
		<?php } ?>
		<?php if((((!empty($s['correction_date_fin']) && $s['soumission_recorrection'] == 0) || (!empty($s['recorrection_date_fin']) && $s['soumission_recorrection'] == 1)) && !$s['correcteur_invisible_recorrection']) || verifier('zcorriger')){ ?>
		<li><strong>Recorrecteur : </strong><a href="/membres/profil-<?php echo $s['id_recorrecteur']; ?>-<?php echo rewrite($s['pseudo_recorrecteur']); ?>.html"><?php echo htmlspecialchars($s['pseudo_recorrecteur']); ?></a> <?php if($s['correcteur_invisible_recorrection']) echo '(pseudo caché)'; ?></li>
		<?php } ?>
		<?php if(verifier('zcorriger')){ ?>
		<li><img src="/img/zcorrection/voir.png" alt="" /> <a href="voir-<?php if($s['soumission_type_tuto'] == MINI_TUTO) echo 'mini'; else echo 'big'; ?>-tuto-<?php echo $s['id_tuto_recorrection']; ?>.html?cid=<?php echo $_GET['id']; ?>">Voir le tuto en recorrection</a></li>
		<?php }?>
		<?php if((verifier('zcorrection_editer_tutos') || $s['id_recorrecteur'] == $_SESSION['id']) && empty($s['recorrection_date_fin'])){ ?>
		<li><img src="/img/editer.png" alt="" /> <a href="corriger-<?php echo $_GET['id']; ?>.html">Editer le tutoriel en recorrection</a></li>
		<?php } ?>
		<?php if(((!empty($s['correction_date_fin']) && $s['soumission_recorrection'] == 0) || (!empty($s['recorrection_date_fin']) && $s['soumission_recorrection'] == 1)) || verifier('zcorriger')){ ?>
		<li><strong>Commentaires du recorrecteur : </strong><br />
		<span class="citation">Citation <?php if(!$s['correcteur_invisible_recorrection'] || verifier('zcorriger')) echo ': '.htmlspecialchars($s['pseudo_recorrecteur']); ?></span>
		<div class="citation2"><?php echo $view['messages']->parseSdz($s['commentaire_recorrection']); ?></div></li>
		<?php } ?>
	</ul>
	<?php } else{ ?>
	<p>Aucune recorrection n'a été effectuée.</p>
	<?php } ?>
</fieldset>

<fieldset>
	<legend>Fin de correction</legend>
	<?php if((!empty($s['correction_date_fin']) && $s['soumission_recorrection'] == 0) || (!empty($s['recorrection_date_fin']) && $s['soumission_recorrection'] == 1)){ ?>
	<ul>
		<?php if (verifier('zcorriger') || verifier('voir_tutos_attente') || verifier('voir_tutos_corriges') || verifier('voir_tutos_correction')){ ?>
		<li><img src="/img/zcorrection/voir.png" alt="" /> <a href="/zcorrection/voir-<?php if($s['soumission_type_tuto'] == MINI_TUTO) echo 'mini'; else echo 'big'; ?>-tuto-<?php echo $s['soumission_recorrection'] == 1 ? $s['id_tuto_recorrection'] : $s['id_tuto_correction']; ?>.html">Voir le tutoriel corrigé</a></li>
		<?php } ?>
		<li><img src="/img/zcorrection/exporter.png" alt="" /> <a href="/zcorrection/exporter-<?php echo $_GET['id']; ?>.html">Exporter le tutoriel</a></li>
	</ul>
	<?php } else{ ?>
	<p>La correction n'est pas encore terminée.</p>
	<?php } ?>
</fieldset>
