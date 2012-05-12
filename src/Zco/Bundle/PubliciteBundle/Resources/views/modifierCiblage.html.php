<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoPubliciteBundle::_onglets.html.php') ?>
<h1>Ciblage : <?php echo htmlspecialchars($publicite['titre']) ?></h1>

<form method="post" action="">
	<fieldset>
		<legend>Ciblage des visiteurs</legend>

		<strong>Ciblage par section.</strong> <span class="gris">N'affiche la publicité que sur certaines sections du site.</span><br />
		<input type="checkbox" name="cibler_categories" id="cibler_categories" onclick="$('row_cibler_categories').toggle();"<?php if (!$cibler_categories) echo ' checked="checked"' ?> />
		<label for="cibler_categories" class="nofloat">Afficher la publicité à tous les visiteurs</label><br />

		<div id="row_cibler_categories">
			<p style="margin-top: 5px; margin-bottom: 5px;">
				<span class="gras">Choisissez les sections où afficher votre publicité.</span><br />
				<span class="italique">
					Sélectionner :
					<a href="#" onclick="$$('.chk_cat').set('checked', true); $$('.lbl_cat').addClass('vertf'); return false;">Toutes</a> -
					<a href="#" onclick="$$('.chk_cat').set('checked', false); $$('.lbl_cat').removeClass('vertf'); return false;">Aucune</a> -
					<a href="#" onclick="$$('.chk_cat').each(function(elem, i){ elem.set('checked', !elem.get('checked')); }); $$('.lbl_cat').toggleClass('vertf'); return false;">Inverser</a>.
				</span>
			</p>

			<?php foreach ($categories as $i => $cat){ ?>
			<input class="chk_cat" type="checkbox" name="categories[]" value="<?php echo $cat['id'] ?>" id="cat_<?php echo $cat['id'] ?>" onchange="if (!this.checked) $('index_<?php echo $cat['id'] ?>').set('checked', false); $('label_<?php echo $cat['id'] ?>').toggleClass('vertf');"<?php if (isset($attr_cats[$cat['id']])) echo 'checked="checked"' ?> />
			<label for="cat_<?php echo $cat['id'] ?>" class="lbl_cat<?php if (isset($attr_cats[$cat['id']])) echo ' vertf' ?>" id="label_<?php echo $cat['id'] ?>">
				<?php echo htmlspecialchars($cat['nom']) ?>
			</label>

			<?php if ($cat['ciblage_actions']){ ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<label for="index_<?php echo $cat['id'] ?>" class="nofloat">N'afficher que sur l'accueil de la section :</label>
			<input type="checkbox" name="index_<?php echo $cat['id'] ?>" id="index_<?php echo $cat['id'] ?>" onclick="if (this.checked && !$('cat_<?php echo $cat['id'] ?>').checked){ $('cat_<?php echo $cat['id'] ?>').set('checked', true); $('label_<?php echo $cat['id'] ?>').addClass('vertf'); }"<?php if (isset($attr_cats[$cat['id']]) && in_array('index', $attr_cats[$cat['id']])) echo 'checked="checked"' ?> />
			<?php } ?><br />
			<?php } ?>
		</div><br />

		<strong>Ciblage par pays.</strong> <span class="gris">Ce ciblage se base sur l'adresse IP des visiteurs et peut être légèrement imprécis.</span><br />
		<input type="checkbox" name="cibler_pays" id="cibler_pays"  onclick="$('row_cibler_pays').toggle();"<?php if (!$cibler_pays) echo ' checked="checked"' ?> />
		<label for="cibler_pays" class="nofloat">Afficher la publicité à tous les visiteurs</label><br />

		<div id="row_cibler_pays">
			<label for="pays">Pays ciblés :</label>
			<select name="pays[]" id="pays" multiple="multiple" size="5" style="min-width: 200px;">
				<?php foreach ($pays as $p){ ?>
				<option value="<?php echo $p['id'] ?>"<?php if (empty($attr_pays) || in_array($p['id'], $attr_pays)) echo 'selected="selected"' ?>>
					<?php echo htmlspecialchars($p['nom']) ?>
				</option>
				<?php } ?>
			</select><br /><em>Appuyez sur Ctrl ou Maj pour sélectionner plusieurs pays.</em><br />

			<input type="checkbox" name="pays_inconnu" id="pays_inconnu" class="nofloat"<?php if ($publicite['aff_pays_inconnu']) echo ' checked="checked"' ?> />
			<label for="pays_inconnu" class="nofloat">Afficher la publicité quand il est impossible de déterminer le pays de provenance.</label>
		</div><br />

		<strong>Ciblage par âge.</strong> <span class="gris">Ne fonctionne que pour les membres inscrits ayant renseigné leur âge (<?php echo $nb_membres_age ?> membre<?php echo pluriel($nb_membres_age) ?> actuellement).</span><br />
		<input type="checkbox" name="cibler_age" id="cibler_age"  onclick="$('row_cibler_age').toggle();"<?php if (!$cibler_age) echo ' checked="checked"' ?> />
		<label for="cibler_age" class="nofloat">Afficher la publicité à tous les visiteurs</label><br />

		<div id="row_cibler_age">
			<label for="age_min">Âge minimum :</label>
			<input type="text" name="age_min" id="age_min" size="4" value="<?php echo !empty($publicite['age_min']) ? $publicite['age_min'] : '-' ?>" />
			<input type="checkbox" name="aucun_age_min" id="aucun_age_min"<?php if (!$cibler_age_min) echo ' checked="checked"' ?> />
			<label for="aucun_age_min" class="nofloat">Aucun</label><br />

			<label for="age_max">Âge maximum :</label>
			<input type="text" name="age_max" id="age_max" size="4" value="<?php echo !empty($publicite['age_max']) ? $publicite['age_max'] : '-' ?>" />
			<input type="checkbox" name="aucun_age_max" id="aucun_age_max"<?php if (!$cibler_age_max) echo ' checked="checked"' ?> />
			<label for="aucun_age_max" class="nofloat">Aucun</label><br />

			<input type="checkbox" name="age_inconnu" id="age_inconnu" class="nofloat"<?php if ($publicite['aff_age_inconnu']) echo ' checked="checked"' ?> />
			<label for="age_inconnu" class="nofloat">Afficher la publicité aux personnes n'ayant pas renseigné leur âge.</label>
		</div>
	</fieldset>

	<div class="send">
		<input type="submit" name="send" value="Sauvegarder" accesskey="s" />
	</div>
</form>

<?php echo $view->render('ZcoPubliciteBundle::_ciblage_js.html.php', array(
	'cibler_categories' => $cibler_categories,
	'cibler_pays'       => $cibler_pays,
	'cibler_age'        => $cibler_age,
	'cibler_age_min'    => $cibler_age_min,
	'cibler_age_max'    => $cibler_age_max,
)) ?>