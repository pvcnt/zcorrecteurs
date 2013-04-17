<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPubliciteBundle::_onglets.html.php', array('campagne_id' => $publicite['campagne_id'])) ?>
<?php $convertisseurMois = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre') ?>

<h1><?php echo htmlspecialchars($publicite['titre']) ?></h1>

<div class="box">
	<table style="width: 100%;">
		<tr>
			<td class="droite gris" style="width: 50%;">Nom de la campagne</td>
			<td style="padding-left: 20px; width: 50%;">
				<?php echo htmlspecialchars($publicite->Campagne['nom']) ?>
			</td>
		</tr>
		<tr>
			<td class="droite gris">État de la campagne</td>
			<td style="padding-left: 20px;">
				<?php echo $publicite->Campagne->getEtatFormat() ?>
			</td>
		</tr>
		<tr>
			<td class="droite gris">Nom de la publicité</td>
			<td style="padding-left: 20px;" id="row_nom">
				<?php echo htmlspecialchars($publicite['titre']) ?>
				<?php if (verifier('publicite_editer') || ($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_siens'))){ ?>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="modifier-apparence-<?php echo $publicite['id'] ?>.html" title="Modifier le nom de la publicité">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td class="droite gris">État de la publicité</td>
			<td style="padding-left: 20px;" id="row_etat">
				<span id="lbl_etat"><?php echo $publicite->getEtatFormat() ?></span>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php if ($publicite['approuve'] == 'approuve' && (verifier('publicite_activer') || ($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_activer_siens')))){ ?>
				<a href="#" onclick="editer_etat(false); return false;" title="Activer / désactiver l'affichage de la publicité"><img src="/img/editer.png" alt="Modifier" /></a>

				<div id="edt_etat" style="position: absolute; z-index: 1.5; display: none; width: 300px;" class="UI_box">
					<select id="etat">
						<option value="oui"<?php if ($publicite['actif']) echo ' selected="selected"' ?>>
							Active
						</option>
						<option value="non"<?php if (!$publicite['actif']) echo ' selected="selected"' ?>>
							En pause
						</option>
					</select>
					<span id="btn_etat">
						<input type="submit" name="send" onclick="editer_etat(true); return false;" value="Sauvegarder" />
						<input type="submit" name="cancel" value="Annuler" onclick="$('edt_etat').setStyle('display', 'none');" />
					</span>
				</div>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td class="droite gris">
				Ciblage des visiteurs
				<?php if ($publicite['emplacement'] != 'autre' && (verifier('publicite_editer_ciblage') || ($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_ciblage_siens')))){ ?><br />
				<a href="modifier-ciblage-<?php echo $publicite['id'] ?>.html">
					<img src="/img/editer.png" alt="Modifier le ciblage" />
				</a>
				<?php } ?>
			</td>
			<td rowspan="5">
				<ul>
					<?php if ($publicite['emplacement'] != 'autre'){ ?>
					<?php $c = 0; if (!empty($publicite['age_min'])){ $c++; ?>
					<li>âgé de <?php echo $publicite['age_min'] ?> ans au minimum</li>
					<?php } if (!empty($publicite['age_max'])){ $c++; ?>
					<li>âgé de <?php echo $publicite['age_max'] ?> ans au maximum</li>
					<?php } if (count($publicite->Categories) > 0){ $c++; ?>
					<li>visitant <?php foreach ($publicite->Categories as $i => $cat) echo $cat->Categorie['nom'].($i < count($publicite->Categories)-1 ? ', ' : '') ?></li>
					<?php } if (count($publicite->Pays) > 0){ $c++; ?>
					<li>provenant de <?php foreach ($publicite->Pays as $i => $pays) echo $pays['nom'].($i < count($publicite->Pays)-1 ? ', ' : '.') ?></li>
					<?php } ?>
					<?php if ($c == 0) echo '<li>aucun critère de ciblage défini</li>' ?>
					<?php } else { ?>
					<li>impossible de définir un ciblage pour cet emplacement</li>
					<?php } ?>
				</ul>
			</td>
		</tr>
	</table>
</div>

<div style="float: right; width: 25%;">
	<div class="box">
		<h3>Prévisualisation</h3>
		<span class="tpetit gris">Le rendu final peut quelque peu différer.</span><br />
		<span class="gris">
			<?php if (verifier('publicite_editer') || ($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_siens'))){ ?>
			<a href="modifier-apparence-<?php echo $publicite['id'] ?>.html">Modifier l'apparence</a>
			<?php } else{ ?><br />
			Vous pouvez contactez un administrateur pour modifier l'apparence de votre publicité.
			<?php } ?>
		</span>
	</div>

	<?php if ($publicite['emplacement'] == 'menu'){ ?>
	<div class="sidebar" style="margin: auto;">
		<div class="bloc partenaires">
			<h4>Partenaires</h4>
			<ul class="nav nav-list">
				<?php if (!$publicite['contenu_js']){ ?>
				<li><a href="<?php echo htmlspecialchars($publicite['url_cible']) ?>" title="<?php echo htmlspecialchars($publicite['titre']) ?>" rel="<?php echo htmlspecialchars($publicite['contenu']) ?>">
					<?php echo htmlspecialchars($publicite['titre']) ?>
				</a></li>
				<?php } else{ ?>
				<?php echo $publicite['contenu'] ?>
				<?php } ?>
				<li><a href="<?php echo $view['router']->generate('zco_about_contact', array('objet' => 'Partenariat')) ?>">Votre site ici ?</a></li>
			</ul>
		</div>
	</div>
	<?php } elseif ($publicite['emplacement'] == 'pied'){ ?>
	<div class="footer center centre"><p class="links blanc" style="margin-top: 20px; margin-left: auto; margin-right: auto;">Partenaires :
		<?php if (!$publicite['contenu_js']){ ?>
		<a href="<?php echo htmlspecialchars($publicite['url_cible']) ?>" title="<?php echo htmlspecialchars($publicite['contenu']) ?>">
			<?php echo htmlspecialchars($publicite['titre']) ?>
		</a>
		<?php } else{ ?>
		<?php echo $publicite['contenu'] ?>
		<?php } ?>
	</p></div>
	<?php } else{ ?>
	<p class="italique">Aucune prévisualisation n'est disponible.</p>
	<?php } ?>
</div>

<table class="table table-striped" style="width: 73%; margin-left: 0;">
	<thead>
		<tr>
			<td colspan="4" style="padding: 2px;">
				<form method="get" action="" id="form_week">
					Statistiques pour la semaine du
					<select name="week" onchange="$('form_week').submit();">
						<?php foreach ($weeks as $w){ ?>
						<?php if (strtotime('+1 week', $w) >= strtotime($publicite->Campagne['date_debut'])){ ?>
						<option value="<?php echo date('d-m-Y', $w) ?>"<?php if (!empty($_GET['week']) && $_GET['week'] == date('d-m-Y', $w)) echo ' selected="selected"' ?>>
							<?php echo date('d ', $w).$convertisseurMois[date('n', $w)-1] ?>
						</option>
						<?php } } ?>
					</select>
					<noscript><input type="submit" value="Aller" /></noscript>
				</form>
			</td>
		</tr>

		<tr>
			<th style="width: 40%;">Date</th>
			<th style="width: 15%;">Impressions</th>
			<th style="width: 15%;">Clics</th>
			<th style="width: 15%;">Taux de clics</th>
		</tr>
	</thead>

	<tfoot>
		<tr class="bold">
			<td>Durée de vie totale de la publicité</td>
			<td class="center"><?php echo $view['humanize']->numberformat($publicite['nb_affichages'], 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($publicite['nb_clics'], 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($publicite->getTauxClics()) ?> %</td>
		</tr>
	</tfoot>

	<tbody>
		<?php foreach ($stats as $date => $stat){ ?>
		<tr>
			<td><?php echo dateformat($date, DATE) ?></td>
			<td class="center">
				<?php if (strtotime($date) > time()) echo '-'; else{ ?>
				<?php echo !is_null($stat) ? $view['humanize']->numberformat($stat['nb_affichages'], 0) : '0' ?>

				<?php if ($stat['nb_affichages'] > 0 && verifier('publicite_raz_affichages')){ ?>
				<a href="raz-affichages-<?php echo $publicite['id'] ?>.html?date=<?php echo $date ?>&token=<?php echo $_SESSION['token'] ?>" title="Remettre les impressions à zéro pour cette journée" onclick="if (confirm('Voulez-vous vraiment réinitialiser le nombre d\'impressions pour cette journée ?')) document.location = this.href; else return false;">
					<img src="/img/supprimer.png" alt="Remettre les impressions à zéro" />
				</a>
				<?php } } ?>
			</td>
			<td class="center">
				<?php if (strtotime($date) > time()) echo '-'; else{ ?>
				<?php echo !is_null($stat) ? $view['humanize']->numberformat($stat['nb_clics'], 0) : '0' ?>

				<?php if ($stat['nb_clics'] > 0 && verifier('publicite_raz_clics')){ ?>
				<a href="raz-clics-<?php echo $publicite['id'] ?>.html?date=<?php echo $date ?>&token=<?php echo $_SESSION['token'] ?>" title="Remettre les clics à zéro pour cette journée" onclick="if (confirm('Voulez-vous vraiment réinitialiser le nombre de clics pour cette journée ?')) document.location = this.href; else return false;">
					<img src="/img/supprimer.png" alt="Remettre les clics à zéro" />
				</a>
				<?php } } ?>
			</td>
			<td class="center">
				<?php if (strtotime($date) > time()) echo '-'; else{ ?>
				<?php echo !is_null($stat) ? $view['humanize']->numberformat($stat->getTauxClics()) : $view['humanize']->numberformat(0) ?> %
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<div style="clear: right;"></div>

<div class="box" style="margin-top: 20px;">
	<form method="get" action="" id="form_type" class="form-horizontal">
		<div class="control-goup">
			<label for="type" class="control-label">Choisissez un graphique</label>
			<div class="controls">
				<select name="type" id="type" onchange="$('img_stats').src = 'graphique-publicite.html?id=<?php echo $publicite['id'] ?>&week=<?php echo $week ?>&type='+this.value; if (this.value == 'pays' || this.value == 'categorie' || this.value == 'age') $('rmq_periode').slide('in'); else $('rmq_periode').slide('out');">
					<optgroup label="Données volumétriques sur la période">
						<option value="clic">Nombre de clics</option>
						<option value="affichage">Nombre d'impressions</option>
						<option value="taux">Taux de clics</option>
					</optgroup>
					<optgroup label="Profil des cliqueurs">
						<option value="pays">Provenance géographique</option>
						<option value="categorie">Section du site</option>
						<option value="age">Répartition des âges</option>
					</optgroup>
				</select>
				<noscript><input type="submit" value="Aller" /></noscript>
			</div>
		</div>
	</form>

	<div class="center">
		<div id="rmq_periode" class="rmq attention">
			Les données concernant le profil des cliqueurs sont établies sur toute
			la durée de vie de la publicité. Notez que du fait de la technologie
			utilisée pour compter les clics (et permettant un référencement plus efficace
			de votre lien) seules les personnes ayant activé le Javascript dans leur
			navigateur sont comptabilisées (cela exclut donc les robots d'indexation).
		</div>
		
		<img id="img_stats" src="graphique-publicite.html?id=<?php echo $publicite['id'] ?>&type=clic&week=<?php echo $week ?>" alt="Graphique de statistiques" />
	</div>
</div>

<script type="text/javascript">
function editer_etat(act)
{
	if (act == false)
	{
		pos = $('row_etat').getPosition();
		$('edt_etat').setPosition({'x': pos.x, 'y': pos.y});
		$('edt_etat').setStyle('display', 'block');
	}
	else
	{
		$('btn_etat').setStyle('display', 'none');
		xhr = new Request({url: '/publicite/ajax-modifier-etat-publicite.html', method: 'post', onSuccess: function(text, xml){
			$('lbl_etat').set('html', text);
			$('edt_etat').setStyle('display', 'none');
			$('btn_etat').setStyle('display', 'inline');
		}});
		xhr.send('etat='+$('etat').value+'&id=<?php echo $publicite['id'] ?>');
	}
}

document.addEvent('domready', function(){ $('rmq_periode').slide('hide'); });
</script>
