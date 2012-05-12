<?php $view->extend('::layouts/default.html.php') ?>
<?php echo $view->render('ZcoAnnoncesBundle::_onglets.html.php') ?>

<h1>Statistiques des annonces globales</h1>

<p>Pour chaque annonce, vous pouvez ici apprécier ses performances.</p>

<?php if (count($annonces) > 0): ?>
<table class="UI_items">
    <thead>
        <tr>
            <th>Nom</th>
			<th style="width: 20%;">Date de début</th>
			<th style="width: 10%;">Affichages</th>
			<th style="width: 10%;">Clics</th>
			<th style="width: 10%;">Taux de clics</th>
			<th style="width: 10%;">Fermetures</th>
            <th style="width: 10%;">Actions</th>
        </tr>
    </thead>
    
    <tbody>
        <?php foreach ($annonces as $annonce): ?>
        <tr<?php if ($annonce->estAffichable()) echo ' class="UI_inforow"' ?>>
            <td>
                <a onmouseover="$('annonce<?php echo $annonce['id'] ?>').setStyle('display', 'block');" onmouseout="$('annonce<?php echo $annonce['id'] ?>').setStyle('display', 'none');" title="Visualiser la bannière en action" href="/?_annonce=<?php echo $annonce['id'] ?>"><?php echo htmlspecialchars($annonce['nom']) ?></a>
                <div id="annonce<?php echo $annonce['id'] ?>" style="position: absolute; display: none; width: 80%; font-weight: normal;">
                    <?php echo $annonce->genererHTML() ?>
                </div>
            </td>
			<td class="centre"><?php echo dateformat($annonce['date_debut']) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($annonce['nb_affichages'] + $view['cache']->get('annonce_nbv-'.$annonce['id'], 0), 0) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($annonce['nb_clics'], 0) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat($annonce['nb_affichages'] + $view['cache']->get('annonce_nbv-'.$annonce['id'], 0) > 0 ? $annonce['nb_clics'] / ($annonce['nb_affichages'] + $view['cache']->get('annonce_nbv-'.$annonce['id'], 0)) : 0, 2) ?> %</td>
			<td class="centre"><?php echo $view['humanize']->numberformat($annonce['nb_fermetures'], 0) ?></td>
            <td class="centre">
				<?php if (verifier('annonces_ajouter')): ?>
				<a href="ajouter-<?php echo $annonce['id'] ?>.html"><img src="/img/copier.png" alt="Copier" /></a>
				<?php endif; ?>
                <?php if (verifier('annonces_modifier')): ?>
                <a href="modifier-<?php echo $annonce['id'] ?>.html"><img src="/img/editer.png" alt="Modifier" /></a>
                <?php endif; ?>
                <?php if (verifier('annonces_supprimer')): ?>
                <a href="supprimer-<?php echo $annonce['id'] ?>.html"><img src="/img/supprimer.png" alt="Supprimer" /></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>Aucune annonce n'a encore été créée.</p>
<?php endif; ?>
