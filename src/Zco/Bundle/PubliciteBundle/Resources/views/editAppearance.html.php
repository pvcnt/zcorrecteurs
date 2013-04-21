<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPubliciteBundle::_onglets.html.php') ?>
<h1>Modifier l'apparence : <?php echo htmlspecialchars($publicite['titre']) ?></h1>

<div class="row-fluid">
    <div class="span8">
        <form method="post" action="<?php echo $view['router']->generate('zco_ads_appearance', array('id' => $publicite['id'])) ?>" class="form-horizontal">
            <div class="control-group">
                <label for="url_cible" class="control-label">Adresse de redirection</label>
                <div class="controls">
                    <input type="text" name="url_cible" id="url_cible" size="40" value="<?php echo htmlspecialchars($publicite['url_cible']) ?>" />
                </div>
            </div>

            <div class="control-group">
                <label for="titre" class="control-label">Nom de la publicité</label>
                <div class="controls">
                    <input type="text" name="titre" id="titre" size="40" value="<?php echo htmlspecialchars($publicite['titre']) ?>" />
                </div>
            </div>

            <div id="row_contenu" class="control-group">
                <label for="contenu" class="control-label">Contenu</label>
                <div class="controls">
                    <textarea name="contenu" id="contenu" style="width: 400px; height: 70px;"><?php echo htmlspecialchars($publicite['contenu']) ?></textarea>
                    
                    <?php if (verifier('publicite_js')) { ?>
                    <p class="help-block">
                        <label for="contenu_js" class="nofloat">
                            <input type="checkbox" name="contenu_js" id="contenu_js" <?php if ($publicite['contenu_js']) echo ' checked="checked"' ?> />
                            Décrire le contenu en HTML + Javascript
                        </label>
                    </p>
                    <?php } ?>
                </div>
            </div>
            
            <div class="form-actions">
                <input type="submit" class="btn btn-primary" value="Sauvegarder" accesskey="s" />
                <a href="<?php echo $view['router']->generate('zco_ads_advertisment', array('id' => $publicite['id'])) ?>" class="btn">Annuler</a>
            </div>
        </form>
    </div> <!-- /.span8 -->
    <div class="span4">
        <div style="margin-bottom: 10px;">
            <strong>Prévisualisation de la publicité.</strong> 
            <span class="gris">Le rendu final peut quelque peu différer.</span>
        </div>
        <div id="preview_pub">
            <?php echo $view->render('ZcoPubliciteBundle::_preview.html.php', array('advertisment' => $publicite)) ?>
        </div>
    </div> <!-- /.span4 -->
</div> <!-- /.row-fluid -->


<script type="text/javascript">
    $('titre').addEvent('keyup', update_pub);
    $('contenu').addEvent('keyup', update_pub);
    $('url_cible').addEvent('change', update_pub);
    <?php if (verifier('publicite_js')) { ?>
        $('contenu_js').addEvent('click', function(){
            if ($('contenu_js').checked) {
                $('contenu').value = get_pub_html();
                update_pub();
            } else {
                update_pub();
            }
        });
    <?php } ?>

    function update_pub()
    {
        <?php if (verifier('publicite_js')) { ?>
            if ($('contenu_js').checked) {
                pub = $('contenu').value;
            }
            else <?php } ?>
        pub = get_pub_html();

        <?php if ($publicite['emplacement'] === 'menu') { ?>
            pub = '<div class="sidebar"><div class="bloc partenaires"><h4>Partenaires</h4><ul>'+
            pub+'</ul></div></div>';
        <?php } elseif ($publicite['emplacement'] === 'pied') { ?>
            pub = '<div class="footer center centre"><p class="links blanc" style="margin-top: 20px;">'+
                'Partenaires : '+pub+
                '</p></div>';
        <?php } ?>
        $('preview_pub').set('html', pub);
    }

    function get_pub_html()
    {
        <?php if ($publicite['emplacement'] === 'menu') { ?>
            return '<li><a href="'+$('url_cible').value+'" title="'+$('titre').value+'" rel="'+$('contenu').value+'">'+$('titre').value+'</a></li>';
        <?php } elseif ($publicite['emplacement'] === 'pied') { ?>
            return '<a href="'+$('url_cible').value+'" title="'+$('contenu').value+'">'+$('titre').value+'</a>';
        <?php } ?>
    }
</script>

<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css') ?>