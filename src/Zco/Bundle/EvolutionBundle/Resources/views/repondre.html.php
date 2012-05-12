<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php', array('type' => $InfosTicket['ticket_type'])) ?>

<h1>Répondre à la demande</h1>

<div class="UI_column_menu">
	<?php include(dirname(__FILE__).'/_actions_anomalie.html.php'); ?>
</div>

<div class="UI_column_text">
	<p>
		Vous pouvez répondre à la personne ayant déposé cette demande. Veillez
		cependant à vous assurer que votre message est utile aux administrateurs.
		Tout message inutile de <em>flood</em> sera supprimé, et son auteur
		pourra être sanctionné.<br />
		Merci de votre compréhension !
	</p>

	<form method="post" action="">
		<div class="centre">
			<input type="submit" name="send" value="Envoyer" tabindex="10" />
		</div>

        <?php if ($can_changer_priorite || verifier('tracker_lier_roadmap') || $can_editer || $can_changer_resolution || $can_changer_assigne){ ?>
		<fieldset>
			<legend>Changer les propriétés de la demande</legend>
			<table class="UI_wrapper">
				<tr>
					<td>
						<?php if($can_changer_priorite){ ?>
						<label for="priorite">Priorité :</label>
						<select name="priorite" id="priorite" tabindex="1">
							<?php foreach($TicketsPriorites as $cle => $valeur){ ?>
							<option value="<?php echo $cle; ?>"<?php if($InfosTicket['version_priorite'] == $cle) echo ' selected="selected"'; ?> class="<?php echo $valeur['priorite_class']; ?>">
								<?php echo htmlspecialchars($valeur['priorite_nom']); ?>
							</option>
							<?php } ?>
						</select><br />
						<?php } ?>

						<?php if($can_editer){ ?>
						<label for="categorie">Module du site concerné : </label>
						<select id="categorie" name="categorie" tabindex="3">
							<?php if(!empty($InfosTicket['cat_id']) && !verifier('voir', $InfosTicket['cat_id'])){ ?>
							<option value="<?php echo $InfosTicket['cat_id']; ?>" selected="selected">Catégorie actuelle (privée)</option>
							<?php } ?>

							<?php foreach($ListerCategories as $c){ ?>
							<?php if($c['cat_niveau'] <= 1){ ?>
							<option value="<?php echo $c['cat_id']; ?>"<?php if($InfosTicket['cat_id'] == $c['cat_id']) echo ' selected="selected"'; ?>><?php echo ($c['cat_niveau'] > 0 ? '..... ' : '').htmlspecialchars($c['cat_nom']); ?></option>
							<?php } } ?>
						</select><br />
						<?php } ?>
					</td>

					<td>
						<?php if($can_changer_resolution){ ?>
						<label for="etat">État :</label>
						<select name="etat" id="etat" tabindex="5">
							<?php foreach($TicketsEtats as $cle => $valeur){ ?>
							<option value="<?php echo $cle; ?>"<?php if($InfosTicket['version_etat'] == $cle) echo ' selected="selected"'; ?> class="<?php echo $valeur['etat_class']; ?>"><?php echo htmlspecialchars($valeur['etat_nom']); ?></option>
							<?php } ?>
						</select><br />
						<?php } ?>

						<?php if($can_changer_assigne){ ?>
						<label for="assigner">Assigner à la résolution de cette demande : </label>
						<select name="assigner" id="assigner" tabindex="7">
							<option value="0">Personne</option>
							<optgroup label="Assigner la tâche à…">
								<?php foreach($ListerEquipe as $cle => $m){ ?>
								<?php if($m['utilisateur_id'] == $_SESSION['id']) $i = $cle + 1; ?>
								<option value="<?php echo $m['utilisateur_id']; ?>" style="color: <?php echo $m['groupe_class']; ?>;"<?php if($m['utilisateur_id'] == $InfosTicket['id_admin']) echo ' selected="selected"'; if(!verifier('tracker_assigner') && $m['utilisateur_id'] != $_SESSION['id']) echo ' disabled="disabled"'; ?>>
									<?php echo htmlspecialchars($m['utilisateur_pseudo']); ?>
								</option>
								<?php } ?>
							</optgroup>
						</select>
						<input  tabindex="8" type="button" onclick="document.getElementById('assigner').options[<?php echo $i; ?>].selected = true" value="M'assigner la demande" />
						<?php } ?>
					</td>
				</tr>
			</table>
		</fieldset>
		<?php } ?>


		<fieldset>
			<legend>Contenu de la réponse</legend>
			<label for="texte">Contenu de votre réponse :</label><br />
			<?php echo $view->render('::zform.html.php', array(
				'tabindex' => 9, 
				'upload_id_formulaire' => $_GET['id'],
				'upload_utiliser_element' => true,
				'texte' => $texte_zform,
			)) ?>
		</fieldset>

		<div class="centre">
			<input type="submit" name="send" value="Envoyer" tabindex="11" />
		</div>
	</form>
</div>
