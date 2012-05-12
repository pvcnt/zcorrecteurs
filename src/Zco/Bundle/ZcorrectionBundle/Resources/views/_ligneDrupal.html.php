<?php
$etats = array(null, 'Non pris en charge.', 'En cours de correction', 'En attente', 'Corrigé.');
?>

<tr<?php if ($s['priority'] >= 3) echo ' class="UI_errorrow"'; elseif (in_array($s['state'], array(RECORRECTION_DEMANDEE, RECORRECTION))) echo ' class="UI_inforow"'; ?>>
	<td class="centre">[<?php echo htmlspecialchars($s['partenaire']) ?>]</td>
	<td><a href="<?php echo $s['path'] ?>"><?php echo htmlspecialchars($s['title']) ?></a></td>
	<td class="centre">
		<a href="http://tickets.corrigraphie.org/user/<?php echo $s['user']['uid'] ?>">
			<?php echo htmlspecialchars($s['user']['name']) ?>
		</a>
	</td>
	<td class="centre"><?php echo dateformat($s['created'], DATE) ?></td>
	
	<?php if ($type !== 'admin'): ?>
		<td><?php echo $s['body']['und'][0]['safe_value'] ?></td>
	<?php endif; ?>
	
	<td>
		<?php if ($s['state'] == ENVOYE): ?>
			Non pris en charge.
		<?php elseif ($s['state'] == CORRECTION): ?>
			En cours de correction<?php if ($type !== 'correcteur'): ?> par <?php echo htmlspecialchars($s['assigned']['name']) ?><?php endif; ?>.
		<?php elseif ($s['state'] == RECORRECTION): ?>
			En cours de recorrection<?php if ($type !== 'correcteur'): ?> par <?php echo htmlspecialchars($s['assigned']['name']) ?><?php endif; ?>.
		<?php elseif ($s['state'] == RECORRECTION_DEMANDEE && $s['assigned']): ?>
			Première correction par <?php echo htmlspecialchars($s['assigned']['name']) ?>.
		<?php elseif ($s['state'] == RECORRECTION_DEMANDEE): ?>
			En attente de recorrection.
		<?php elseif ($s['state'] == TERMINE_CORRIGE): ?>
			Corrigé par <?php echo htmlspecialchars($s['assigned']['name']) ?>.
		<?php else: ?>
			—
		<?php endif; ?>
	</td>
	
	<?php if ($type === 'correcteur'): ?>
	<td class="centre">
		<a href="<?php echo $s['path'] ?>">
			<img src="/img/zcorrection/corriger.png" alt="Voir" title="Voir" />
		</a>
		<a href="<?php echo $s['path'] ?>#comment-form">
			<img src="/img/zcorrection/abandonner.png" alt="Abandonner" title="Abandonner" />
		</a>
	</td>
	<?php elseif ($type === 'admin'): ?>
	<td class="centre" style="vertical-align: middle;">
		-
	<?php else: ?>
	<td class="centre">
		<a href="<?php echo $s['path'] ?>">
			<img src="/img/zcorrection/voir.png" alt="Voir" title="Voir" />
		</a>
		<?php if (verifier('zcorriger')): ?>
			<a href="<?php echo $s['path'] ?>#comment-form">
				<img src="/img/zcorrection/prendre.png" alt="Prendre en charge" title="Je m'en charge !" />
			</a>
		<?php endif; ?>
	</td>
	<?php endif; ?>
</tr>