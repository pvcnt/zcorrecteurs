<ul class="nav nav-tabs">
    <li<?php if (!isset($currentTab) || $currentTab === 'campaigns') echo ' class="active"' ?>>
        <a href="<?php echo $view['router']->generate('zco_ad_index') ?>?etat[]=en_cours&etat[]=pause&etat[]=termine<?php if (verifier('publicite_voir')) echo '&all=1' ?>">
            Campagnes
        </a>
    </li>
    <li<?php if (isset($currentTab) && $currentTab === 'new') echo ' class="active"'; ?>>
        <?php if (isset($campagne_id)): ?>
            <a href="<?php echo $view['router']->generate('zco_ad_new', array('id' => $campagne_id)) ?>">
        <?php else: ?>
            <a href="<?php echo $view['router']->generate('zco_ad_new') ?>">
        <?php endif ?>
            Créer une publicité
        </a>
    </li>
</ul>