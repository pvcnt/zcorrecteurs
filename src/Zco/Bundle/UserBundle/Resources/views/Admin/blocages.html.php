<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Liste des tentatives de connexion ratées</h1>

<?php if (count($blocages) > 0): ?>

<?php echo $blocages->render() ?>

<table class="table">
	<thead>
		<tr>
			<th style="width: 20%;">Compte concerné</th>
			<th style="width: 20%;">IP</th>
			<th style="width: 20%;">Date</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($blocages as $blocage): ?>
		<tr>
			<td>
				<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $blocage->Utilisateur->getId(), 'slug' => rewrite($blocage->Utilisateur->getUsername()))) ?>" style="color: <?php echo htmlspecialchars($blocage->Utilisateur->getGroup()->getCssClass()) ?>">
					<?php echo htmlspecialchars($t->Utilisateur->getUsername()) ?>
				</a>
			</td>
			<td><?php echo long2ip($blocage['ip']) ?></td>
			<td><?php echo dateformat($blocage['date']) ?></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>

<?php echo $blocages->render() ?>

<?php else: ?>
<p>Aucune tentative de connexion ratée n'a été enregistrée.</p>
<?php endif ?>