<?php $view->extend('::layouts/default.html.php') ?>

<h1>Analyser une adresse IP</h1>

<p>
	Cette page vous permet de trouver la liste des actions effectuées par un
	membre répondant à cette IP, ainsi que les membres l'ayant utilisée. Vous
	pouvez utiliser le joker * pour rechercher une plage d'IP.
</p>

<fieldset>
	<legend>Analyser une adresse IP</legend>
	<form method="get" action="">
		<label for="ip">Adresse IP : </label>
		<input type="text" name="ip" id="ip" value="<?php echo !empty($_GET['ip']) ? $_GET['ip'] : '' ?>" />
		<input type="submit" value="Envoyer" />
	</form>
</fieldset>

<?php if (!empty($_GET['ip'])){ ?>
<br /><hr />

<p>
	<strong><?php echo $nombre ?> utilisateur<?php echo pluriel($nombre) ?></strong>
	<?php echo pluriel($nombre, 'ont', 'a') ?> été trouvé<?php echo pluriel($nombre) ?>
	à partir de la recherche <em><?php echo htmlspecialchars($_GET['ip']) ?></em> -

	<a href="localiser.html?ip=<?php echo $_GET['ip']; ?>">Localiser cette IP (<?php echo $pays; ?>)</a> -
	<a href="http://dns.l4x.org/<?php echo $_GET['ip']; ?>">Résoudre cette IP</a>
	<?php if(verifier('ips_bannir')){ ?>
	 - <a href="bannir.html?ip=<?php echo $_GET['ip']; ?>">Bannir cette IP</a>
	 <?php } ?>
</p>

<?php if (!empty($utilisateurs)){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 15%;">Dernière IP connue</th>
			<th style="width: 15%;">Pseudo</th>
			<th style="width: 25%;">Période</th>
			<th style="width: 5%;">Validé ?</th>
			<th style="width: 20%;">Date d'inscription</th>
			<th style="width: 7%;">Messages</th>
			<th style="width: 15%;">Groupe</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($utilisateurs as $key => $ip){ ?>
		<tr class="<?php echo $key % 2 ? 'odd' : 'even' ?>">
			<td class="centre">
				<a href="analyser.html?ip=<?php echo long2ip($ip->Utilisateur['ip']); ?>">
					<?php echo long2ip($ip->Utilisateur['ip']); ?>
				</a> -
				<a href="bannir.html?ip=<?php echo long2ip($ip->Utilisateur['ip']); ?>">
					Bannir
				</a>
			</td>
			<td><?php echo $ip->Utilisateur ?></td>
			<td>
				<?php echo dateformat($ip['date_debut']); ?> &rarr;
				<?php echo dateformat($ip['date_last']); ?>
			</td>
			<td class="centre">
				<img src="/bundles/zcocore/img/generator/boolean-<?php echo $ip->Utilisateur['valide'] ? 'yes' : 'no' ?>.png" alt="<?php echo $ip->Utilisateur['valide'] ? 'Oui' : 'Non' ?>" />
			</td>
			<td><?php echo dateformat($ip->Utilisateur['date_inscription']) ?></td>
			<td class="centre"><?php echo $ip->Utilisateur['forum_messages'] ?></td>
			<td class="centre" style="color: <?php echo $ip->Utilisateur->Groupe['class'] ?>;">
				<?php echo htmlspecialchars($ip->Utilisateur->Groupe['nom']) ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } ?>
<?php } ?>
