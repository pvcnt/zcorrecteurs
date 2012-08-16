<?php $view->extend('::layouts/default.html.php') ?>

<h1>Diviser un sujet</h1>
<h2><?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?></h2>

<p>Vous vous apprêtez à diviser le sujet <strong><a href="sujet-<?php echo $InfosSujet['sujet_id'].'-'.rewrite($InfosSujet['sujet_titre']); ?>.html"><?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?></a></strong> en deux sujets distincts.</p>

<form method="post" action="">
	<fieldset>
		<legend>Paramètres du nouveau sujet</legend>
		<p>Vous devez dans ce cadre paramétrer le nouveau sujet, à savoir son titre, sous-titre (facultatif) et le forum où il sera.</p>

		<label for="titre">Titre du nouveau sujet : </label> <input type="text" name="titre" id="titre" size="35" /><br />
		<label for="titre">Sous-titre du nouveau sujet : </label> <input type="text" name="sous_titre" id="sous_titre" size="35" /><br />
		<label for="forum">Forum de destination : </label>
		<select name="forum" id="forum">
			<?php
			$nb = 0;
			foreach($ListerCategories as $clef => $valeur)
			{
				//Dans ce if on ne liste que les catégories
				if($valeur['cat_niveau'] == 2)
				{
					if($nb != 0)
						echo '</optgroup>';
					echo '<optgroup label="'.htmlspecialchars($valeur['cat_nom']).'">';
				}
				//Ici on liste les forums
				else
				{
					if($InfosSujet['sujet_forum_id'] == $valeur['cat_id'])
					{
						$selected = ' selected="selected"';
					}
					else
					{
						$selected = '';
					}
					echo '<option value="'.$valeur['cat_id'].'"'.$selected.'>'.htmlspecialchars($valeur['cat_nom']).'</option>';
				}
				$nb++;
			}
			?>
			</optgroup>
		 </select>
	</fieldset>

	<fieldset>
		<legend>Sélection des messages à déplacer</legend>
		<p>Vous devez maintenant sélectionner les messages à déplacer. Tous les messages cochés seront dans le <strong>nouveau sujet</strong>. Les autres resteront dans celui d'origine.</p>
		<?php if(count($ListerMessages) > 1){ ?>
		<table class="UI_items messages">
			<thead>
				<tr>
					<th style="width: 13%;">Auteur</th>
					<th style="width: 87%;">Message</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($ListerMessages as $clef => $valeur){ ?>
				<tr class="header_message">
					<td class="pseudo_membre">
						<img src="/img/<?php echo $valeur['statut_connecte']; ?>" alt="<?php echo $valeur['statut_connecte_label']; ?>" title="<?php echo $valeur['statut_connecte_label']; ?>" />
						<?php if(!empty($valeur['auteur_groupe'])) {?>
						<a href="/membres/profil-<?php echo $valeur['message_auteur']; ?>-<?php echo rewrite($valeur['auteur_message_pseudo']); ?>.html" style="color: <?php echo $valeur['groupe_class']; ?>;">
						<?php } ?>
						<?php echo htmlspecialchars($valeur['auteur_message_pseudo']); ?>
						<?php if(!empty($valeur['auteur_groupe'])) {?>
						</a>
						<?php } ?>
					</td>
					<td class="dates">
						Posté <?php echo dateformat($valeur['message_date'], MINUSCULE); ?> -
						<label for="msg[<?php echo $valeur['message_id']; ?>]" style="float: none;" class="gras">Sélectionner : </label>
						<input type="checkbox" name="msg[<?php echo $valeur['message_id']; ?>]" id="msg[<?php echo $valeur['message_id']; ?>]" />
					</td>
				</tr>
				<tr>
					<td class="infos_membre">
						<?php echo $view->get('messages')->afficherAvatar($valeur) ?><br/>
						<?php echo $view->get('messages')->afficherGroupe($valeur) ?><br/>
					</td>
					<td class="message">
						<div class="msgbox">
							<?php echo $view['messages']->parse($valeur['message_texte'], array(
								'core.anchor_prefix' => $valeur['message_id'], 
								'files.entity_id' => $valeur['message_id'],
								'files.entity_class' => 'ForumMessage',
							)); ?>

							<?php if(!empty($valeur['message_edite_auteur'])){ ?>
							<div class="message_edite">
								<?php if($valeur['message_auteur'] != $valeur['message_edite_auteur']){	?>
								<span style="color: red;">
								<?php } ?>
								Modifié <?php echo dateformat($valeur['message_edite_date'], MINUSCULE); ?>
								par
								<?php if(!empty($valeur['auteur_edition_id'])){ ?>
								<a href="/membres/profil-<?php echo $valeur['message_edite_auteur']; ?>-<?php echo rewrite($valeur['auteur_edition_pseudo']); ?>.html">
								<?php } ?>
								<?php echo htmlspecialchars($valeur['auteur_edition_pseudo']); ?>
								<?php if(!empty($valeur['auteur_edition_id'])){ ?></a><?php } ?>
								<?php if($valeur['message_auteur'] != $valeur['message_edite_auteur']){ ?>
								</span>
								<?php } ?>
							</div>
							<?php } if(!empty($valeur['auteur_message_signature'])){ ?>
							<div class="signature">
								<hr />
								<?php echo $view['messages']->parse($valeur['auteur_message_signature']); ?>
							</div>
							<?php } ?>
							<div class="cleaner">&nbsp;</div>
						</div>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php } else{ ?>
		Ce sujet contient moins de deux messages, vous ne pouvez donc pas le diviser.
		<?php } ?>
	</fieldset>

	<div class="send">
		<input type="submit" name="submit" value="Envoyer" />
	</div>
</form>
