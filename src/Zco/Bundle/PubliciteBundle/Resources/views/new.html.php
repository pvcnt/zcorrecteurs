<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPubliciteBundle::_onglets.html.php', array('currentTab' => 'new')) ?>

<div class="row-fluid">
    <div class="span8">
        <form method="post" action="" class="form-horizontal">
            <div class="accordion" id="pub-accordion">
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#pub-accordion" href="#pub-conception">
                            Conception de votre publicité
                        </a>
                    </div> <!-- /.accordion-heading -->
                    <div id="pub-conception" class="accordion-body collapse in">
                        <div class="accordion-inner">
                            <div class="control-group">
                                <label for="emplacement" class="control-label">Emplacement souhaité</label>
                                <div class="controls">
                                    <select name="emplacement" id="emplacement">
                                        <option value="" class="opt_titre">Sélectionnez un emplacement</option>
                                        <option value="menu">Menu de gauche</option>
                                        <option value="pied">Pied de page</option>
                                        <option value="autre">Autre (intégré spéficiquement)</option>
                                    </select>
                                </div>
                            </div>
				
                            <div class="control-group">
                                <label for="url_cible" class="control-label">Adresse de redirection</label>
                                <div class="controls">
                                    <input type="text" name="url_cible" id="url_cible" placeholder="http://" /><br />
                                </div>
                            </div>
				
                            <div class="control-group">
                                <label for="titre" class="control-label">Nom de la publicité</label>
                                <div class="controls">
                                    <input type="text" name="titre" id="titre" />
                                </div>
                            </div>

                            <div class="control-group" id="row_contenu">
                                <label for="contenu" class="control-label">Contenu</label>
                                <div class="controls">
                                    <textarea name="contenu" id="contenu" style="width: 400px; height: 70px;"></textarea>
                                    <?php if (verifier('publicite_js')){ ?>
                                        <p class="help-block">
                                            <label for="contenu_js" class="nofloat">
                                                <input type="checkbox" name="contenu_js" id="contenu_js" />
                                                Décrire le contenu en HTML + Javascript
                                            </label>
                                        </p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
      
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#pub-accordion" href="#pub-ciblage">
                            Ciblage des visiteurs
                        </a>
                    </div> <!-- /.accordion-heading -->
                    <div id="pub-ciblage" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <strong>Ciblage par section.</strong> <span class="gris">N'affiche la publicité que sur certaines sections du site.</span><br />
                            <input type="checkbox" name="aff_accueil" id="aff_accueil" />
                            <label for="aff_accueil" class="nofloat">N'afficher que sur l'accueil du site</label><br /><br />

                            <strong>Ciblage par pays.</strong> 
                            <span class="gris">Ce ciblage se base sur l'adresse IP des visiteurs et peut être légèrement imprécis.</span><br />
                            <input type="checkbox" name="cibler_pays" id="cibler_pays"  onclick="$('row_cibler_pays').toggle();" checked="checked" />
                            <label for="cibler_pays" class="nofloat">Afficher la publicité à tous les visiteurs</label><br />

                            <div id="row_cibler_pays">
                                <label for="pays">Pays ciblés :</label>
                                <select name="pays[]" id="pays" multiple="multiple" size="5" style="min-width: 200px;">
                                    <?php foreach ($pays as $p){ ?>
                                    <option value="<?php echo $p['id'] ?>" selected="selected">
                                        <?php echo htmlspecialchars($p['nom']) ?>
                                    </option>
                                    <?php } ?>
                                </select><br /><em>Appuyez sur Ctrl ou Maj pour sélectionner plusieurs pays.</em><br />

                                <input type="checkbox" name="pays_inconnu" id="pays_inconnu" class="nofloat" checked="checked" />
                                <label for="pays_inconnu" class="nofloat">Afficher la publicité quand il est impossible de déterminer le pays de provenance.</label>
                            </div><br />

                            <strong>Ciblage par âge.</strong> <span class="gris">Ne fonctionne que pour les membres inscrits ayant renseigné leur âge (<?php echo $nb_membres_age ?> membre<?php echo pluriel($nb_membres_age) ?> actuellement).</span><br />
                            <input type="checkbox" name="cibler_age" id="cibler_age"  onclick="$('row_cibler_age').toggle();" checked="checked" />
                            <label for="cibler_age" class="nofloat">Afficher la publicité à tous les visiteurs</label><br />

                            <div id="row_cibler_age">
                                <label for="age_min">Âge minimum :</label>
                                <input type="text" name="age_min" id="age_min" size="4" value="-" />
                                <input type="checkbox" name="aucun_age_min" id="aucun_age_min" checked="checked" />
                                <label for="aucun_age_min" class="nofloat">Aucun</label><br />

                                <label for="age_max">Âge maximum :</label>
                                <input type="text" name="age_max" id="age_max" size="4" value="-" />
                                <input type="checkbox" name="aucun_age_max" id="aucun_age_max" checked="checked" />
                                <label for="aucun_age_max" class="nofloat">Aucun</label><br />

                                <input type="checkbox" name="age_inconnu" id="age_inconnu" class="nofloat" checked="checked" />
                                <label for="age_inconnu" class="nofloat">Afficher la publicité aux personnes n'ayant pas renseigné leur âge.</label>
                            </div>
                        </div> <!-- /.accordion-inner -->
                    </div> <!-- /.accordion-body -->
                </div> <!-- /.accordion-group -->
    
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#pub-accordion" href="#pub-campagne">
                            Propriétés de la campagne
                        </a>
                    </div> <!-- /.accordion-heading -->
                    <div id="pub-campagne" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <?php if ($campagne != false){ ?>
                                <label for="nom">Nom de la campagne :</label>
                                <?php echo htmlspecialchars($campagne['nom']) ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                <a href="ajouter.html" class="tpetit">Nouvelle campagne</a><br /><br />
                            <?php } else { ?>
                                <label for="nom">Nom de la campagne :</label>
                                <input type="text" name="nom" id="nom" size="40" /><br /><br />

                                <strong>Programmation de l'affichage.</strong> 
                                <span class="gris">Durant quelle période de temps souhaitez-vous diffuser votre annonce ?</span><br />
                                
                                <input type="radio" name="prog" value="continu" id="prog_continu" checked="checked" onclick="$('row_prog_periode').setStyle('display', 'none');" />
                                <label for="prog_continu" class="nofloat">Afficher en continu à partir de la date de validation</label><br />
                                <input type="radio" name="prog" value="periode" id="prog_periode" onclick="$('row_prog_periode').setStyle('display', 'inline');" />
                                <label for="prog_periode" class="nofloat">Afficher durant la période spécifiée</label>

                                <span id="row_prog_periode" style="display: none;"> :
                                    du <?php echo $view->get('widget')->datePicker('date_debut', $campagne ? $campagne['date_debut'] : '') ?>
                                    au <?php echo $view->get('widget')->datePicker('date_fin', $campagne ? $campagne['date_fin'] : '', array('allowEmpty' => true), array('onclick' => "$('pas_date_fin').checked = false;")) ?>
                                    <input type="checkbox" name="pas_date_fin" id="pas_date_fin" onclick="if (this.checked) $('date_fin').value = '';" <?php if ($campagne && empty($campagne['date_fin'])) echo ' checked="checked"'; ?> />
                                    <label for="pas_date_fin" class="nofloat">pas de date de fin</label>
                                </span><br />
                            <?php } ?>

                            <?php if (verifier('publicite_changer_etat_siens') || verifier('publicite_changer_etat')){ ?>
                                <input type="checkbox" name="actif" id="actif" />
                                <label for="actif" class="nofloat">Valider et activer la publicité dès maintenant</label>
                            <?php } ?>
                        </div> <!-- /.accordion-inner -->
                    </div> <!-- /.accordion-body -->
                </div> <!-- /.accordion-group -->
            </div> <!-- /#pub-accordion -->

            <div class="form-actions">
                <input type="submit" name="send" class="btn btn-primary" value="Lancer ma campagne !" accesskey="s" />
            </div>
        </form>
    </div> <!-- /.span8 -->
    <div class="span4">
        <strong>Prévisualisation de la publicité.</strong> 
        <span class="gris">Le rendu final peut quelque peu différer.</span>
        <div id="preview_pub"><em>Complétez les champs pour avoir un aperçu.</em></div>
    </div> <!-- /.span4 -->
</div> <!-- /.row-fluid -->


<?php echo $view->render('ZcoPubliciteBundle::_apparence_js.html.php') ?>
<?php echo $view->render('ZcoPubliciteBundle::_ciblage_js.html.php') ?>

<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css') ?>