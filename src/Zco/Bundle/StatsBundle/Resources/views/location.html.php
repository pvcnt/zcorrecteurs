<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Statistiques de géolocalisation</h1>

<table class="table table-striped" style="width: 500px; float: right;">
    <thead>
    <tr>
        <th>Provenance</th>
        <th>Répartition</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $sous_total = 0;
    foreach ($Stats as $pays => $pourcent) {
        if ($pourcent >= 1) $sous_total += $pourcent;
        elseif ($sous_total != 0) {
            ?>
            <tr class="bold">
                <td>Sous-total des pays représentatifs :</td>
                <td class="centre">
                    <?php echo $view['humanize']->numberformat($sous_total); ?> % &nbsp;&nbsp;&nbsp;
                    (<?php echo round($sous_total * $NbUtilisateurs / 100); ?>
                    membre<?php echo pluriel(round($sous_total * $NbUtilisateurs / 100)); ?>)
                </td>
            </tr>
            <?php $sous_total = 0;
        } ?>
        <tr>
            <td><?php echo htmlspecialchars($pays); ?></td>
            <td class="center">
                <?php echo $view['humanize']->numberformat($pourcent); ?> % &nbsp;&nbsp;&nbsp;
                (<?php echo round($pourcent * $NbUtilisateurs / 100); ?>
                membre<?php echo pluriel(round($pourcent * $NbUtilisateurs / 100)); ?>)
            </td>
        </tr>
    <?php } ?>
    <tr class="bold">
        <td>Total :</td>
        <td class="center">100,0&nbsp;% (<?php echo $NbUtilisateurs; ?> membres)</td>
    </tr>
    </tbody>
</table>

<img src="<?php echo $view['router']->path('zco_stats_locationChart') ?>"
     alt="Statistiques de géolocalisation des membres"/>