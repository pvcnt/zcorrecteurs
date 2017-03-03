<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Adresses IP utilisées sur plusieurs comptes</h1>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Adresse IP</th>
			<th>Nombre de comptes</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($doublons as $doublon){ ?>
		<tr>
			<td class="center">
				<a href="<?php echo $view['router']->path('zco_user_ips_analyze', ['ip' => long2ip($doublon['ip_ip'])]) ?>">
					<?php echo long2ip($doublon['ip_ip']); ?>
				</a>
			</td>
			<td class="center"><?php echo $doublon['nombre']; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>