<?php $view->extend('::layouts/default.html.php') ?>

<h1>Répondre à une candidature</h1>

<form action="" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Répondre à une candidature</legend>
		<div class="send">
			<input type="submit" value="Envoyer" />
		</div>

		<label>Décision : </label>
		<?php if($InfosCandidature['candidature_etat'] != CANDIDATURE_TESTE){ ?>
		<input type="radio" id="tester" onclick="$('commentaire_accept').style.display = 'none'; $('commentaire').style.display = 'none'; $('test').style.display = 'block';" name="etat" value="<?php echo CANDIDATURE_ATTENTE_TEST; ?>" />
		<label class="nofloat" for="tester">Tester</label>
		<?php } ?>
		<input type="radio" id="accepter" onclick="$('commentaire_accept').style.display = 'block'; $('commentaire').style.display = 'block'; $('test').style.display = 'none';" name="etat" value="<?php echo CANDIDATURE_ACCEPTE; ?>" />
		<label class="nofloat vertf" for="accepter">Accepter</label>
		<input type="radio" id="refuser" onclick="$('commentaire_accept').style.display = 'none'; $('commentaire').style.display = 'block'; $('test').style.display = 'none';" name="etat" value="<?php echo CANDIDATURE_REFUSE; ?>" />
		<label class="nofloat rouge" for="refuser">Refuser</label>

		<div id="commentaire">
			<br /><hr /><br />
			<label for="comm">Commentaire envoyé par MP <noscript>(inutile si test) </noscript>:</label><br />
			<?php echo $view->render('::zform.html.php', array('id' => 'comm')); ?><br />
		</div>

		<div id="commentaire_accept">
			<label for="change_grp">Placer le membre dans son nouveau groupe immédiatement</label>
			<input type="checkbox" name="change_grp" id="change_grp" />
		</div>

		<div id="test">
			<br /><hr /><br />
			<label for="date_fin">Fin de correction du test <noscript>(inutile si acceptation ou refus) </noscript>:</label>
			<?php echo $view['widget']->dateTimePicker('date_fin', $InfosCandidature['recrutement_date_fin_epreuve']); ?><br />

			<label for="explicatif">Texte explicatif <noscript>(inutile si acceptation ou refus) </noscript>:</label><br />
			<?php echo $view->render('::zform.html.php', array('id' => 'explicatif')); ?><br />

			<p class="gras">Sélectionnez le mode d'envoi du test :</p>
			<ul class="gras">
				<li><a href="#" onclick="$('test_texte').slide('hide'); $('test_tuto').slide('hide'); $('test_defaut').slide('in'); return false;">
						Utiliser les deux tests par défaut
				</a> ou</li>
				<li><a href="#" onclick="$('test_texte').slide('hide'); $('test_defaut').slide('hide'); $('test_tuto').slide('in'); return false;">
					Envoyer au candidat le texte dans un <em>.tuto</em>
				</a> ou</li>
				<li><a href="#" onclick="$('test_tuto').slide('hide'); $('test_defaut').slide('hide'); $('test_texte').slide('in'); return false;">
					Envoyer au candidat le texte directement dans une zForm
				</a></li>
			</ul>

			<div id="test_defaut">
				Utiliser les deux tests par défaut : <a href="/tutos/recrutement/originaux/0101010101.tuto">le tutoriel</a>
				et <a href="/tutos/recrutement/originaux/0101010101.txt">le texte littéraire</a>.
			</div>

			<noscript><p class="gras centre" style="font-size: 14px;">OU</p><br /></noscript>


			<div id="test_tuto">
				<label for="tuto">Uploader un .tuto <noscript>(inutile si acceptation ou refus) </noscript>: </label>
				<input type="file" name="tuto" id="tuto" /><br />
			</div>

			<noscript><p class="gras centre" style="font-size: 14px;">OU</p><br /></noscript>

			<div id="test_texte">
				<label for="texte">Texte à corriger <noscript>(inutile si acceptation ou refus) </noscript></label>
				<?php echo $view->render('::zform.html.php', array('id' => 'texte')) ?>
			</div>
		</div>

		<br />
		<div class="send">
			<input type="submit" value="Envoyer" />
		</div>
	</fieldset>
</form>

<script type="text/javascript">
	$('commentaire').style.display = 'none';
	$('test').style.display = 'none';
	$('commentaire_accept').style.display = 'none';
	$('test_texte').slide('hide');
	$('test_tuto').slide('hide');
	$('test_defaut').slide('hide');
</script>
