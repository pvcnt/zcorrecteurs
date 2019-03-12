<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Rechercher une adresse mail</h1>

<p>
	Cette page vous permet de trouver des membres à partir de leur adresse
	mail. Vous pouvez utiliser le joker *.
</p>

<fieldset>
	<form method="get" action="<?php echo $view['router']->path('zco_user_admin_searchEmail') ?>" class="form-horizontal">
		<input type="text" name="email" id="email" value="<?php echo htmlspecialchars($email) ?>" placeholder="*@gmail.com" />
		<input type="submit" class="btn btn-primary" value="Envoyer" />
	</form>
</fieldset>

<?php if (!empty($email)): ?>
<br /><hr />

<p class="center">
	<strong><?php echo count($users) ?> utilisateur<?php echo pluriel(count($users)) ?></strong>
	<?php echo pluriel(count($users), 'ont', 'a') ?> été trouvé<?php echo pluriel(count($users)) ?>
	à partir de la recherche <em><?php echo htmlspecialchars($email) ?></em>.
</p>

<?php if (count($users) > 0): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width: 20%;">Pseudo</th>
			<?php if (verifier('ips_analyser')): ?>
			<th style="width: 8%;">IP</th>
			<?php endif ?>
			<th style="width: 20%;">Adresse mail</th>
			<th style="width: 5%;">Validé&nbsp;?</th>
			<th style="width: 20%;">Date d'inscription</th>
			<th style="width: 7%;">Messages</th>
			<th style="width: 20%;">Groupe</th>
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
			<?php if (verifier('ips_analyser')): ?>
			<td class="center">
				<a href="<?php echo $view['router']->path('zco_user_ips_locate', ['ip' => long2ip($user->getLastIpAddress())]) ?>">
					<?php echo long2ip($user->getLastIpAddress()); ?>
				</a>
			</td>
			<?php endif ?>
			<td><?php echo htmlspecialchars($user->getEmail()) ?></td>
			<td class="center">
				<img src="/bundles/zcocore/img/generator/boolean-<?php echo $user->isAccountValid() ? 'yes' : 'no' ?>.png" 
					alt="<?php echo $user->isAccountValid() ? 'Oui' : 'Non' ?>" />
			</td>
			<td><?php echo dateformat($user->getRegistrationDate()) ?></td>
			<td class="center"><?php echo $user->getNbMessages() ?></td>
			<td class="center"><?php echo htmlspecialchars($user->getGroup()->getName()) ?></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php endif ?>
<?php endif ?>