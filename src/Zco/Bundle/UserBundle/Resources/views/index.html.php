<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Liste des membres</h1>

<p class="bold center">
	<?php if ($users->getTotalItemCount() > 0): ?>
		<?php echo $users->getTotalItemCount() ?> membre<?php echo pluriel($users->getTotalItemCount()) ?> 
		<?php echo pluriel($users->getTotalItemCount(), 'ont', 'a') ?> été 
		trouvé<?php echo pluriel($users->getTotalItemCount()) ?>.
	<?php else: ?>
		Aucun membre n'a été trouvé.
	<?php endif ?><br />
	
	<a href="#filtres">Filtrer les utilisateurs</a>
</p>

<?php echo $users->render() ?>

<?php if (count($users) > 0): ?>
<table class="table table-avatar">
	<thead>
		<tr>
			<th class="avatar">Avatar</th>
			<th class="pseudo">Utilisateur</th>
			<th>Groupe</th>
			<?php if (verifier('voir_nb_messages')) echo '<th>Messages</th>'; ?>
			<th>Date d'inscription</th>
			<th>Dernière action</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($users as $user): ?>
		<tr>
			<td class="avatar">
				<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>">
					<img src="<?php echo htmlspecialchars($user->getAvatar()) ?>" 
						 alt="Avatar de <?php echo htmlspecialchars($user->getUsername()) ?>" />
				</a>
			</td>
			<td>
				<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>" 
					style="color: <?php echo $user->getGroup()->getCssClass() ?>;">
					<?php echo htmlspecialchars($user->getUsername()) ?>
				</a>
			</td>
			<td class="center"><?php echo htmlspecialchars($user->getGroup()) ?></td>
			<?php if (verifier('voir_nb_messages')): ?>
				<td class="center">
					<?php echo $user->getNbMessages() ?>
				</td>
			<?php endif ?>
			<td class="center">
				<?php echo $view['humanize']->dateformat($user->getRegistrationDate()) ?>
			</td>
			<td class="center">
				<?php echo $view['humanize']->dateformat($user->getLastActionDate()) ?>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php endif ?>

<?php echo $users->render() ?>

<form method="get" action="<?php echo $view['router']->generate('zco_user_index') ?>" class="form-horizontal">
	<fieldset>
		<legend id="filtres">Filtres</legend>
		<div class="control-group">
			<label for="type" class="control-label">Chercher les membres…</label>
			<div class="controls">
				<select name="type" id="type">
					<option value="1" <?php if (!empty($type) && $type === 1) echo 'selected="selected"'; ?>>
						… commençant par
					</option>
					<option value="2" <?php if (!empty($type) && $type === 2) echo 'selected="selected"'; ?>>
						… terminant par
					</option>
					<option value="3" <?php if (!empty($type) && $type === 3) echo 'selected="selected"'; ?>>
						… contenant
					</option>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label for="pseudo" class="control-label">Pseudo</label>
			<div class="controls">
				<input type="text" name="pseudo" id="pseudo" 
					value="<?php echo htmlspecialchars($pseudo) ?>" />
				<?php $view['javelin']->initBehavior('autocomplete', array(
				    'id' => 'pseudo', 
				    'callback' => $view['router']->generate('zco_user_api_searchUsername'),
				)) ?>
			</div>
		</div>

		<div class="control-group">
			<label for="tri" class="control-label">Classer par</label>
			<div class="controls">
				<select name="tri" id="tri">
					<option value="pseudo"<?php if ($order === 'pseudo') echo ' selected="selected"' ?>>Pseudo</option>
					<option value="id"<?php if ($order === 'id') echo ' selected="selected"' ?>>Numéro de membre</option>
					<?php if (verifier('voir_nb_messages')): ?>
						<option value="forum_messages"<?php if ($order === 'forum_messages') echo ' selected="selected"' ?>>
							Messages
						</option>
					<?php endif ?>
					<option value="date_inscription"<?php if ($order === 'date_inscription') echo ' selected="selected"' ?>>
						Date d’inscription
					</option>
					<option value="date_derniere_visite"<?php if ($order === 'date_derniere_visite') echo ' selected="selected"' ?>>
						Date de dernière visite
					</option>
				</select>
				<select name="ordre" class="input-medium">
					<option value="asc"<?php if ($orderBy === 'asc') echo ' selected="selected"'; ?>>Croissant</option>
					<option value="desc"<?php if ($orderBy === 'desc') echo ' selected="selected"'; ?>>Décroissant</option>
				</select>
			</div>
		</div>
		
		<div class="control-group">
			<label for="groupe" class="control-label">Groupe</label>
			<div class="controls">
				<select name="groupe" id="groupe">
					<option value="">Tous les groupes</option>
					<?php foreach ($groups as $group): ?>
						<option value="<?php echo $group['id'] ?>" 
							style="color: <?php echo htmlspecialchars($group['class']) ?>;"
							<?php if ($group == $group['id']) echo ' selected="selected"' ?>>
							<?php echo htmlspecialchars($group['nom']) ?>
						</option>
					<?php endforeach ?>
				</select>
			</div>
		</div>

		<?php if (verifier('voir_groupes_secondaires')): ?>
		<div class="control-group">
			<label for="groupe" class="control-label">Groupes secondaires</label>
			<div class="controls">
				<select name="secondaire[]" id="groupes_secondaires" size="<?php echo count($secondaryGroups) ?>" multiple>
					<?php foreach ($secondaryGroups as $group): ?>
						<option value="<?php echo $group['id'] ?>" 
							style="color: <?php echo htmlspecialchars($group['class']) ?>;"
							<?php if (in_array($group['id'], $secondaryGroup)) echo ' selected="selected"' ?>>
							<?php echo htmlspecialchars($group['nom']) ?>
						</option>
					<?php endforeach ?>
				</select>
				<p class="help-block">Aucun filtre n’est appliqué lorsqu’aucune sélection n’est faite.</p>
			</div>
		</div>
		<?php endif ?>

		<div class="form-actions">
			<input type="submit" class="btn btn-primary" value="Envoyer" />
			<a href="<?php echo $view['router']->generate('zco_user_index') ?>" class="btn">
				Effacer les filtres
			</a>
		</div>
	</fieldset>
</form>
