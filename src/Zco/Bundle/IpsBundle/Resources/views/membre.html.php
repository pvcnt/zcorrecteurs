<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des adresses IP d'un membre</h1>

<p>Voici la liste des adresses IP connues utilisées par le membre <strong><a href="/membres/profil-<?php echo $InfosMembre['utilisateur_id']; ?>-<?php echo rewrite($InfosMembre['utilisateur_pseudo']); ?>.html"><?php echo htmlspecialchars($InfosMembre['utilisateur_pseudo']); ?></a></strong>.</p>

<p class="centre gras"><a href="membre.html">Obtenir la liste des adresses IP d'un nouveau membre</a></p>

<?php if(!empty($ListerIPs)){ ?>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 35%;">IP</th>
			<th style="width: 10%;">Proxy</th>
			<th style="width: 40%;">Date</th>
			<th style="width: 15%;">Géolocalisation</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($ListerIPs as $ip){ ?>
		<tr>
			<td class="centre">
				<a href="analyser.html?ip=<?php echo long2ip($ip['ip_ip']); ?>"><?php echo long2ip($ip['ip_ip']); ?></a>
				 - 	<a href="localiser.html?ip=<?php echo long2ip($ip['ip_ip']); ?>">
				 		Localiser
				 	</a>
				<?php if(verifier('ips_bannir')){ ?>
				 - 	<a href="bannir.html?ip=<?php echo long2ip($ip['ip_ip']); ?>">
				 		Bannir cette IP
				 	</a>
				<?php } ?>
			</td>
			<td class="centre"><?php if(!empty($ip['ip_proxy'])) echo long2ip($ip['ip_proxy']); ?></td>
			<td class="centre"><?php echo dateformat($ip['ip_date_debut']); ?> =&gt; <?php echo dateformat($ip['ip_date_last']); ?></td>
			<td class="centre"><?php echo !empty($ip['ip_localisation']) ? htmlspecialchars($ip['ip_localisation']) : 'Inconnu'; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php } else{ ?>
<p>Nous ne possédons aucun historique d'adresses IP pour ce membre.</p>
<?php } ?>
