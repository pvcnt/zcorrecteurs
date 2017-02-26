<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Liste des comptes non validés</h1>

<p>Voici la liste de tous les utilisateurs dont le compte n'est pas validé.</p>

<?php if (count($users) > 0): ?>
<table class="table table-small">
	<thead>
		<tr>
			<th>Utilisateur</th>
			<th>Date d'inscription</th>
			<th>Valider</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($users as $user): ?>
		<tr>
			<td>
				<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>">
					<?php echo htmlspecialchars($user->getUsername()) ?>
				</a>
			</td>
			<td class="center">
				<?php echo dateformat($user->getRegistrationDate()) ?>
			</td>
			<td class="center">
				<a href="<?php echo $view['router']->path('zco_user_admin_validateAccount', array('id' => $user->getId())) ?>">
					<img src="/pix.gif" class="fff tick" alt="Valider" />
				</a>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php else: ?>
Aucun compte non validé n'a été trouvé.
<?php endif ?>
