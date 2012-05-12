<?php $view->extend('::layouts/default.html.php') ?>

<h1>Voir la liste des alertes</h1>

<?php if(!empty($InfosSujet)){ ?>
<p>Vous êtes en train de visualiser la liste des alertes pour le sujet suivant : <strong><a href="sujet-<?php echo $_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']); ?>.html"><?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?></a></strong>.</p>
<?php } else{ ?>
<p>Vous êtes en train de visualiser la liste des alertes sur tout le site (pour les forums que vous modérez).</p>
<?php } ?>

<form method="get" action="">
	<fieldset>
		<legend>Filtrer les alertes</legend>
		<label for="solved">Voir les alertes : </label>
		<select name="solved" id="solved" onchange="document.location = '?solved=' + this.value;">
			<option value="" selected="selected">Toutes</option>
			<option value="0"<?php if(isset($_GET['solved']) && $_GET['solved'] === '0') echo ' selected="selected"'; ?>>Non résolues</option>
			<option value="1"<?php if(isset($_GET['solved']) && $_GET['solved'] === '1') echo ' selected="selected"'; ?>>Résolues</option>
		</select>
		<noscript><input type="submit" value="Aller"/></noscript>
	</fieldset>
</form>

<?php if(!empty($InfosSujet)){ ?>
<p class="gras centre"><a href="alertes.html">Voir toutes les alertes du site</a></p>
<?php } ?>

<?php if(count($Alertes) > 0){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 15%;">Titre du sujet</th>
			<th style="width: 10%;">Pseudo</th>
			<th style="width: 10%;">Date</th>
			<th style="width: 15%;">Alerte résolue</th>
			<?php if(verifier('ips_analyser')){ ?>
			<th style="width: 5%;">IP</th>
			<?php } ?>
			<th style="width: 40%;">Raison</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($Alertes as $alerte){ ?>
		<tr<?php if(!$alerte['resolu']) echo ' class="UI_errorrow"'; ?>>
			<td>
				<?php if($alerte->Sujet['ferme']){ ?>
					<img src="/bundles/zcoforum/img/cadenas.png" alt="Fermé" title="Ce sujet a été fermé" />
				<?php } ?>
				<?php if($alerte->Sujet['corbeille']){ ?>
					<img src="/bundles/zcoforum/img/corbeille.png" alt="Corbeille" title="Ce sujet a été jeté  la corbeille" />
				<?php } ?>
				<?php if($alerte->Sujet['resolu']){ ?>
					<img src="/bundles/zcoforum/img/resolu.png" alt="Résolu" title="Ce sujet est marqué comme résolu" />
				<?php } ?>

				<a href="sujet-<?php echo $alerte->Sujet['id']; ?>.html"><?php echo htmlspecialchars($alerte->Sujet['titre']); ?></a>
			</td>
			<td>
				<a href="/membres/profil-<?php echo $alerte->Utilisateur['id']; ?>-<?php echo rewrite($alerte->Utilisateur['pseudo']); ?>.html" style="color: <?php echo $alerte->Utilisateur->Groupe['class']; ?>;">
					<?php echo htmlspecialchars($alerte->Utilisateur['pseudo']); ?>
				</a>
			</td>
			<td class="centre"><?php echo dateformat($alerte['date']); ?></td>
			<td class="centre">
				<?php if(!$alerte['resolu']){ ?>
				Non - <a href="?resolu=<?php echo $alerte['id']; ?>">Résoudre</a>
				<?php } else{ ?>
				Par <a href="/membres/profil-<?php echo $alerte->Admin['id']; ?>-<?php echo rewrite($alerte->Admin['pseudo']); ?>.html" style="color: <?php echo $alerte->Admin->Groupe['class']; ?>;">
					<?php echo htmlspecialchars($alerte->Admin['pseudo']); ?>
				</a> -
				<a href="?nonresolu=<?php echo $alerte['id']; ?>">N'est plus résolue</a>
				<?php } ?>
			</td>
			<?php if(verifier('ips_analyser')){ ?>
			<td>
				<a href="/ips/analyser.html?ip=<?php echo long2ip($alerte['ip']); ?>">
					<?php echo long2ip($alerte['ip']); ?>
				</a>
			</td>
			<?php } ?>
			<td><?php echo $view['messages']->parse($alerte['raison'], array(
				'files.entity_id' => $alerte['id'],
				'files.entity_class' => 'ForumAlerte',
			)); ?>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php } else{ ?>
<p>Aucune alerte n'a été trouvée dans les forums que vous modérez.</p>
<?php } ?>
