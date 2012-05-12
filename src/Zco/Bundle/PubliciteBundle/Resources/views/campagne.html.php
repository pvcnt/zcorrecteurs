<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPubliciteBundle::_onglets.html.php', array('currentTab' => 'campaigns')) ?>

<h1 id="h2_nom"><?php echo htmlspecialchars($campagne['nom']) ?></h1>

<div class="box">
	<table style="width: 100%;">
		<tr>
			<td class="droite gris" style="width: 50%;">Nom de la campagne</td>
			<td style="padding-left: 20px; width: 50%;" id="row_nom">
				<span id="lbl_nom"><?php echo htmlspecialchars($campagne['nom']) ?></span>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="editer_nom(false); return false;"><img src="/img/editer.png" alt="Modifier" /></a>

				<?php if (verifier('publicite_supprimer')){ ?>
				<a href="supprimer-campagne-<?php echo $campagne['id'] ?>.html" title="Supprimer définitivement la campagne">
					<img src="/img/supprimer.png" alt="Supprimer" />
				</a>
				<?php } if (verifier('publicite_editer_createur') || ($campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_createur_siens'))){ ?>
				<a href="modifier-createur-<?php echo $campagne['id'] ?>.html" title="Changer le propriétaire de la campagne">
					<img src="/img/membres/changer_groupe.png" alt="Changer le créateur" />
				</a>
				<?php } ?>

				<div id="edt_nom" style="position: absolute; z-index: 1.5; display: none; width: 400px;" class="box">
					<input type="text" name="nom" id="nom" size="35" value="<?php echo htmlspecialchars($campagne['nom']) ?>" />
					<span id="btn_nom">
						<input type="submit" class="btn btn-primary" name="send" onclick="editer_nom(true); return false;" value="Sauvegarder" />
						<input type="submit" class="btn" name="cancel" value="Annuler" onclick="$('edt_nom').setStyle('display', 'none');" />
					</span>
				</div>
			</td>
		</tr>
		<tr>
			<td class="droite gris">État</td>
			<td style="padding-left: 20px;" id="row_etat">
				<span id="lbl_etat"><?php echo $campagne->getEtatFormat() ?></span>
				<?php if (verifier('publicite_editer_etat') || ($campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_etat_siens'))){ ?>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="editer_etat(false); return false;" title="Modifier l'état de la campagne"><img src="/img/editer.png" alt="Modifier" /></a>

				<div id="edt_etat" style="position: absolute; z-index: 1.5; display: none; width: 400px;" class="box">
					<select id="etat">
						<option value="en_cours"<?php if ($campagne['etat'] == 'en_cours') echo ' selected="selected"' ?>>
							Active
						</option>
						<option value="pause"<?php if ($campagne['etat'] == 'pause') echo ' selected="selected"' ?>>
							En pause
						</option>
						<option value="termine"<?php if ($campagne['etat'] == 'termine') echo ' selected="selected"' ?>>
							Terminée
						</option>
						<option value="supprime"<?php if ($campagne['etat'] == 'supprime') echo ' selected="selected"' ?>>
							Supprimée
						</option>
					</select>
					<span id="btn_etat">
						<input type="submit" name="send" class="btn btn-primary" onclick="editer_etat(true); return false;" value="Sauvegarder" />
						<input type="submit" name="cancel" class="btn" value="Annuler" onclick="$('edt_etat').setStyle('display', 'none');" />
					</span>
				</div>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td class="droite gris">Durée de la campagne</td>
			<td style="padding-left: 20px;" id="row_dates">
				<span id="lbl_dates"><?php echo dateformat($campagne['date_debut'], DATE) ?> -
				<?php echo !is_null($campagne['date_fin']) ? dateformat($campagne['date_fin'], DATE) : 'jamais' ?></span>
				<?php if (verifier('publicite_editer_etat') || ($campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_etat_siens'))){ ?>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="editer_dates(false); return false;" title="Modifier la durée de la campagne"><img src="/img/editer.png" alt="Modifier" /></a>

				<div id="edt_dates" style="position: absolute; z-index: 1.5; display: none; width: 220px;" class="box">
					Du <?php echo $view->get('widget')->datePicker('date_debut', $campagne['date_debut']) ?><br />
					au <?php echo $view->get('widget')->datePicker('date_fin', $campagne['date_fin'], array('allowEmpty' => true), array('onclick' => "$('pas_date_fin').checked = false;")) ?>
					<input type="checkbox" name="pas_date_fin" id="pas_date_fin" onchange="if (this.checked) $('date_fin').value = '';" <?php if (empty($campagne['date_fin'])) echo ' checked="checked"'; ?> />
					<label for="pas_date_fin" class="nofloat">jamais</label><br />
					<span id="btn_dates">
						<input type="submit" name="send" class="btn btn-primary" onclick="editer_dates(true); return false;" value="Sauvegarder" />
						<input type="submit" name="cancel" class="btn" value="Annuler" onclick="$('edt_dates').setStyle('display', 'none');" />
					</span>
				</div>
				<?php } ?>
			</td>
		</tr>
	</table>
</div>

<?php if (count($publicites) > 0){ ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Nom de la publicité</th>
			<th style="width: 17%;">État</th>
			<th style="width: 17%;">Impressions</th>
			<th style="width: 17%;">Clics</th>
			<th style="width: 17%;">Taux de clics</th>
		</tr>
	</thead>

	<tfoot>
		<tr class="bold">
			<td colspan="2">Totaux</td>
			<td class="center"><?php echo $view['humanize']->numberformat($campagne['nb_affichages'], 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($campagne['nb_clics'], 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($campagne->getTauxClics()) ?> %</td>
		</tr>
	</tfoot>

	<tbody>
		<?php foreach ($publicites as $publicite){ ?>
		<tr>
			<td>
				<a href="publicite-<?php echo $publicite['id'] ?>.html"><?php echo htmlspecialchars($publicite['titre']) ?></a>
			</td>
			<td><?php echo $publicite->getEtatFormat() ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($publicite['nb_affichages'], 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($publicite['nb_clics'], 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($publicite->getTauxClics()) ?> %</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucune publicité n'a été créée dans cette campagne.</p>
<?php } ?>

<script type="text/javascript">
function editer_nom(act)
{
	if (act == false)
	{
		pos = $('row_nom').getPosition();
		$('edt_nom').setPosition({'x': pos.x, 'y': pos.y});
		$('edt_nom').setStyle('display', 'block');
	}
	else
	{
		$('btn_nom').setStyle('display', 'none');
		xhr = new Request({url: '/publicite/ajax-modifier-nom-campagne.html', method: 'post', onSuccess: function(text, xml){
			$('lbl_nom').set('html', text);
			$('h2_nom').set('html', text);
			$('edt_nom').set('value', text);
			$('edt_nom').setStyle('display', 'none');
			$('btn_nom').setStyle('display', 'inline');
		}});
		xhr.send('nom='+$('nom').value+'&id=<?php echo $campagne['id'] ?>');
	}
}

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
		xhr = new Request({url: '/publicite/ajax-modifier-etat-campagne.html', method: 'post', onSuccess: function(text, xml){
			$('lbl_etat').set('html', text);
			$('edt_etat').setStyle('display', 'none');
			$('btn_etat').setStyle('display', 'inline');
		}});
		xhr.send('etat='+$('etat').value+'&id=<?php echo $campagne['id'] ?>');
	}
}

function editer_dates(act)
{
	if (act == false)
	{
		pos = $('row_dates').getPosition();
		$('edt_dates').setPosition({'x': pos.x, 'y': pos.y});
		$('edt_dates').setStyle('display', 'block');
	}
	else
	{
		$('btn_dates').setStyle('display', 'none');
		xhr = new Request({url: '/publicite/ajax-modifier-dates-campagne.html', method: 'post', onSuccess: function(text, xml){
			$('lbl_dates').set('html', text);
			$('edt_dates').setStyle('display', 'none');
			$('btn_dates').setStyle('display', 'inline');
		}});
		xhr.send('date_debut='+$('date_debut').value+'&date_fin='+($('pas_date_fin').checked ? '' : $('date_fin').value)+'&id=<?php echo $campagne['id'] ?>');
	}
}
</script>

<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css') ?>