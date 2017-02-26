<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Voir les changements de pseudos</h1>

<?php if (count($queries) > 0): ?>
<table class="table table-bordered">
	<thead>
		<tr>
			<th style="width: 15%;">Pseudo</th>
			<th style="width: 15%;">Pseudo désiré</th>
			<th style="width: 20%;">Date</th>
			<th style="width: 40%;">Raison</th>
			<th style="width: 10%;">Répondre</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($queries as $query): ?>
		<tr>
			<td>
				<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $query->getUserId(), 'slug' => rewrite($query->getUser()->getUsername()))) ?>">
					<?php echo htmlspecialchars($query->getUser()->getUsername()) ?>
				</a>
			</td>
			<td><?php echo htmlspecialchars($query->getNewUsername()) ?></td>
			<td class="center"><?php echo dateformat($query->getDate()) ?></td>
			<td><?php echo $view['messages']->parse($query->getReason()) ?></td>
			<td class="center">
				<a href="<?php echo $view['router']->path('zco_user_admin_newPseudoAnswer', array('id' => $query->getId())) ?>">
					<img src="/bundles/zcoblog/img/valider.png" alt="Valider" /> / 
					<img src="/img/supprimer.png" alt="Refuser" />
				</a>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php else: ?>
<p>Aucune demande de changement de pseudo n'est en attente.</p>
<?php endif ?>
