<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoRecrutementBundle::_onglets.html.php') ?>

<h1>Candidature de <?php echo htmlspecialchars($InfosCandidature['utilisateur_pseudo']); ?></h1>

<?php echo $view->render('ZcoRecrutementBundle::_saut_rapide.html.php', array('CandidaturePrecedente' => $CandidaturePrecedente, 'CandidatureSuivante' => $CandidatureSuivante, 'IdRecrutement' => $InfosCandidature['recrutement_id'])); ?>

<p class="centre">
	<a href="#motivation">Lettre de motivation</a>
	<?php if(!empty($InfosCandidature['candidature_redaction'])) { ?>
	| <a href="#redaction">Rédaction</a>
	<?php } if($InfosCandidature['candidature_quiz_score'] !== NULL) { ?>
	| <a href="#quiz_score">Score au questionnaire</a>
	<?php } if(verifier('recrutements_voir_tests') &&
in_array($InfosCandidature['candidature_test_type'], array(TEST_TEXTE, TEST_TUTO, TEST_DEFAUT)) &&
in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ATTENTE_TEST, CANDIDATURE_TESTE, CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE, CANDIDATURE_DESISTE))){ ?>
	| <a href="#test">Test de correction</a>
	<?php } if(verifier('recrutements_voir_commentaire') &&
in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE))){ ?>
	| <a href="#commentaire">Réponse de l'administrateur</a>
	<?php } if(($InfosCandidature['recrutement_etat'] != RECRUTEMENT_FINI &&
		    verifier('recrutements_voir_shoutbox'))
	        || ($InfosCandidature['recrutement_etat'] == RECRUTEMENT_FINI &&
        	    verifier('recrutements_termines_voir_shoutbox'))) { ?>
	| <a href="#shoutbox">Commentaires privés</a>
	<?php } ?>
</p>

<?php $c_etats = array(CANDIDATURE_ENVOYE => 'en attente',
		CANDIDATURE_REDACTION => 'en rédaction',
		CANDIDATURE_ACCEPTE => 'accepté',
		CANDIDATURE_ATTENTE_TEST => 'en test',
		CANDIDATURE_TESTE=> 'testé',
		CANDIDATURE_REFUSE => 'refusé',
		CANDIDATURE_DESISTE => 'désistement'); ?>

<div class="UI_column_menu" style="width: 25%;">
	<div class="box">
		<ul>
			<li>
				<strong>Pseudo : </strong>
				<?php if(!empty($InfosCandidature['utilisateur_id'])){ ?>
				<a href="/membres/profil-<?php echo $InfosCandidature['utilisateur_id']; ?>-<?php echo rewrite($InfosCandidature['utilisateur_pseudo']); ?>.html">
					<?php echo htmlspecialchars($InfosCandidature['utilisateur_pseudo']); ?>
				</a>
				<?php } elseif(!empty($InfosCandidature['candidature_pseudo'])){ ?>
				<?php echo htmlspecialchars($InfosCandidature['candidature_pseudo']); ?>
				<?php } ?>
			</li>
			<li>
				<strong>Recrutement concerné : </strong>
				<a href="recrutement-<?php echo $InfosCandidature['recrutement_id']; ?>-<?php echo rewrite($InfosCandidature['recrutement_nom']); ?>.html">
					<?php echo htmlspecialchars($InfosCandidature['recrutement_nom']); ?>
				</a>
			</li>
			<li>
				<strong>État : </strong>
				<?php echo $c_etats[$InfosCandidature['candidature_etat']]; ?>
				<?php if(in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_REFUSE, CANDIDATURE_ACCEPTE))){ ?>
				(<a href="/membres/profil-<?php echo $InfosCandidature['id_admin']; ?>-<?php echo rewrite($InfosCandidature['pseudo_admin']); ?>.html" style="color: <?php echo $InfosCandidature['groupe_admin']; ?>;"><?php echo htmlspecialchars($InfosCandidature['pseudo_admin']); ?></a>,
				<?php echo dateformat($InfosCandidature['candidature_date_reponse'], MINUSCULE); ?>)
				<?php } ?>
			</li>
			<?php if (!empty($InfosCandidature['candidature_correcteur_note'])){ ?>
			<li>
				<strong>Note :</strong>
				<?php echo $view['humanize']->numberformat($InfosCandidature['candidature_correcteur_note']) ?> %
				(par <?php echo '<a href="/membres/profil-'.$InfosCandidature['id_correcteur'].'-'.rewrite($InfosCandidature['pseudo_correcteur']).'.html">'.htmlspecialchars($InfosCandidature['pseudo_correcteur']).'</a>' ?>)
			</li>
			<?php } ?><br />

			<li>
				<strong>Date de dépôt : </strong>
				<?php echo dateformat($InfosCandidature['candidature_date'], MINUSCULE); ?>
			</li>
			<?php if(verifier('recrutements_voir_tests') && in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_TESTE, CANDIDATURE_ATTENTE_TEST, CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE))){ ?>
			<li>
				<strong>Date de lancement du test : </strong>
				<?php echo dateformat($InfosCandidature['candidature_date_debut_correction'], MINUSCULE); ?>
			</li>
			<li>
				<strong>Date limite de correction du test : </strong>
				<?php echo dateformat($InfosCandidature['candidature_date_fin_correction'], MINUSCULE); ?>
			</li>
			<?php } if(verifier('recrutements_voir_tests') && in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_TESTE, CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE))){ ?>
			<li>
				<strong>Date de correction du test : </strong>
				<?php echo dateformat($InfosCandidature['candidature_date_correction'], MINUSCULE); ?>
			</li>
			<?php } ?>
		</ul>
	</div>

	<?php if((verifier('recrutements_repondre') && in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ENVOYE, CANDIDATURE_TESTE, CANDIDATURE_ATTENTE_TEST))) || verifier('recrutements_supprimer_candidatures') || (verifier('recrutements_attribuer_copie') && in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ATTENTE_TEST, CANDIDATURE_TESTE)) && $InfosCandidature['candidature_correcteur'] == $_SESSION['id'] && empty($InfosCandidature['candidature_correcteur_note']))){ ?>
	<div class="box">
		<ul>
			<?php if(verifier('recrutements_attribuer_copie') && in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ATTENTE_TEST, CANDIDATURE_TESTE)) && $InfosCandidature['candidature_correcteur'] == $_SESSION['id'] && empty($InfosCandidature['candidature_correcteur_note'])){ ?>
			<li>
				<img src="/img/recrutement/corriger.png" alt="" />
				<a href="noter-<?php echo $_GET['id']; ?>.html">
					Noter le test
				</a>
			</li>
			<?php } if(verifier('recrutements_repondre') && in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ENVOYE, CANDIDATURE_TESTE))){ ?>
			<li>
				<img src="/img/recrutement/repondre.png" alt="" />
				<a href="repondre-<?php echo $_GET['id']; ?>.html">
					Répondre à la candidature
				</a>
			</li>
			<?php } if(verifier('recrutements_repondre') && $InfosCandidature['candidature_etat'] != CANDIDATURE_REDACTION){ ?>
			<li>
				<img src="/img/editer.png" alt="" />
				<a href="editer-candidature-<?php echo $_GET['id']; ?>.html">
					Modifier la candidature
				</a>
			</li>
			<?php } if(verifier('recrutements_supprimer_candidatures')){ ?>
			<li>
				<img src="/img/supprimer.png" alt="" />
				<a href="supprimer-candidature-<?php echo $_GET['id']; ?>.html">
					Supprimer la candidature
				</a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
</div>

<div class="UI_column_text" style="width: 75%;">
<?php
if(verifier('recrutements_avis'))
{
	$donnees = array();
	for($i = 0; $i < count(\RecrutementAvis::VALUES); $i++)
	{
		$donnees[$i]['nom'] = \RecrutementAvis::VALUES[$i]['nom'];
		$donnees[$i]['couleur'] = \RecrutementAvis::VALUES[$i]['couleur'];
		if(array_key_exists($i, $avis))
		{
			$donnees[$i]['votes'] = $avis[$i];
		}
		else
		{
			$donnees[$i]['votes'] = 0;
		}
	}
?>
<div style="float: left; margin-right: 10px;" class="UI_box">
<form method="post" action="candidature-<?php echo $_GET['id']; ?>.html">
<p>
<?php
	$i = 0;
	if(is_numeric($resultat))
		echo '<input type="hidden" name="act" value="update" />';
	else
		echo '<input type="hidden" name="act" value="insert" />';
	foreach($donnees as $donnee)
	{
		if(is_numeric($resultat))
			echo '<input type="radio" name="type" id="' . $i . '" value="' . $i . '"' . (((int)$resultat == $i) ? ' checked="checked"' : '') . '><label for="' . $i . '" class="nofloat"><strong> <span style="color: ' . $donnee['couleur'] . ';">' . $donnee['nom'] . ' :</strong> ' . $donnee['votes'] . '</span></label></input><br />';
		else
			echo '<input type="radio" name="type" id="' . $i . '" value="' . $i . '"><label for="' . $i . '" class="nofloat"><strong> <span style="color: ' . $donnee['couleur'] . ';">' . $donnee['nom'] . ' :</strong> ' . $donnee['votes'] . '</span></label></input><br />';
		$i++;
	}
?>
</p>
<input type="submit" value="Donner / modifier mon avis" />
</form></div>
<?php
}
?>
<h2 id="motivation">Texte de motivation</h2>
<p style="min-height: 120px;"><?php echo $view['messages']->parse($InfosCandidature['candidature_texte']); ?></p>

<?php if(!empty($InfosCandidature['candidature_redaction'])) { ?><br />
	<h2 id="redaction">Rédaction</h2>
	<p><?php echo $view['messages']->parse($InfosCandidature['candidature_redaction']); ?></p>
<?php } ?>

<?php if($InfosCandidature['candidature_quiz_score'] !== NULL) { ?><br />
	<h2 id="quiz_score">Score au questionnaire</h2>
	<p>
		Le candidat a répondu au questionnaire à choix multiples qui lui était proposé
		et a obtenu un score de
		<strong><?php echo $InfosCandidature['candidature_quiz_score'] ?>/20</strong>.
		<?php $diff = $view->get('humanize')->datediff(
				strtotime($InfosCandidature['candidature_quiz_fin']),
				strtotime($InfosCandidature['candidature_quiz_debut'])); ?>
		Il lui a fallu <strong><?php echo $diff ?></strong> pour répondre au quiz.
	</p>
	<?php echo $view->render('ZcoRecrutementBundle::_quiz.html.php', compact('questions')) ?>
<?php } ?>
</div>

<?php if(verifier('recrutements_voir_tests') &&
in_array($InfosCandidature['candidature_test_type'], array(TEST_TEXTE, TEST_TUTO, TEST_DEFAUT)) &&
in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ATTENTE_TEST, CANDIDATURE_TESTE, CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE, CANDIDATURE_DESISTE))){ ?><br />
<h2 id="test">Test de correction</h2>

<?php if($InfosCandidature['candidature_test_type'] == TEST_TEXTE){ ?>
<h3>Original</h3>
<p><?php echo $view['messages']->parse($InfosCandidature['candidature_correction_original']); ?></p>

<?php if(in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_TESTE, CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE, CANDIDATURE_DESISTE)) &&
!empty($InfosCandidature['candidature_correction_corrige'])){ ?>
<h3>Corrigé</h3>
<p><?php echo $view['messages']->parse($InfosCandidature['candidature_correction_corrige']); ?></p>
<?php } ?>

<?php } else if($InfosCandidature['candidature_test_type'] == TEST_TUTO){ ?>
<ul>
	<li><a href="/tutos/recrutement/originaux/<?php echo $InfosCandidature['candidature_test_tuto']; ?>">
		Récupérer le tutoriel original
	</a></li>
	<?php if(in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_TESTE, CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE, CANDIDATURE_DESISTE)) &&
	is_file(BASEPATH.'/web/tutos/recrutement/corrections/'.$InfosCandidature['candidature_test_tuto'])){ ?>
	<li><a href="/tutos/recrutement/corrections/<?php echo $InfosCandidature['candidature_test_tuto']; ?>">
		Récupérer le tutoriel corrigé
	</a></li>
	<?php } ?>
</ul>
<?php } else if($InfosCandidature['candidature_test_type'] == TEST_DEFAUT){ ?>
<ul>
	<li><a href="/tutos/recrutement/originaux/0101010101.tuto">
		Récupérer le tutoriel original
	</a></li>
	<li><a href="/tutos/recrutement/originaux/0101010101.txt">
		Récupérer le texte littéraire original
	</a></li>
	<?php if(in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_TESTE, CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE, CANDIDATURE_DESISTE)) &&
	is_file(BASEPATH.'/web/tutos/recrutement/corrections/'.$InfosCandidature['recrutement_id'].'_'.$InfosCandidature['utilisateur_id'].'_'.$InfosCandidature['candidature_test_tuto'])){ ?>
	<li><a href="/tutos/recrutement/corrections/<?php echo $InfosCandidature['recrutement_id'].'_'.$InfosCandidature['utilisateur_id'].'_'.$InfosCandidature['candidature_test_tuto']; ?>">
		Récupérer le tutoriel corrigé
	</a></li>
	<?php } ?>
	<?php if(in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_TESTE, CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE, CANDIDATURE_DESISTE)) &&
	is_file(BASEPATH.'/web/tutos/recrutement/corrections/'.$InfosCandidature['recrutement_id'].'_'.$InfosCandidature['utilisateur_id'].'_'.$InfosCandidature['candidature_test_texte'])){ ?>
	<li><a href="/tutos/recrutement/corrections/<?php echo $InfosCandidature['recrutement_id'].'_'.$InfosCandidature['utilisateur_id'].'_'.$InfosCandidature['candidature_test_texte']; ?>">
		Récupérer le texte littéraire corrigé
	</a></li>
	<?php } ?>
</ul>
<?php } ?>

<?php if(!empty($InfosCandidature['candidature_correction_note'])) { ?><br />
<h3>Notes laissées aux correcteurs par le candidat</h3>
<p><?php echo $view['messages']->parse($InfosCandidature['candidature_correction_note']); ?></p>
<?php } } ?>

<?php if(verifier('recrutements_voir_commentaire') &&
in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE))){ ?><br />
<h2 id="commentaire">Réponse de l'administrateur au candidat</h2>
<p><?php echo $view['messages']->parse($InfosCandidature['candidature_commentaire']); ?></p>
<?php } ?>

<br />
<div style="clear:right;"> </div>
<?php echo $view->render('ZcoRecrutementBundle::_saut_rapide.html.php', array('CandidaturePrecedente' => $CandidaturePrecedente, 'CandidatureSuivante' => $CandidatureSuivante, 'IdRecrutement' => $InfosCandidature['recrutement_id'])); ?>

<?php if(($InfosCandidature['recrutement_etat'] != RECRUTEMENT_FINI &&
          verifier('recrutements_voir_shoutbox'))
      || ($InfosCandidature['recrutement_etat'] == RECRUTEMENT_FINI &&
          verifier('recrutements_termines_voir_shoutbox'))) { ?>
<h2 id="shoutbox">Commentaires privés</h2>
	<?php if(verifier('recrutements_ecrire_shoutbox')) { ?>
	<p style="text-align:right;">
		<a href="ajouter-message-<?php echo $_GET['id'];?>.html">
			<img src="/bundles/zcoforum/img/nouveau.png" alt="Ajouter un commentaire" />
		</a>
	</p>
	<?php if($ListerCommentaires): ?>
	<table class="UI_items messages">
		<thead>
			<tr>
				<td colspan="2">Page : <?php foreach($ListePage as $p) { echo $p; } ?></td>
			</tr>
			<tr>
				<th style="width: 13%;">Auteur</th>
				<th style="width: 87%;">Message</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">Page : <?php foreach($ListePage as $p) { echo $p; } ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach($ListerCommentaires as &$valeur): ?>
			<tr class="header_message">
				<td class="pseudo_membre">
					<a href="/membres/profil-<?php echo $valeur['utilisateur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html" style="color: <?php echo $valeur['groupe_class']; ?>;">
						<?php echo htmlspecialchars($valeur['utilisateur_pseudo']); ?>
					</a>
				</td>
				<td class="dates">
					<span id="c<?php echo $valeur['commentaire_id'];?>"><a href="candidature-<?php echo $InfosCandidature['candidature_id'];?>-<?php echo $valeur['commentaire_id'];?>.html" rel="nofollow">#</a></span>
					Posté <?php echo dateformat($valeur['commentaire_date'], MINUSCULE);
					if(verifier('recrutements_ecrire_shoutbox')): ?>
					<a href="ajouter-message-<?php echo $_GET['id']; ?>-<?php echo $valeur['commentaire_id'];?>.html"><img src="/img/citer.png" alt="Citer"></a>
					<a href="editer-message-<?php echo $valeur['commentaire_id']; ?>.html"><img src="/img/editer.png" alt="Éditer"></a>
					<a href="supprimer-message-<?php echo $valeur['commentaire_id']; ?>.html"><img src="/img/supprimer.png" alt="Supprimer"></a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="infos_membre">
					<?php
					if(!empty($valeur['utilisateur_citation'])) echo htmlspecialchars($valeur['utilisateur_citation']).'<br />';
					if(!empty($valeur['utilisateur_avatar'])): ?>
						<a href="/membres/profil-<?php echo $valeur['utilisateur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html"><img src="/uploads/avatars/<?php echo $valeur['utilisateur_avatar']; ?>" alt="<?php echo htmlspecialchars($valeur['utilisateur_pseudo']); ?>" /></a><br />
					<?php endif; ?>
					<?php echo $view->get('messages')->afficherGroupe($valeur) ?><br/>

					<?php if(!empty($valeur['utilisateur_titre']))
						echo htmlspecialchars($valeur['utilisateur_titre']);
					?>
				</td>
				<td class="message">
					<div class="msgbox">
						<?php echo preg_replace('`&amp;#(\d+);`', '&#$1;', $view['messages']->parse($valeur['commentaire_texte']));
						if(!empty($valeur['utilisateur_signature'])): ?>
							<div class="signature"><hr />
							<?php echo $view['messages']->parse($valeur['utilisateur_signature']); ?>
							</div>
						<?php endif; ?>
						<div class="cleaner">&nbsp;</div>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php else: ?>
	<p><em>Il n'y a pas encore de commentaires sur cette candidature.</em></p>
	<?php endif; ?>

	<?php } if(verifier('recrutements_ecrire_shoutbox') && $NombreDeCommentaires > 0) { ?>
		<p style="text-align:right;"><a href="ajouter-message-<?php echo $_GET['id'];?>.html"><img src="/bundles/zcoforum/img/nouveau.png" alt="Ajouter un commentaire" /></a></p>
	<?php } ?>
<?php } ?>
