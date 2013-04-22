<?php if ('menu' === $advertisment['emplacement']) { ?>
    <div class="sidebar" style="margin: auto;">
        <div class="bloc partenaires">
            <h4>Partenaires</h4>
            <ul class="nav nav-list">
                <?php if (!$advertisment['contenu_js']) { ?>
                    <li><a href="<?php echo htmlspecialchars($advertisment['url_cible']) ?>" title="<?php echo htmlspecialchars($advertisment['titre']) ?>" rel="<?php echo htmlspecialchars($advertisment['contenu']) ?>">
                            <?php echo htmlspecialchars($advertisment['titre']) ?>
                        </a></li>
                <?php } else { ?>
                    <?php echo $advertisment['contenu'] ?>
                <?php } ?>
                <li><a href="<?php echo $view['router']->generate('zco_about_contact', array('objet' => 'Partenariat')) ?>">Votre site ici ?</a></li>
            </ul>
        </div>
    </div>
<?php } elseif ('pied' === $advertisment['emplacement']) { ?>
    <div class="footer center centre"><p class="links blanc" style="margin-top: 20px; margin-left: auto; margin-right: auto;">Partenaires :
            <?php if (!$advertisment['contenu_js']) { ?>
                <a href="<?php echo htmlspecialchars($advertisment['url_cible']) ?>" title="<?php echo htmlspecialchars($advertisment['contenu']) ?>">
                    <?php echo htmlspecialchars($advertisment['titre']) ?>
                </a>
            <?php } else { ?>
                <?php echo $advertisment['contenu'] ?>
            <?php } ?>
        </p></div>
<?php } else { ?>
    <p class="italique">Aucune prévisualisation n'est disponible pour cet emplacement.</p>
<?php } ?>