<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des retours d'expérience</h1>

<?php if (count($feedbacks) > 0): ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 25%;">Informations</th>
			<th style="width: 75%;">Contenu</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach ($feedbacks as $feedback): ?>
		<tr>
			<td>
				<ul>
					<li><?php echo dateformat($feedback['date']) ?></li>
					<?php if (!empty($feedback['utilisateur_id'])): ?>
					<li>Par <a href="/membres/profil-<?php echo $feedback['Utilisateur']['id'] ?>-<?php echo rewrite($feedback['Utilisateur']['pseudo']) ?>.html"><?php echo htmlspecialchars($feedback['Utilisateur']['pseudo']) ?></a></li>
					<?php endif; ?>
					<?php if (!empty($feedback['email'])): ?>
					<li>Courriel : <a href="mailto: <?php echo htmlspecialchars($feedback['email']) ?>"><?php echo htmlspecialchars($feedback['email']) ?></a></li>
					<?php endif; ?>
				</ul>
			</td>
			<td>
				<?php echo nl2br(htmlspecialchars($feedback['contenu'])) ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>Aucun retour d'expérience n'a encore été déposé.</p>
<?php endif; ?>