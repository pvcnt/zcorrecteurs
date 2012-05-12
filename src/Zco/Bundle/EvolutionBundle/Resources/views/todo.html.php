<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php') ?>

<h1><?php echo Page::$titre; ?></h1>

<form action="" method="get">
	<fieldset>
		<legend>Sélectionner un développeur</legend>
		<label for="id">Développeur :</label>
		<select name="id" id="id" onchange="document.location = '?id='+this.value;">
			<?php foreach($ListerDeveloppeurs as $dev){ ?>
			<option value="<?php echo $dev['utilisateur_id']; ?>" style="color: <?php echo $dev['groupe_class']; ?>;"<?php if($id_admin == $dev['utilisateur_id']) echo ' selected="selected"'; ?>>
				<?php echo htmlspecialchars($dev['utilisateur_pseudo']); ?>
			</option>
			<?php } ?>
		</select>
		<input type="submit" value="Sélectionner" />
	</fieldset>
</form>

<?php if(!empty($Tickets)){ ?>
<h2><?php echo count($Tickets); ?> anomalie<?php echo pluriel(count($Tickets)); ?> à résoudre</h2>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 55%;">Titre</th>
			<th style="width: 15%;">Priorité</th>
			<th style="width: 15%;">État</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($Tickets as $t){ ?>
		<tr>
			<td>
				<a href="demande-<?php echo $t['ticket_id']; ?>-<?php echo rewrite($t['ticket_titre']); ?>.html">
					<?php echo htmlspecialchars($t['ticket_titre']); ?>
				</a>
			</td>
			<td class="centre">
				<span class="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_class']); ?>">
					<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>
				</span>
			</td>
			<td class="centre">
				<span class="<?php echo htmlspecialchars($TicketsEtats[$t['version_etat']]['etat_class']); ?>">
					<?php echo htmlspecialchars($TicketsEtats[$t['version_etat']]['etat_nom']); ?>
				</span>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } ?>

<?php if(!empty($Taches)){ ?>
<br />
<h2><?php echo count($Taches); ?> tâche<?php echo pluriel(count($Taches)); ?> à implémenter</h2>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 55%;">Titre</th>
			<th style="width: 15%;">Priorité</th>
			<th style="width: 15%;">État</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($Taches as $t){ ?>
		<tr>
			<td>
				<a href="demande-<?php echo $t['ticket_id']; ?>-<?php echo rewrite($t['ticket_titre']); ?>.html">
					<?php echo htmlspecialchars($t['ticket_titre']); ?>
				</a>
			</td>
			<td class="centre">
				<span class="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_class']); ?>">
					<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>
				</span>
			</td>
			<td class="centre">
				<span class="<?php echo htmlspecialchars($TicketsEtats[$t['version_etat']]['etat_class']); ?>">
					<?php echo htmlspecialchars($TicketsEtats[$t['version_etat']]['etat_nom']); ?>
				</span>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } ?>

<?php if(empty($Tickets) && empty($Taches)){ ?>
<p>Ce développeur n'a aucune demande assignée.</p>
<?php } ?>
