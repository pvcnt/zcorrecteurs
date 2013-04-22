<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoAdBundle::_onglets.html.php', array('campagne_id'      => $publicite['campagne_id'])) ?>
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
                <?php if (verifier('publicite_editer') || ($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_siens'))) { ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo $view['router']->generate('zco_ad_appearance', array('id' => $publicite['id'])) ?>" title="Modifier le nom de la publicité">
                        <img src="/img/editer.png" alt="Modifier" />
                    </a>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td class="droite gris">État de la publicité</td>
            <td style="padding-left: 20px;" id="row_etat">
                <span id="lbl_etat"><?php echo $publicite->getEtatFormat() ?></span>
                <?php if ($publicite['approuve'] == 'approuve' && (verifier('publicite_activer') || ($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_activer_siens')))) { ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="#" onclick="editer_etat(false); return false;" title="Activer / désactiver l'affichage de la publicité">
                        <img src="/img/editer.png" alt="Modifier" />
                    </a>
                    <div id="edt_etat" style="position: absolute; z-index: 1.5; display: none; width: 300px;" class="box">
                        <select id="etat">
                            <option value="oui"<?php if ($publicite['actif']) echo ' selected="selected"' ?>>
                                Active
                            </option>
                            <option value="non"<?php if (!$publicite['actif']) echo ' selected="selected"' ?>>
                                En pause
                            </option>
                        </select>
                        <span id="btn_etat">
                            <input type="submit" class="btn btn-primary" name="send" onclick="editer_etat(true); return false;" value="Sauvegarder" />
                            <input type="submit" class="btn" name="cancel" value="Annuler" onclick="$('edt_etat').setStyle('display', 'none');" />
                        </span>
                    </div>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td class="droite gris">
                Ciblage des visiteurs
                <?php if ('autre' !== $publicite['emplacement'] && (verifier('publicite_editer_ciblage') || ($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_ciblage_siens')))) { ?><br />
                    <a href="<?php echo $view['router']->generate('zco_ad_targeting', array('id' => $publicite['id'])) ?>">
                        <img src="/img/editer.png" alt="Modifier le ciblage" />
                    </a>
                <?php } ?>
            </td>
            <td rowspan="5">
                <ul>
                    <?php if ('autre' !== $publicite['emplacement']) { ?>
                        <?php $c = 0;
                        if (!empty($publicite['age_min'])) {
                            $c++; ?>
                            <li>âgé de <?php echo $publicite['age_min'] ?> ans au minimum</li>
                        <?php } if (!empty($publicite['age_max'])) {
                            $c++; ?>
                            <li>âgé de <?php echo $publicite['age_max'] ?> ans au maximum</li>
                        <?php } if ($publicite['aff_accueil']) {
                            $c++; ?>
                            <li>visitant l'accueil du site</li>
                        <?php } if (count($publicite->Pays) > 0) {
                            $c++; ?>
                            <li>provenant de <?php foreach ($publicite->Pays as $i => $pays)
                                echo $pays['nom'] . ($i < count($publicite->Pays) - 1 ? ', ' : '.') ?></li>
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
    <div style="margin-bottom: 10px;">
        <strong>Prévisualisation de la publicité.</strong> 
        <span class="gris">Le rendu final peut quelque peu différer.</span>

        <div class="gris">
            <?php if (verifier('publicite_editer') || ($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_siens'))) { ?>
                <a href="<?php echo $view['router']->generate('zco_ad_appearance', array('id' => $publicite['id'])) ?>">
                    Modifier l'apparence
                </a>
            <?php } else { ?><br />
                Vous pouvez contactez un administrateur pour modifier l'apparence 
                de votre publicité.
            <?php } ?>
        </div>
    </div>

    <?php echo $view->render('ZcoAdBundle::_preview.html.php', array('advertisment' => $publicite)) ?>
</div>

<table class="table table-striped" style="width: 73%; margin-left: 0;">
    <thead>
        <tr>
            <td colspan="4" style="padding: 2px;">
                <form method="get" action="" id="form_week">
                    Statistiques pour la semaine du
                    <select name="week" onchange="$('form_week').submit();">
                        <?php foreach ($weeks as $w) { ?>
                            <?php if (strtotime('+1 week', $w) >= strtotime($publicite->Campagne['date_debut'])) { ?>
                                <option value="<?php echo date('d-m-Y', $w) ?>"<?php if (!empty($_GET['week']) && $_GET['week'] == date('d-m-Y', $w)) echo ' selected="selected"' ?>>
                                    <?php echo date('d ', $w) . $convertisseurMois[date('n', $w) - 1] ?>
                                </option>
                            <?php } ?>
                        <?php } ?>
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
        <?php foreach ($stats as $date => $stat) { ?>
            <tr>
                <td><?php echo dateformat($date, DATE) ?></td>
                <td class="center">
                    <?php if (strtotime($date) > time()) echo '-'; else { ?>
                        <?php echo $stat ? $view['humanize']->numberformat($stat->getDisplayCount(), 0) : '0' ?>

                        <?php if ($stat['nb_affichages'] > 0 && verifier('publicite_raz_affichages')) { ?>
                            <a href="<?php echo $view['router']->generate('zco_ad_resetDisplays', array('id' => $publicite['id'], 'date' => $date, 'token' => $_SESSION['token'])) ?>" 
                               title="Remettre les impressions à zéro pour cette journée" 
                               onclick="if (confirm('Voulez-vous vraiment réinitialiser le nombre d\'impressions pour cette journée ?')) document.location = this.href; else return false;"
                            >
                                <img src="/img/supprimer.png" alt="Remettre les impressions à zéro" />
                            </a>
                        <?php } ?>
                    <?php } ?>
                </td>
                <td class="center">
                    <?php if (strtotime($date) > time()) echo '-'; else { ?>
                        <?php echo $stat ? $view['humanize']->numberformat($stat->getClickCount(), 0) : '0' ?>

                        <?php if ($stat['nb_clics'] > 0 && verifier('publicite_raz_clics')) { ?>
                            <a href="<?php echo $view['router']->generate('zco_ad_resetClicks', array('id' => $publicite['id'], 'date' => $date, 'token' => $_SESSION['token'])) ?>" 
                               title="Remettre les clics à zéro pour cette journée" 
                               onclick="if (confirm('Voulez-vous vraiment réinitialiser le nombre de clics pour cette journée ?')) document.location = this.href; else return false;"
                            >
                                <img src="/img/supprimer.png" alt="Remettre les clics à zéro" />
                            </a>
                        <?php } ?>
                    <?php } ?>
                </td>
                <td class="center">
                    <?php if (strtotime($date) > time()): ?>
                        -
                    <?php else : ?>
                        <?php echo $view['humanize']->numberformat($stat ? $stat->getTauxClics() : 0) ?> %
                    <?php endif ?>
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
                <select name="type" id="type" 
                        onchange="$('img_stats').src = Routing.generate('zco_ad_graph_advertisment', {'id': <?php echo $publicite['id'] ?>, 'week': '<?php echo $week ?>', 'type': this.value}); if (this.value == 'pays' || this.value == 'categorie' || this.value == 'age') $('rmq_periode').slide('in'); else $('rmq_periode').slide('out');">
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

        <img id="img_stats" 
             src="<?php echo $view['router']->generate('zco_ad_graph_advertisment', array('id' => $publicite['id'], 'type' => 'clic', 'week' => $week)) ?>" 
             alt="Graphique de statistiques" 
        />
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
            xhr = new Request({
                url: Routing.generate('zco_ad_api_editAdStatus', {id: <?php echo $publicite['id'] ?>}), 
                method: 'post', 
                onSuccess: function(text, xml){
                    $('lbl_etat').set('html', text);
                    $('edt_etat').setStyle('display', 'none');
                    $('btn_etat').setStyle('display', 'inline');
                }});
            xhr.send('etat='+$('etat').value);
        }
    }

    document.addEvent('domready', function(){ $('rmq_periode').slide('hide'); });
</script>

<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css') ?>