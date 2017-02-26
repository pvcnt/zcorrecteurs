<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Qui est en ligne ?</h1>

<p>
	Cette page affiche les personnes en train de visiter le site qui ont été 
	actives depuis les <?php echo NOMBRE_MINUTES_CONNECTE ?> dernière<?php echo pluriel(NOMBRE_MINUTES_CONNECTE) ?>
	minute<?php echo pluriel(NOMBRE_MINUTES_CONNECTE) ?>.
</p>

<p class="center">
	Il y a actuellement <?php echo $loggedUsers ?> membre<?php echo pluriel($loggedUsers) ?> et 
	<?php echo $anonymousUsers ?> visiteur<?php echo pluriel($anonymousUsers) ?>
	en ligne sur le site.
</p>

<p class="bold center">
	<?php if (!$showAnonymousUsers): ?>
	<a href="<?php echo $view['router']->path('zco_user_online_all') ?>">
		Montrer tous les connectés, y compris les visiteurs 
		(<?php echo $anonymousUsers ?> de plus)
	</a>
	<?php else: ?>
	<a href="<?php echo $view['router']->path('zco_user_online') ?>">
		Ne montrer que les membres
	</a>
	<?php endif; ?>
</p>

<table class="table table-avatar">
	<thead>
		<tr>
			<th style="width: 100px;">Avatar</th>
			<th style="width: 200px;">Pseudo</th>
			<th>Dernière action</th>
			<th>Section du site visitée</th>
			<?php if (verifier('ips_analyser')): ?>
			<th>IP</th>
			<?php endif ?>
		</tr>
	</thead>
	
	<tbody>
		<?php if (count($online) > 0): ?>
			<?php foreach ($online as $user): ?>
			<tr>
				<td class="avatar">
					<?php if ($user->isAuthenticated()): ?>
					<div class="avatar">
						<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getUser()->getId(), 'slug' => rewrite($user->getUser()->getUsername()))) ?>">
							<img src="<?php echo htmlspecialchars($user->getUser()->getAvatar()) ?>" 
								alt="Avatar de <?php echo htmlspecialchars($user->getUser()->getUsername()) ?>" />
						</a>
						<?php endif ?>
					</div>
				</td>
				<td class="center">
					<?php if ($user->isAuthenticated() && $user->getUser()->getId()): ?>
					<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getUser()->getId(), 'slug' => rewrite($user->getUser()->getUsername()))) ?>"
						style="color: <?php echo htmlspecialchars($user->getUser()->Groupe['class']) ?>;" 
						title="Groupe : <?php echo htmlspecialchars($user->getUser()->Groupe['nom']) ?>">
						<?php echo htmlspecialchars($user->getUser()->getUsername()) ?>
					</a>
					<?php elseif ($user->isBot()): ?>
					Robot d'indexation : <em><?php echo htmlspecialchars($user->getBotName()) ?></em>
					<?php else: ?>
					Anonyme
					<?php endif ?>
				</td>
				<td class="center">
					<?php echo $view['humanize']->dateformat($user->getLastActionDate()) ?>
				</td>
				<td class="center">
					<?php echo htmlspecialchars($user->getCategory()) ?>
				</td>
				<?php if(verifier('ips_analyser')){ ?>
				<td class="center">
					<a href="/ips/analyser.html?ip=<?php echo long2ip($user->getIpAddress()); ?>">
						<?php echo long2ip($user->getIpAddress()); ?>
					</a>
				</td>
				<?php } ?>
			</tr>
			<?php endforeach ?>
		<?php else: ?>
			<tr>
				<td colspan="<?php echo verifier('ips_analyser') ? 5 : 4 ?>">
					Aucun membre n'a été trouvé.
				</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>
