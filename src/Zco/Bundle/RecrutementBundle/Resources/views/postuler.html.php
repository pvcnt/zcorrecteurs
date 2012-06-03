<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoRecrutementBundle::_onglets.html.php') ?>

<h1>Postuler <small><?php echo htmlspecialchars($InfosRecrutement['recrutement_nom']) ?></small></h1>

<?php /* Première fois */ if(empty($InfosCandidature)){ ?>
<p class="rmq information bold" style="font-size: 13px;">
	Avant toute chose, assurez-vous d'avoir bien lu
	<a href="recrutement-<?php echo $_GET['id']; ?>-<?php echo rewrite($InfosRecrutement['recrutement_nom']); ?>.html">
		les consignes du recrutement
	</a> auquel vous vous apprêtez à postuler.
</p>

<p>
	La première étape pour postuler est de fournir un court texte de motivation.
	Des consignes plus précises vous ont été données précédemment. Vous pouvez
	rédiger tranquillement votre texte. Il est enregistré en brouillon en
	attendant son envoi définitif. Ce que vous allez rédiger maintenant ne sera
	donc <strong>pas envoyé immédiatement aux administrateurs</strong>. Prenez
	le temps de soigner votre orthographe, syntaxe, etc.
	
	<?php if (!empty($InfosRecrutement['recrutement_date_fin_depot'])): ?><br />
	Nous vous rappelons que la date limite d'envoi pour ce recrutement est
	<strong><?php echo dateformat($InfosRecrutement['recrutement_date_fin_depot'], MINUSCULE); ?></strong>.
	<?php endif; ?>
</p>

<form method="post" action="" class="form-horizontal">
	<?php if (!empty($InfosRecrutement['recrutement_id_quiz'])): ?>
		<p class="center">
			La réponse à <strong>un questionnaire de langue française</strong>
			a été requise pour tous les candidats. Vous pourrez y répondre dès
			que vous aurez <strong>enregistré votre premier brouillon</strong>
			de lettre de motivation.
		</p><br />
	<?php endif; ?>

	<div class="control-group">
		<label for="texte" class="control-label">Texte de motivation</label>
		<div class="controls">
			<div class="zform">
				<div class="zform-wrapper">
					<textarea name="texte" id="texte"></textarea>
				</div>
				<div class="zform-preview-area zform-invisible"></div>
			</div>
			<?php $view['javelin']->initBehavior('zform', array('id' => 'texte')) ?>
			<?php $view['javelin']->initBehavior('resizable-textarea', array('id' =>'texte')) ?>
		</div>
	</div>
	
	<?php if ($InfosRecrutement['recrutement_redaction']){ ?>
	<div class="control-group">
		<label for="redaction" class="control-label">Rédaction</label>
		<div class="controls">
			<div class="zform">
				<div class="zform-wrapper">
					<textarea name="redaction" id="redaction"></textarea>
				</div>
				<div class="zform-preview-area zform-invisible"></div>
			</div>
			<?php $view['javelin']->initBehavior('zform', array('id' => 'redaction')) ?>
			<?php $view['javelin']->initBehavior('resizable-textarea', array('id' =>'redaction')) ?>
		</div>
	</div>
	<?php } ?>
	<?php $view['javelin']->initBehavior('squeezebox', array('selector' => '.zform-squeezebox-link', 'options' => array('handler' => 'iframe'))) ?>
	<?php $view['javelin']->initBehavior('twipsy', array('selector' => '.zform-tool-button a')) ?>
	
	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Enregistrer le brouillon" />
	</div>
</form>

<?php } /* Rédaction */ elseif($InfosCandidature['candidature_etat'] == CANDIDATURE_REDACTION){ ?>

<p>
	Vous pouvez modifier votre texte de motivation. Des consignes plus précises
	vous ont été données précédemment. Vous pouvez rédiger tranquillement votre
	texte. Il est enregistré en brouillon en attendant son envoi définitif. Ce
	que vous allez modifier maintenant ne sera donc <strong>pas envoyé
	immédiatement aux administrateurs</strong>. Prenez donc le temps de soigner
	votre orthographe, syntaxe, etc.
	
	<?php if (!empty($InfosRecrutement['recrutement_date_fin_depot'])): ?><br />
	Nous vous rappelons que la date limite d'envoi pour ce recrutement est
	<strong><?php echo dateformat($InfosRecrutement['recrutement_date_fin_depot'], MINUSCULE); ?></strong>.
	<?php endif; ?>
</p>

<?php if(!isset($_POST['valider1'])){ ?>
<form method="post" action="">
	<div class="box">
		<p>
			En cliquant sur ce bouton, vous validez définitivement votre candidature
			et ne pourrez plus la modifier. <strong>C'est irréversible !</strong>
		</p>

		<?php if (!$InfosRecrutement['depot_possible']){ ?>
		<p class="center bold rouge">Vous avez dépassé la date limite d'envoi.</p>
		<?php } else { ?>
		<div class="center">
			<?php if (isset($quiz) && $InfosCandidature['candidature_quiz_score'] === NULL): ?>
				<p class="bold rouge">Vous devez répondre au questionnaire avant d'envoyer votre candidature.</p>
			<?php endif; ?>
			<input type="submit" name="valider1" class="btn" value="Envoyer ma candidature aux administrateurs"<?php
				if (isset($quiz) && $InfosCandidature['candidature_quiz_score'] === NULL) echo ' disabled="disabled"' ?> />
		</div>
		<?php } ?>
	</div>
</form>

<?php if (isset($quiz)): ?>
<p class="center">
	<a href="#bloc_motivation"
	   onclick="$('bloc_motivation').slide('show'); $('bloc_motivation').fade('in'); $('bloc_quiz').slide('hide'); $('bloc_quiz').fade('hide');">
		Modifier votre texte de motivation
	</a> |
	<a href="#bloc_quiz"
	   onclick="$('bloc_quiz').slide('show'); $('bloc_quiz').fade('in'); $('bloc_motivation').slide('hide'); $('bloc_motivation').fade('hide');"<?php
	   if ($InfosCandidature['candidature_quiz_score'] === NULL) echo ' class="bold"' ?>>
		Répondre au questionnaire à choix multiples
	</a>
</p>
<?php endif; ?>

<div id="bloc_motivation">
	<form method="post" action="" class="form-horizontal">
		<fieldset>
			<p class="center">Dernière modification <?php echo dateformat($InfosCandidature['candidature_date'], MINUSCULE); ?>.</p>
			
			<div class="control-group">
				<label for="texte" class="control-label">Texte de motivation</label>
				<div class="controls">
					<div class="zform">
						<div class="zform-wrapper">
							<textarea name="texte" id="texte"><?php echo htmlspecialchars($texte_zform) ?></textarea>
						</div>
						<div class="zform-preview-area">
							<?php echo $view['messages']->parse($texte_zform) ?>
						</div>
					</div>
					<?php $view['javelin']->initBehavior('zform', array('id' => 'texte')) ?>
					<?php $view['javelin']->initBehavior('resizable-textarea', array('id' =>'texte')) ?>
				</div>
			</div>

			<?php if ($InfosRecrutement['recrutement_redaction']){ ?>
			<div class="control-group">
				<label for="redaction" class="control-label">Rédaction</label>
				<div class="controls">
					<div class="zform">
						<div class="zform-wrapper">
							<textarea name="redaction" id="redaction"><?php echo htmlspecialchars($InfosCandidature['candidature_redaction']) ?></textarea>
						</div>
						<div class="zform-preview-area">
							<?php echo $view['messages']->parse($InfosCandidature['candidature_redaction']) ?>
						</div>
					</div>
					<?php $view['javelin']->initBehavior('zform', array('id' => 'redaction')) ?>
					<?php $view['javelin']->initBehavior('resizable-textarea', array('id' =>'redaction')) ?>
				</div>
			</div>
			<?php } ?>
			<?php $view['javelin']->initBehavior('squeezebox', array('selector' => '.zform-squeezebox-link', 'options' => array('handler' => 'iframe'))) ?>
			<?php $view['javelin']->initBehavior('twipsy', array('selector' => '.zform-tool-button a')) ?>

			<div class="form-actions">
				<input type="submit" class="btn btn-primary" value="Enregistrer le brouillon" />
			</div>
		</fieldset>
	</form>
</div>

<?php if (isset($quiz)): ?>
	<div id="bloc_quiz">
		<fieldset>
			<legend>Répondre au questionnaire à choix multiples</legend>
			<?php if ($InfosCandidature['candidature_quiz_score'] !== NULL): ?>
				<p>Vous avez déjà répondu au questionnaire.</p>
			<?php else: ?>
				<p>
					En cliquant sur le lien ci-dessous, vous accéderez au quiz.<br/>
					Notez que le temps de réponse au quiz est pris en compte.
					Sans être un critère de notation, il pourra servir à départager deux candidats.<br/><br/>
				</p>
				<p class="center bold"><a href="quiz-<?php echo $_GET['id'] ?>.html">Accéder au quiz</a></p>
			<?php endif; ?>
		</fieldset>
	</div>
<?php endif; ?>

<?php } else{ ?>

<form method="post" action="">
	<fieldset>
		<legend>Confirmer l'envoi de ma candidature</legend>
		<p>
			En cliquant sur ce bouton, vous validez définitivement votre
			candidature et ne pourrez plus la modifier.
			<strong>C'est irréversible !</strong>
		</p>

		<p class="center">
			<input type="submit" name="confirmer1" value="Confirmer l'envoi" /> <input type="submit" name="annuler" value="Annuler" />
		</p>
	</fieldset>
</form>

<span class="citation">Citation : votre candidature</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_texte']); ?></div>
<?php if(!empty($InfosCandidature['candidature_redaction'])) { ?>
<br /><span class="citation">Citation : votre rédaction</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_redaction']); ?></div>
<?php } ?>

<?php } } /* Candidature envoyée */ elseif($InfosCandidature['candidature_etat'] == CANDIDATURE_ENVOYE){ ?>

<p>
	Votre texte de motivation a bien été envoyé <?php echo dateformat($InfosCandidature['candidature_date'], MINUSCULE); ?>
	aux administrateurs. Ils examinent votre production et se prononceront
	prochainement. Vous serez informé par un message privé de tout changement
	vous concernant.
</p>

<span class="citation">Citation : étape 1 - Texte de motivation</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_texte']); ?></div>
<?php if(!empty($InfosCandidature['candidature_redaction'])) { ?>
<br /><span class="citation">Citation : étape 1 - Rédaction</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_redaction']); ?></div>
<?php } ?>

<?php } /* Test */ elseif($InfosCandidature['candidature_etat'] == CANDIDATURE_ATTENTE_TEST && $InfosCandidature['correction_possible']){ ?>

<?php if($InfosCandidature['candidature_test_type'] == TEST_TEXTE){ ?>
<p>
	Un administrateur a requis un test. Cette procédure est habituelle et ne doit
	pas être considérée comme négative, au contraire. Ce test consiste en la
	correction d'un texte comportant des fautes. C'est une mise en situation du
	travail de zCorrection. Tout comme lors de la rédaction du texte de motivation,
	<strong>rien n'est envoyé aux administrateurs tant que vous ne validez pas
	l'envoi</strong>. En attendant, cela reste à l'état de brouillon.<br />

	Nous vous rappelons que la date limite d'envoi pour cette correction est
	<strong><?php echo dateformat($InfosCandidature['candidature_date_fin_correction'], MINUSCULE); ?></strong>.
</p>

<?php } elseif($InfosCandidature['candidature_test_type'] == TEST_TUTO){ ?>
<p>
	Un administrateur a requis un test. Cette procédure est habituelle et ne doit
	pas être considérée comme négative, au contraire. Ce test consiste en la
	correction d'un texte comportant des fautes. C'est une mise en situation du
	travail de zCorrection. Le tutoriel vous est envoyé au format <em>.tuto</em>.
	Vous aurez donc besoin de zEditor, logiciel libre et multi-plateforme
	(<a href="/zeditor.html">plus d'informations ici</a>).<br />

	Nous vous rappelons que la date limite d'envoi pour cette correction est
	<strong><?php echo dateformat($InfosCandidature['candidature_date_fin_correction'], MINUSCULE); ?></strong>.
</p>

<?php } elseif($InfosCandidature['candidature_test_type'] == TEST_DEFAUT){ ?>
<p>
	Un administrateur a requis un test. Cette procédure est habituelle et ne doit
	pas être considérée comme négative, au contraire. Ce test consiste en la
	correction de deux textes comportant des fautes. Le premier est une mise en situation du
	travail de zCorrection, avec un tutoriel. Il vous est envoyé au format <em>.tuto</em>.
	Vous aurez donc besoin de zEditor, logiciel libre et multi-plateforme
	(<a href="/zeditor.html">plus d'informations ici</a>). Le second est un texte
	plus littéraire. Il vous est envoyé au format <em>.txt</em> et peut donc être ouvert
	avec n'importe quel éditeur de texte (bloc-notes sous Windows par exemple).<br />

	Nous vous rappelons que la date limite d'envoi pour les deux corrections est
	<strong><?php echo dateformat($InfosCandidature['candidature_date_fin_correction'], MINUSCULE); ?></strong>.
</p>
<?php } ?>

<?php if(!isset($_POST['valider2'])){ ?>
<?php if($InfosCandidature['candidature_test_type'] == TEST_TUTO){ ?>
<form action="" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Envoyer ma correction</legend>
		<p class="center bold">
			<a href="/tutos/recrutement/originaux/<?php echo $InfosCandidature['candidature_test_tuto']; ?>">Récupérer le tutoriel à corriger</a>
		</p><br />

		<label for="tuto">Sélectionner le tutoriel sur mon disque dur : </label>
		<input type="file" name="tuto" id="tuto" />
		<br /><br />
		<label for="note_correction">Note aux correcteurs</label>
		<?php echo $view->render('::zform.html.php', array('id' => 'note_correction', 'texte' => $InfosCandidature['candidature_correction_note'])) ?><br /><br />

		<div class="send">
			<input type="submit" name="valider_tuto" value="Envoyer le tutoriel" />
		</div>
	</fieldset>
</form>
<?php } elseif($InfosCandidature['candidature_test_type'] == TEST_DEFAUT){ ?>
<form action="" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Envoyer mes corrections</legend>
		<p class="center bold">
			<a href="/tutos/recrutement/originaux/0101010101.tuto">Récupérer le tutoriel à corriger</a><br />
			<a href="/tutos/recrutement/originaux/0101010101.txt">Récupérer le texte littéraire à corriger</a>
		</p><br />

		<label for="tuto">Sélectionner le tutoriel corrigé sur mon disque dur : </label>
		<input type="file" name="tuto" id="tuto" />
		<br /><br />
		<label for="tuto">Sélectionner le texte littéraire corrigé sur mon disque dur : </label>
		<input type="file" name="texte" id="texte" />
		<br /><br />
		<label for="note_correction">Note aux correcteurs</label>
		<?php echo $view->render('::zform.html.php', array('id' => 'note_correction', 'texte' => $InfosCandidature['candidature_correction_note'])) ?><br /><br />

		<div class="send">
			<input type="submit" name="valider_tuto" value="Envoyer les corrections" />
		</div>
	</fieldset>
</form>
<?php } elseif($InfosCandidature['candidature_test_type'] == TEST_TEXTE){ ?>
<form method="post" action="">
	<fieldset>
		<legend>Valider ma correction</legend>
		<p>
			En cliquant sur ce bouton, vous validez définitivement votre correction
			et ne pourrez plus la modifier. <strong>C'est irréversible !</strong>
		</p>

		<p class="center">
			<input type="submit" name="valider2" value="Envoyer ma correction aux administrateurs" />
		</p>
	</fieldset>
</form>

<form method="post" action="">
	<fieldset>
		<legend>Corriger le texte</legend>
		<div class="send">
			<input type="submit" value="Enregistrer le brouillon" />
		</div>

		<p class="center">
			<?php if($InfosCandidature['candidature_date_correction'] != '0000-00-00 00:00:00'){ ?>
			Dernière modification <?php echo dateformat($InfosCandidature['candidature_date_correction'], MINUSCULE); ?>.<br />
			<?php } ?>
			<a href="#" onclick="$('texte').value = $('texte_original').innerHTML; return false;">
				Insérer le texte original dans la zForm
			</a>

			<div id="texte_original" style="display: none;"><?php echo $InfosCandidature['candidature_correction_original']; ?></div>
		</p>
		<label for="texte">Correction</label>
		<?php echo $view->render('::zform.html.php', array('texte' => $texte_zform)); ?>
		<br /><br />
		<label for="note_correction">Note aux correcteurs</label>
		<?php echo $view->render('::zform.html.php', array('id' => 'note_correction', 'texte' => $InfosCandidature['candidature_correction_note'])) ?>

		<div class="send">
			<input type="submit" value="Enregistrer le brouillon" />
		</div>
	</fieldset>
</form>
<?php } } else{ ?>
<form method="post" action="">
	<fieldset>
		<legend>Confirmer l'envoi de ma correction</legend>
		<p>
			En cliquant sur ce bouton, vous validez définitivement votre
			correction et ne pourrez plus la modifier.
			<strong>C'est irréversible !</strong>
		</p>

		<p class="center">
			<input type="submit" name="confirmer2" value="Confirmer l'envoi" />
			<input type="submit" name="annuler" value="Annuler" />
		</p>
	</fieldset>
</form>

<?php } } /* Test raté */ elseif($InfosCandidature['candidature_etat'] == CANDIDATURE_ATTENTE_TEST && !$InfosCandidature['correction_possible']){ ?>

<p>
	Un administrateur a requis un test. Cette procédure est habituelle et ne doit
	pas être considérée comme négative, au contraire. Ce test consiste en la
	correction d'un texte comportant des fautes. C'est une mise en situation du
	travail de zCorrection.<br />

	<span class="bold rouge">
		Vous avez dépassé la date limite d'envoi pour cette	correction qui était
		<strong><?php echo dateformat($InfosCandidature['candidature_date_fin_correction'], MINUSCULE); ?></strong>.
	</span>
</p>

<p>
	Les examinateurs examinent tout de même votre travail et donneront les résultats
	du recrutement prochainement. Vous serez informé par un message privé de
	tout changement vous concernant.
</p>

<?php } /* Testé */ elseif($InfosCandidature['candidature_etat'] == CANDIDATURE_TESTE){ ?>

<p>
	Votre test a bien été envoyé <?php echo dateformat($InfosCandidature['candidature_date_correction'], MINUSCULE); ?>
	aux administrateurs. Ils examinent votre travail et donneront les résultats
	du recrutement prochainement. Vous serez informé par un message privé de tout
	changement vous concernant.
</p>

<span class="citation">Citation : étape 1 - Texte de motivation</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_texte']); ?></div>
<?php if(!empty($InfosCandidature['candidature_redaction'])) { ?>
<br /><span class="citation">Citation : étape 1 - Rédaction</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_redaction']); ?></div>
<?php } ?><br />

<?php if($InfosCandidature['candidature_test_type'] == TEST_TEXTE){ ?>
<span class="citation">Citation : étape 2 - Correction d'un texte - Texte à corriger</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_correction_original']); ?></div><br />

<span class="citation">Citation : étape 2 - Correction d'un texte - Votre correction</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_correction_corrige']); ?></div>
<?php } elseif ($InfosCandidature['candidature_test_type'] == TEST_TUTO){ ?>
<span class="citation">Citation : étape 2 - Correction d'un tutoriel</span>
<div class="citation2">
	Votre correction de tutoriel a été envoyée :
	<a href="/tutos/recrutement/corrections/<?php echo $InfosCandidature['candidature_test_tuto']; ?>">tutoriel corrigé</a>
</div>
<?php } elseif ($InfosCandidature['candidature_test_type'] == TEST_DEFAUT){ ?>
<span class="citation">Citation : étape 2 - Correction d'un tutoriel</span>
<div class="citation2">
	Votre correction de tutoriel a été envoyée :
	<a href="/tutos/recrutement/corrections/<?php echo $InfosCandidature['recrutement_id'].'_'.$InfosCandidature['utilisateur_id'].'_'.$InfosCandidature['candidature_test_tuto']; ?>">tutoriel corrigé</a> -
	<a href="/tutos/recrutement/corrections/<?php echo $InfosCandidature['recrutement_id'].'_'.$InfosCandidature['utilisateur_id'].'_'.$InfosCandidature['candidature_test_texte']; ?>">texte littéraire corrigé</a>
</div>
<?php } if(!empty($InfosCandidature['candidature_correction_note'])) { ?>
<br /><span class="citation">Citation : étape 2 - Vos notes aux correcteurs</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_correction_note']); ?></div>
<?php } ?>

<?php } /* Accepté */ elseif($InfosCandidature['candidature_etat'] == CANDIDATURE_ACCEPTE){ ?>

<p>
	Félicitations, vous avez été accepté ! Vous avez normalement reçu un
	message privé vous informant de cette bonne nouvelle. Vous serez informé des
	modalités concernant votre intégration dans votre nouveau groupe prochainement.<br />
	Nous vous souhaitons la bienvenue dans l'équipe et du bon boulot parmi nous !
</p>

<span class="citation">Citation : commentaires de l'administrateur</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_commentaire']); ?></div><br />

<?php } /* Refusé */ elseif($InfosCandidature['candidature_etat'] == CANDIDATURE_REFUSE){ ?>

<p>
	Nous sommes désolés mais votre candidature a été refusée. Vous avez
	normalement reçu un message privé vous en informant. Vous pourrez cependant
	retenter votre chance lors du prochain recrutement !<br />
	À bientôt, et merci d'avoir participé à ce recrutement.
</p>

<span class="citation">Citation : commentaires de l'administrateur</span>
<div class="citation2"><?php echo $view['messages']->parse($InfosCandidature['candidature_commentaire']); ?></div><br />

<?php } /* Désistement */ else if ($InfosCandidature['candidature_etat'] == CANDIDATURE_DESISTE) { ?>

<p>
	Vous vous êtes désisté pour ce recrutement. Par conséquent, votre candidature
	n'est plus valide. Il vous faudra attendre une prochaine session de recrutement
	pour postuler de nouveau.<br />
	À bientôt, et merci d'avoir participé à ce recrutement.
</p>
<?php } ?>

<?php if ($InfosCandidature['candidature_etat'] == CANDIDATURE_REDACTION && isset($quiz)): ?>
<script type="text/javascript">
	document.addEvent('domready', function(){
		$('bloc_quiz').slide('hide');
		$('bloc_quiz').fade('hide');
	});
</script>
<?php endif; ?>
