<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoPubliciteBundle::_onglets.html.php') ?>

<h1>Rapport personnalisé</h1>

<p>
	Nous vous fournissons déjà des statistiques prêtes à l'emploi sur
	l'ensemble de vos campagnes, ou bien individuellement par publicité.
	Cependant, si vous désirez avoir une vue d'ensemble des effets de
	votre campagne plus fine, les rapports personnalisés sont destinés
	à cela. Ils sont de plus exportables dans divers formats pour être
	exploités ensuite dans d'autres outils, comme un tableur.
</p>

<form method="get" action="">
	<fieldset>
		<legend>Configuration du rapport</legend>

		<table class="UI_wrapper"><tr><td>
		<label for="element">Rapport concernant :</label>
		<select name="element" id="element" onchange="if (this.value == 'campagne') $('element_campagne').setStyle('display', 'inline'); else $('element_campagne').setStyle('display', 'none'); if (this.value == 'publicite') $('element_publicite').setStyle('display', 'inline'); else $('element_publicite').setStyle('display', 'none');">
			<option value="tout"<?php if (!empty($_GET['element']) && $_GET['element'] == 'tout') echo ' selected="selected"' ?>>Toutes mes campagnes</option>
			<?php if (verifier('publicite_voir')){ ?>
			<option value="tout2"<?php if (!empty($_GET['element']) && $_GET['element'] == 'tout2') echo ' selected="selected"' ?>>Toutes les campagnes du site</option>
			<?php } ?>
			<option value="campagne"<?php if (!empty($_GET['element']) && $_GET['element'] == 'campagne') echo ' selected="selected"' ?>>Choisir une campagne...</option>
			<option value="publicite"<?php if (!empty($_GET['element']) && $_GET['element'] == 'publicite') echo ' selected="selected"' ?>>Choisir une publicité...</option>
		</select>

		<select name="element_campagne" id="element_campagne" style="display: <?php echo (!empty($_GET['element']) && $_GET['element'] == 'campagne') ? 'inline' : 'none' ?>;">
			<?php foreach ($campagnes as $campagne){ ?>
			<option value="<?php echo $campagne['id'] ?>"<?php if (!empty($_GET['element_campagne']) && $_GET['element_campagne'] == $campagne['id']) echo ' selected="selected"' ?>>
				<?php echo htmlspecialchars($campagne['nom']) ?>
			</option>
			<?php } ?>
		</select>

		<select name="element_publicite" id="element_publicite" style="display: <?php echo (!empty($_GET['element']) && $_GET['element'] == 'publicite') ? 'inline' : 'none' ?>;">
			<?php foreach ($publicites as $publicite){ ?>
			<option value="<?php echo $publicite['id'] ?>"<?php if (!empty($_GET['element_publicite']) && $_GET['element_publicite'] == $publicite['id']) echo ' selected="selected"' ?>>
				<?php echo htmlspecialchars($publicite['titre']) ?>
			</option>
			<?php } ?>
		</select><br />

		<label for="granularite">Unité de temps :</label>
		<select name="granularite" id="granularite">
			<option value="jour"<?php if (!empty($_GET['granularite']) && $_GET['granularite'] == 'jour') echo ' selected="selected"' ?>>
				Jour
			</option>
			<option value="semaine"<?php if (!empty($_GET['granularite']) && $_GET['granularite'] == 'semaine') echo ' selected="selected"' ?>>
				Semaine
			</option>
			<option value="mois"<?php if (!empty($_GET['granularite']) && $_GET['granularite'] == 'mois') echo ' selected="selected"' ?>>
				Mois
			</option>
		</select></td>

		<td><label>Période :</label>
		<input type="radio" name="periode" value="predef" id="periode_predef"<?php if (empty($_GET['periode']) || $_GET['periode'] == 'predef') echo ' checked="checked"' ?> />
		<select name="predef" id="predef" onchange="$('periode_predef').checked = true;">
			<option value="semaine"<?php if (!empty($_GET['predef']) && $_GET['predef'] == 'semaine') echo ' selected="selected"' ?>>
				Les 7 derniers jours
			</option>
			<option value="mois"<?php if (!empty($_GET['predef']) && $_GET['predef'] == 'mois') echo ' selected="selected"' ?>>
				Ce mois-ci
			</option>
			<option value="mois_dernier"<?php if (!empty($_GET['predef']) && $_GET['predef'] == 'mois_dernier') echo ' selected="selected"' ?>>
				Le mois dernier
			</option>
			<option value="toute"<?php if (!empty($_GET['predef']) && $_GET['predef'] == 'toute') echo ' selected="selected"' ?>>
				Toute la durée de vie
			</option>
		</select><br />

		<input type="radio" name="periode" value="perso" id="periode_perso" style="margin-left: 200px;"<?php if (!empty($_GET['periode']) && $_GET['periode'] == 'perso') echo ' checked="checked"' ?> />
		Du <?php echo $view->get('widget')->datePicker('date_debut', !empty($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-d', strtotime('-7 days')), array(), array('onclick' => "$('periode_perso').checked= true;")) ?>
		au <?php echo $view->get('widget')->datePicker('date_fin', !empty($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d', strtotime('yesterday')), array(), array('onclick' => "$('periode_perso').checked= true;")) ?>
		</td></tr></table>

		<div class="send"><input type="submit" name="submit" value="Générer le rapport" /></div>
	</fieldset>
</form>

<?php if (!empty($resultats)){ ?>
<div style="margin-top: 20px;"></div>

<?php /*
<div style="float: right;">
	<?php if (!isset($rapport)){ ?>
	<form method="get" action="">
		<?php foreach ($_GET as $k => $v){ if (!in_array($k, array('id', 'id2', 'page', 'p', 'act', 'titre', 'intitule'))){ ?>
		<input type="hidden" name="<?php echo $k ?>" value="<?php echo $v ?>" />
		<?php } } ?>
		<label for="intitule" class="nofloat">Sauvegarder le rapport :</label>
		<input type="text" name="intitule" id="intitule" size="40" />
		<input type="submit" value="Enregistrer" />
	</form>
	<?php } else{ ?>
	Vous visualisez le rapport intitulé <strong><?php echo htmlspecialchars($rapport['intitule']) ?></strong>.
	<?php } ?>
</div>*/ ?>

<h2><?php echo dateformat($date_debut, DATE) ?> - <?php echo dateformat($date_fin, DATE) ?></h2>
<div style="clear: right;"></div>

<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 40%;">Date</th>
			<th style="width: 20%;">Impressions</th>
			<th style="width: 20%;">Clics</th>
			<th style="width: 20%;">Taux de clics</th>
		</tr>
	</thead>

	<tfoot>
		<tr class="gras">
			<td>Totaux</td>
			<td class="centre"><?php echo $view['humanize']->numberformat($resultats['totaux']['nb_affichages'], 0) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($resultats['totaux']['nb_clics'], 0) ?></td>
			<td class="centre">-</td>
		</tr>
		<tr class="gras">
			<td>Moyennes</td>
			<td class="centre"><?php echo $view['humanize']->numberformat($resultats['moyennes']['nb_affichages'], 0) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($resultats['moyennes']['nb_clics'], 0) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($resultats['moyennes']['taux_clics']) ?> %</td>
		</tr>
	</tfoot>

	<tbody>
		<?php foreach ($resultats['lignes'] as $i => $ligne){ ?>
		<tr class="<?php echo $i % 2 ? 'odd' : 'even' ?>">
			<td><?php echo $ligne['date'] ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($ligne['nb_affichages'], 0) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($ligne['nb_clics'], 0) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($ligne['taux_clics']) ?> %</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } ?>