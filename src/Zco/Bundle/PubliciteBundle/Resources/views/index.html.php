<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPubliciteBundle::_onglets.html.php', array('currentTab' => 'campaigns')) ?>

<?php if (count($campagnes) > 0){ ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Nom de la campagne</th>
			<?php if (verifier('publicite_voir') && isset($_GET['all'])){ ?>
			<th>Créateur</th>
			<?php } ?>
			<th>État</th>
			<th>Impressions</th>
			<th>Clics</th>
			<th>Taux de clics</th>
		</tr>
	</thead>

	<tfoot>
		<tr class="bold">
			<td colspan="<?php echo (verifier('publicite_voir') && isset($_GET['all'])) ? '3' : '2' ?>">Totaux</td>
			<td class="center"><?php echo $view['humanize']->numberformat($total_aff, 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($total_clic, 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($total_taux) ?> %</td>
		</tr>
	</tfoot>

	<tbody>
		<?php foreach ($campagnes as $campagne){ ?>
		<tr>
			<td>
				<a href="campagne-<?php echo $campagne['id'] ?>.html"><?php echo htmlspecialchars($campagne['nom']) ?></a>
			</td>
			<?php if (verifier('publicite_voir') && isset($_GET['all'])){ ?>
			<td><?php echo $campagne->Utilisateur ?></td>
			<?php } ?>
			<td class="center"><?php echo $campagne->getEtatFormat() ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($campagne['nb_affichages'], 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($campagne['nb_clics'], 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($campagne->getTauxClics()) ?> %</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<div class="box" style="margin-top: 20px;">
	<form method="get" action="" id="form_type" class="form-horizontal">
		<?php if (verifier('publicite_voir') && isset($_GET['all'])){ ?>
		<input type="hidden" name="all" value="1" />
		<?php } ?>
		<?php if (!empty($_GET['etat'])){ foreach ($_GET['etat'] as $etat){ ?>
		<input type="hidden" name="etat[]" value="<?php echo $etat ?>" />
		<?php } } ?>
		
		<div class="control-group">
			<label for="type" class="control-label">Choisissez un graphique</label>
			<div class="controls">
				<select name="type" id="type" onchange="$('img_stats').src = 'graphique-campagnes.html?'+$('form_type').toQueryString();">
					<optgroup label="Données volumétriques sur les 15 derniers jours">
						<option value="clic">Nombre de clics</option>
						<option value="affichage">Nombre d'impressions</option>
						<option value="taux">Taux de clics</option>
					</optgroup>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label for="campagnes" class="control-label">Campagnes à afficher</label>
			<div class="controls">
				<?php foreach ($campagnes as $i => $campagne){ ?>
				<input type="checkbox" name="ids[]" onchange="$('img_stats').src = 'graphique-campagnes.html?'+$('form_type').toQueryString();" value="<?php echo $campagne['id'] ?>" id="campagne_<?php echo $campagne['id'] ?>" checked="checked" />
				<label for="campagne_<?php echo $campagne['id'] ?>" class="nofloat <?php echo $couleurs[$i % count($couleurs)] ?>" style="margin-right: 10px;">
					<?php echo htmlspecialchars($campagne['nom']) ?>
				</label>
				<?php } ?>
			</div>
		</div>
		<noscript><input type="submit" value="Aller" /></noscript>
	</form>
	
	<div class="center">
		<img id="img_stats" src="graphique-campagnes.html?type=clic<?php echo $query_string ?>" alt="Graphique de statistiques" />
	</div>
</div>
<?php } else{ ?>
<p>Aucune campagne publicitaire n'a été trouvée.</p>
<?php } ?>

<p class="box bold center" style="margin-top: 20px;">
	<?php if (verifier('publicite_voir')){ ?>
		<?php if (isset($_GET['all'])){ ?>
		<a href="?">Afficher uniquement mes campagnes</a>
		<?php } else{ ?>
		<a href="?all=1">Afficher toutes les campagnes du site</a>
		<?php } ?><br />
	<?php } ?>

	<?php if (empty($_GET['etat']) || !in_array('supprime', $_GET['etat'])){ ?>
	<a href="?etat[]=supprime&etat[]=en_cours&etat[]=pause&etat[]=termine<?php echo isset($_GET['all']) && verifier('publicite_voir') ? '&all=1' : '' ?>">Montrer les campagnes supprimées</a>
	<?php } else{ ?>
	<a href="?etat[]=en_cours&etat[]=pause&etat[]=termine<?php echo isset($_GET['all']) && verifier('publicite_voir') ? 'all=1' : '' ?>">Masquer les campagnes supprimées</a>
	<?php } ?>
</p>

<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css') ?>