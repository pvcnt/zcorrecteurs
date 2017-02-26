<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des adresses IP bannies</h1>

<p>Voici la liste des adresses IP bannies sur ce site. Les visiteurs l'utilisant ne peuvent alors plus le visiter.</p>

<ul>
	<li><a href="?">Voir tous les bans IP</a></li>
	<li><a href="?fini=0">Voir seulement les bans IP en cours</a></li>
	<li><a href="?fini=1">Voir seulement les bans IP finis</a></li>
	<li><form method="get" action=""><label for="ip" class="nofloat">Voir tous les bans de l'adresse IP suivante :</label><br />
	<input type="text" name="ip" id="ip" value="<?php echo isset($_GET['ip']) ? $_GET['ip'] : ''; ?>" />
	<input type="submit" value="Aller" /></form></li>
</ul>
<?php if(verifier('ips_bannir')): ?>
	<p class="gras centre"><a href="bannir.html">Bannir une adresse IP</a></p>
<?php endif; ?>

<?php if($ListerIPs){ ?>
<table class="UI_items">
	<thead>
	<tr class="header_message">
		<th style="width: 10%;">IP</th>
		<th style="width: 10%;">Admin</th>
		<th style="width: 10%;">Début</th>
		<th style="width: 15%;">Fin</th>
		<th style="width: 24%;">Raison</th>
		<th style="width: 24%;">Raison admin</th>
		<th style="width: 7%;">Actions</th>
	</tr>
	</thead>
	<?php foreach($ListerIPs as $cle=>$valeur){ ?>
		<tr>
			<td class="centre"><a href="analyser.html?ip=<?php echo long2ip($valeur['ip_ip']); ?>"><?php echo long2ip($valeur['ip_ip']); ?></a></td>
			<td><a href="/membres/profil-<?php echo $valeur['utilisateur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html"><?php echo htmlspecialchars($valeur['utilisateur_pseudo']); ?></a></td>
			<td class="centre <?php echo $valeur['ip_fini'] ? 'rouge' : 'vertf'; ?>"><?php echo dateformat($valeur['ip_date_debut']); ?></td>
			<td class="centre <?php echo $valeur['ip_fini'] ? 'rouge' : 'vertf'; ?>"><?php
			if($valeur['ip_date_fin'] !== 'Jamais')
			{
				echo dateformat($valeur['ip_date_fin'], DATE);
			}
			else
			{
				echo 'Jamais';
			}
			if($valeur['ip_duree_restante'] > 0 && !$valeur['ip_fini']){ ?><br />
			(dans <?php echo $valeur['ip_duree_restante']; ?> jour<?php echo pluriel($valeur['ip_duree_restante']); ?>)<?php } ?></td>
			<td><?php echo $view['messages']->parse($valeur['ip_raison']); ?></td>
			<td><?php echo $view['messages']->parse($valeur['ip_raison_admin']); ?></td>
			<td class="centre">
				<?php if(verifier('ips_bannir')): ?>
					<?php if(!$valeur['ip_fini']): ?>
						<a href="?fin=<?php echo $valeur['ip_id']; ?>"
						   title="Mettre fin à ce ban">
						   <img src="/img/refuser.png" alt="Mettre fin" /></a>
					<?php else: ?>
						<a href="?supprimer=<?php echo $valeur['ip_id']; ?>"
						   title="Supprimer">
						   <img src="/img/supprimer.png" alt="Supprimer" /></a>
					<?php endif; ?>
				<?php else: ?>
					-
				<?php endif; ?>
			</td>
		</tr>
	<?php } ?>
	<tbody>
<?php
?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucune adresse IP n'a été trouvée dans l'historique.</p>
<?php } ?>
