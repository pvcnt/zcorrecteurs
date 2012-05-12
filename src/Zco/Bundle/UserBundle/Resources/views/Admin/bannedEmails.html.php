<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Liste des adresses courriel bannies</h1>

<p>
	Les adresses courriel bannies ne pourront pas être utilisées à l'inscription
	ou lors d'un changement d'adresse. Le bannissement fonctionne par domaine entier.
</p>

<p class="bold center">
	<a href="<?php echo $view['router']->generate('zco_user_admin_newBannedEmail') ?>">
		Bannir une nouvelle plage d'adresses
	</a>
</p>

<p class="center">
	<strong><?php echo count($emails) ?> adresse<?php echo pluriel(count($emails)) ?> courriel</strong>
	<?php echo pluriel(count($emails), 'sont', 'est') ?> bannie<?php echo pluriel(count($emails)) ?>.
</p>

<?php if (count($emails) > 0): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width: 15%;">Plage d'adresses</th>
			<th style="width: 15%;">Admin</th>
			<th style="width: 55%;">Raison</th>
			<th style="width: 5%;">Débannir</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($emails as $email): ?>
		<tr>
			<td><?php echo htmlspecialchars($email->getEmail()) ?></td>
			<td>
				<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $email->getUser()->getId(), 'slug' => rewrite($email->getUser()->getUsername()))) ?>">
					<?php echo $email->getUser()->getUsername() ?>
				</a>
			</td>
			<td><?php echo $view['messages']->parse($email->getReason()) ?></td>
			<td class="center">
				<a href="<?php echo $view['router']->generate('zco_user_admin_deleteBannedEmail', array('id' => $email->getId())) ?>">
					<img src="/img/supprimer.png" alt="Débannir" title="Débannir cette plage d'adresses mail" />
				</a>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php endif ?>
