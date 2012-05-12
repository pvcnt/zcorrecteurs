<?php $view->extend('::layouts/default.html.php') ?>

<h1>Adresses IP utilisées sur plusieurs comptes</h1>

<table class="UI_items">
	<thead>
		<tr>
			<th>Adresse IP</th>
			<th>Nombre de comptes</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($doublons as $doublon){ ?>
		<tr>
			<td class="centre">
				<a href="analyser.html?ip=<?php echo long2ip($doublon['ip_ip']); ?>">
					<?php echo long2ip($doublon['ip_ip']); ?>
				</a>
			</td>
			<td class="centre"><?php echo $doublon['nombre']; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>