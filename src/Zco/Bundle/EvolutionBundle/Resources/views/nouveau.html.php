<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php', array('type' => !empty($_GET['type']) ? $_GET['type'] : 'bug')) ?>

<h1>Déposer une nouvelle demande</h1>

<p>
	Vous vous apprêtez à envoyer une demande aux développeurs du site. Avant de l'envoyer, relisez-vous,
	et n'hésitez pas à joindre une capture d'écran si cela peut aider à la compréhension du problème.
</p>

<p><a href="/aide/page-9-suggestions-et-dysfonctionnements.html">
    <img src="/img/misc/aide.png" alt="" />
    Plus d'informations sur les suggestions et dysfonctionnements.
</a></p>

<form method="post" action="">
	<fieldset>
		<legend>Description rapide de la demande</legend>

		<table class="UI_wrapper">
		<tr><td>
			<label for="resume">Titre :</label>
			<input type="text" size="50" name="resume" id="resume" maxlength="255" /><br />

			<label for="type">Type de demande :</label>
			<select name="type" id="type">
				<option value="bug"<?php if (!empty($_GET['type']) && $_GET['type'] == 'bug') echo ' selected="selected"' ?>>
					Anomalie
				</option>
				<option value="tache"<?php if (!empty($_GET['type']) && $_GET['type'] == 'tache') echo ' selected="selected"' ?>>
					Tâche / suggestion
				</option>
			</select></td>

			<td><?php if(verifier('tracker_voir_prives')){ ?>
			<label for="prive">Cette demande concerne une partie privée du site :</label>
			<input type="checkbox" name="prive" id="prive" /><br /><br />
			<?php } ?>

			<label for="critique">Cette demande concerne une faille de sécurité critique :</label>
			<input type="checkbox" name="critique" id="critique" />
		</td></tr></table>
	</fieldset>

	<fieldset>
		<legend>Propriétés de la demande</legend>
		<table class="UI_wrapper">
			<tr>
				<td>
					<label for="priorite">Priorité :</label>
					<select name="priorite" id="priorite">
						<?php foreach($TicketsPriorites as $cle => $valeur){ ?>
						<option value="<?php echo $cle; ?>"<?php if($cle == 3) echo ' selected="selected"'; ?> class="<?php echo $valeur['priorite_class']; ?>">
							<?php echo htmlspecialchars($valeur['priorite_nom']); ?>
						</option>
						<?php } ?>
					</select><br />

					<label for="categorie">Module du site concerné : </label>
					<select id="categorie" name="categorie">
						<?php foreach($ListerCategories as $c){ ?>
						<?php if($c['cat_niveau'] <= 1){ ?>
						<option value="<?php echo $c['cat_id']; ?>"><?php echo ($c['cat_niveau'] > 0 ? '..... ' : '').htmlspecialchars($c['cat_nom']); ?></option>
						<?php } } ?>
					</select>
				</td>

				<td>
					<?php if(verifier('tracker_assigner_soi') || verifier('tracker_assigner')){ ?>
					<label for="assigner">Assigner à la résolution de cette demande : </label>
					<select name="assigner" id="assigner">
						<option value="0" selected="selected">Personne</option>
						<optgroup label="Assigner la tâche à…">
							<?php foreach($ListerEquipe as $cle => $m){ ?>
							<?php if($m['utilisateur_id'] == $_SESSION['id']) $i = $cle + 1; ?>
							<option value="<?php echo $m['utilisateur_id']; ?>" style="color: <?php echo $m['groupe_class']; ?>;"<?php if(!verifier('tracker_assigner') && $m['utilisateur_id'] != $_SESSION['id']) echo ' disabled="disabled"'; ?>><?php echo htmlspecialchars($m['utilisateur_pseudo']); ?></option>
							<?php } ?>
						</optgroup>
					</select>
					<input type="button" onclick="document.getElementById('assigner').options[<?php echo $i; ?>].selected = true" value="M'assigner la demande" />
					<br /><br />
					<?php } ?>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Description détaillée</legend>
		<label for="url">Lien (facultatif) :</label>
		<input type="text" size="40" name="url" id="url" /><br />

		<label for="texte">Description détaillée de l'anomalie (causes, comment la reproduire, etc.) ou de la suggestion :</label><br /><br />
		<?php echo $view->render('::zform.html.php'); ?><br />
	</fieldset>

	<div class="centre">
		<input type="submit" name="send" value="Envoyer" />
	</div>
</form>
