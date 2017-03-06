<?php $view->extend('::layouts/default.html.php') ?>

<p>
	Les groupes sont un outil permettant de regrouper les utilisateurs. Chacun appartient à un et un seul groupe. Les droits de chaque
	groupe sont configurables individuellement, et les membres de ce groupe en héritent automatiquement.
</p>

<h1>Liste des groupes « principaux »</h1>

<p>
	Il y a actuellement <strong><?php echo count($ListerGroupes); ?> groupes principaux</strong> enregistrés.
</p>

<div class="UI_box gras centre"><a href="ajouter.html">Ajouter un groupe</a></div>

<?php afficher_liste_groupes($view, $ListerGroupes); ?>

<h1>Liste des groupes « secondaires »</h1>

<p>
	Il y a actuellement <strong><?php echo count($ListerGroupesSecondaires); ?> groupes secondaires</strong> enregistrés.
</p>

<?php afficher_liste_groupes($view, $ListerGroupesSecondaires, 'only_assigned=1'); ?>

<?php
function afficher_liste_groupes($view, $ListerGroupes, $get = '') {
	?>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 25%;">Groupe</th>
			<th style="width: 25%;">Logo</th>
			<th style="width: 10%;">Effectifs</th>
			<?php if(verifier('groupes_changer_droits')){ ?>
			<th style="width: 10%;">Droits</th>
			<th style="width: 10%;">Vérifier</th>
			<?php } if(verifier('groupes_gerer')){ ?>
			<th style="width: 10%;">Editer</th>
			<th style="width: 10%;">Supprimer</th>
			<?php } ?>
		</tr>
	</thead>

	<tbody>
		<?php foreach($ListerGroupes as $g){ ?>
		<tr>
			<td>
                <span style="color: <?php echo htmlspecialchars($g['groupe_class']); ?>;"><?php echo htmlspecialchars($g['groupe_nom']); ?></span>
                <?php if ($g['groupe_code']): ?>
                    (<?php echo htmlspecialchars($g['groupe_code']); ?>)
                <?php endif ?>
            </td>
			<td class="centre">
				<?php if(!empty($g['groupe_logo'])){ ?>
				<img src="<?php echo htmlspecialchars($g['groupe_logo']); ?>" alt="" />
				<?php } else echo '-'; ?>
			</td>
			<td class="centre">
				<?php if($g['groupe_code'] != \Groupe::ANONYMOUS){ ?>
				<?php echo $view['humanize']->numberformat($g['groupe_effectifs'], 0); ?>
				<?php } else echo '-'; ?>
			</td>
			<?php if(verifier('groupes_changer_droits')){ ?>
			<td class="centre">
				<a href="droits-<?php echo $g['groupe_id']; ?>.html" title="Éditer les droits de ce groupe"><img src="/bundles/zcogroupes/img/droits.png" alt="Droits" /></a>
			</td>
			<td class="centre">
				<a href="verifier-<?php echo $g['groupe_id']; ?>.html<?php echo $get ? "?$get" : ''; ?>" title="Vérifier les droits de ce groupe"><img src="/bundles/zcogroupes/img/verifier.png" alt="Vérifier" /></a>
			</td>
			<?php } if(verifier('groupes_gerer')){ ?>
			<td class="centre">
				<?php if($g['groupe_code'] != \Groupe::ANONYMOUS){ ?>
				<a href="editer-<?php echo $g['groupe_id']; ?>.html" title="Éditer ce groupe"><img src="/img/editer.png" alt="Éditer" /></a>
				<?php } else echo '-'; ?>
			</td>
			<td class="centre">
				<?php if($g['groupe_code'] != \Groupe::ANONYMOUS){ ?>
				<a href="supprimer-<?php echo $g['groupe_id']; ?>.html" title="Supprimer ce groupe"><img src="/img/supprimer.png" alt="Supprimer" /></a>
				<?php } else echo '-'; ?>
			</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>
	<?php
}
