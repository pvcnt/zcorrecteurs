<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Analyser une adresse IP</h1>

<p>
	Cette page vous permet de trouver la liste des actions effectuées par un
	membre répondant à cette IP, ainsi que les membres l'ayant utilisée. Vous
	pouvez utiliser le joker * pour rechercher une plage d'IP.
</p>

<form method="get" action="<?php echo $view['router']->path('zco_user_ips_analyze') ?>" class="form-inline">
    <input type="text" name="ip" id="ip" value="<?php echo htmlspecialchars($ip) ?>" placeholder="94.23.204.81" />
    <input type="submit" class="btn btn-primary" value="Envoyer" />
</form>

<?php if ($ip){ ?>
<p>
	<strong><?php echo $nombre ?> utilisateur<?php echo pluriel($nombre) ?></strong>
	<?php echo pluriel($nombre, 'ont', 'a') ?> été trouvé<?php echo pluriel($nombre) ?>
	à partir de la recherche <em><?php echo htmlspecialchars($ip) ?></em> -

	<a href="<?php echo $view['router']->path('zco_user_ips_locate', ['ip' => $ip]) ?>">Localiser cette IP (<?php echo $pays ?>)</a> -
	<a href="http://dns.l4x.org/<?php echo htmlspecialchars($ip) ?>">Résoudre cette IP</a>
	<?php if(verifier('ips_bannir')){ ?>
	 - <a href="<?php echo $view['router']->path('zco_user_ips_ban', ['ip' => $ip]) ?>">Bannir cette IP</a>
	 <?php } ?>
</p>

<?php if (!empty($utilisateurs)){ ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Dernière IP connue</th>
			<th>Pseudo</th>
			<th>Période</th>
			<th>Validé ?</th>
			<th>Date d'inscription</th>
			<th>Messages</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($utilisateurs as $key => $ip){ ?>
		<tr class="<?php echo $key % 2 ? 'odd' : 'even' ?>">
			<td class="center">
				<a href="<?php echo $view['router']->path('zco_user_ips_analyze', ['ip' => long2ip($ip->Utilisateur['ip'])]) ?>">
					<?php echo long2ip($ip->Utilisateur['ip']); ?>
				</a> -
				<a href="<?php echo $view['router']->path('zco_user_ips_ban', ['ip' => long2ip($ip->Utilisateur['ip'])]) ?>">
					Bannir
				</a>
			</td>
			<td><?php echo $ip->Utilisateur ?></td>
			<td>
				<?php echo dateformat($ip['date_debut']); ?> &rarr;
				<?php echo dateformat($ip['date_last']); ?>
			</td>
			<td class="center">
				<img src="/bundles/zcocore/img/generator/boolean-<?php echo $ip->Utilisateur['valide'] ? 'yes' : 'no' ?>.png" alt="<?php echo $ip->Utilisateur['valide'] ? 'Oui' : 'Non' ?>" />
			</td>
			<td><?php echo dateformat($ip->Utilisateur['date_inscription']) ?></td>
			<td class="center"><?php echo $ip->Utilisateur['forum_messages'] ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } ?>
<?php } ?>
