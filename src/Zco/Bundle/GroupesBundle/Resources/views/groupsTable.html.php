<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Groupe</th>
			<th>Logo</th>
			<th>Effectifs</th>
            <th>Vérifier</th>
            <th>Droits</th>
			<th>Modifier</th>
			<th>Supprimer</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($ListerGroupes as $g){ ?>
		<tr>
			<td>
                <?php echo htmlspecialchars($g['groupe_nom']); ?>
                <?php if ($g['groupe_code']): ?>
                    (<?php echo htmlspecialchars($g['groupe_code']); ?>)
                <?php endif ?>
            </td>
			<td class="center">
				<?php if(!empty($g['groupe_logo'])){ ?>
				<img src="<?php echo htmlspecialchars($g['groupe_logo']); ?>" alt="Logo du groupe" />
				<?php } else echo '-'; ?>
			</td>
			<td class="center">
				<?php if ($g['groupe_code'] != \Groupe::ANONYMOUS) { ?>
				<?php echo $view['humanize']->numberformat($g['groupe_effectifs'], 0); ?>
				<?php } else echo '-'; ?>
			</td>
            <td class="center">
                <a href="<?php echo $view['router']->path('zco_groups_checkCredentials', ['id' => $g['groupe_id']]) ?>"><img src="/img/verifier.png" alt="Vérifier" /></a>
            </td>
            <td class="center">
                <a href="<?php echo $view['router']->path('zco_groups_changeCredentials', ['id' => $g['groupe_id']]) ?>"><img src="/img/droits.png" alt="Droits" /></a>
            </td>
			<td class="center">
				<?php if($g['groupe_code'] != \Groupe::ANONYMOUS){ ?>
				<a href="<?php echo $view['router']->path('zco_groups_edit', ['id' => $g['groupe_id']]) ?>"><img src="/img/editer.png" alt="Modifier" /></a>
				<?php } else echo '-'; ?>
			</td>
			<td class="center">
				<?php if($g['groupe_code'] != \Groupe::ANONYMOUS){ ?>
				<a href="<?php echo $view['router']->path('zco_groups_delete', ['id' => $g['groupe_id']]) ?>"><img src="/img/supprimer.png" alt="Supprimer" /></a>
				<?php } else echo '-'; ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>