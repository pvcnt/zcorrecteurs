<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoAdBundle::_onglets.html.php') ?>
<h1>Ciblage : <?php echo htmlspecialchars($publicite['titre']) ?></h1>

<form method="post" action="">
    <strong>Ciblage par section.</strong> <span class="gris">N'affiche la publicité que sur certaines sections du site.</span><br />
    <input type="checkbox" name="aff_accueil" id="aff_accueil" <?php if ($publicite['aff_accueil']) echo ' checked="checked"' ?> />
    <label for="aff_accueil" class="nofloat">N'afficher que sur l'accueil du site</label><br /><br />

    <strong>Ciblage par pays.</strong> <span class="gris">Ce ciblage se base sur l'adresse IP des visiteurs et peut être légèrement imprécis.</span><br />
    <input type="checkbox" name="cibler_pays" id="cibler_pays"  onclick="$('row_cibler_pays').toggle();"<?php if (!$cibler_pays) echo ' checked="checked"' ?> />
    <label for="cibler_pays" class="nofloat">Afficher la publicité à tous les visiteurs</label><br />

    <div id="row_cibler_pays">
        <label for="pays">Pays ciblés :</label>
        <select name="pays[]" id="pays" multiple="multiple" size="5" style="min-width: 200px;">
            <?php foreach ($pays as $p) { ?>
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
        <input type="text" name="age_min" id="age_min" size="4" value="<?php echo!empty($publicite['age_min']) ? $publicite['age_min'] : '-' ?>" />
        <input type="checkbox" name="aucun_age_min" id="aucun_age_min"<?php if (!$cibler_age_min) echo ' checked="checked"' ?> />
        <label for="aucun_age_min" class="nofloat">Aucun</label><br />

        <label for="age_max">Âge maximum :</label>
        <input type="text" name="age_max" id="age_max" size="4" value="<?php echo!empty($publicite['age_max']) ? $publicite['age_max'] : '-' ?>" />
        <input type="checkbox" name="aucun_age_max" id="aucun_age_max"<?php if (!$cibler_age_max) echo ' checked="checked"' ?> />
        <label for="aucun_age_max" class="nofloat">Aucun</label><br />

        <input type="checkbox" name="age_inconnu" id="age_inconnu" class="nofloat"<?php if ($publicite['aff_age_inconnu']) echo ' checked="checked"' ?> />
        <label for="age_inconnu" class="nofloat">Afficher la publicité aux personnes n'ayant pas renseigné leur âge.</label>
    </div>

    <div class="form-actions">
        <input type="submit" name="send" class="btn btn-primary" value="Sauvegarder les nouveaux critères" accesskey="s" />
        <a href="<?php echo $view['router']->generate('zco_ad_advertisment', array('id' => $publicite['id'])) ?>" class="btn">Annuler</a>
    </div>
</form>

<?php
echo $view->render('ZcoAdBundle::_ciblage_js.html.php', array(
    'cibler_pays'    => $cibler_pays,
    'cibler_age'     => $cibler_age,
    'cibler_age_min' => $cibler_age_min,
    'cibler_age_max' => $cibler_age_max,
))
?>

<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css') ?>