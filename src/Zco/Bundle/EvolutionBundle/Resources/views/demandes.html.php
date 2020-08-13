<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php', array('type' => !empty($_GET['id']) && $_GET['id'] == 2 ? 'tache' : 'bug')) ?>

<h1>Liste des <?php echo !empty($_GET['id']) && $_GET['id'] == 2 ? 'tâches' : 'anomalies' ?></h1>

<?php if(!empty($ListerTickets)){ ?>
<table class="UI_items">
    <thead>
        <tr>
            <td colspan="4">Page : <?php foreach($tableau_pages as $p) echo $p; ?></td>
        </tr>
        <tr>
            <th style="min-width: 10%;">Priorité</th>
            <th style="min-width: 45%;">Titre</th>
            <th style="width: 15%;">État</th>
            <th style="max-width: 15%;">Module concerné</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <td colspan="4">Page : <?php foreach($tableau_pages as $p) echo $p; ?></td>
        </tr>
    </tfoot>

    <tbody>
        <?php foreach($ListerTickets as $t){ ?>
        <tr>
            <td>
                <span class="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_class']); ?> centre">
                    <?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>
                </span>
            </td>

            <td>
                <span style="float: right;">
                    <?php if($t['ticket_prive']){ ?>
                    <img src="/bundles/zcoforum/img/cadenas.png" title="Cette anomalie est privée." alt="Privé" />
                    <?php } ?>

                    <?php if($t['ticket_critique']){ ?>
                    <img src="/bundles/zcoevolution/img/depasse.png" title="Cette anomalie concerne une faille de sécurité critique." alt="Critique" />
                    <?php } ?>
                </span>
                <?php if(verifier('connecte') && $t['ticket_id_version_courante'] != $t['lunonlu_id_version']){ ?>
                    <img src="/bundles/zcoforum/img/fleche.png" alt="Des changements ont été effectués" title="Des changements ont été effectués depuis votre dernière visite" />
                <?php } ?>
                <a href="demande-<?php echo $t['ticket_id']; ?>-<?php echo rewrite($t['ticket_titre']); ?>.html" title="Envoyé <?php echo dateformat($t['ticket_date'], MINUSCULE); ?> par <?php echo htmlspecialchars($t['pseudo_demandeur']); ?>">
                    <?php echo htmlspecialchars($t['ticket_titre']); ?>
                </a>

            </td>

            <td class="centre">
                <span class="<?php echo htmlspecialchars($TicketsEtats[$t['version_etat']]['etat_class']); ?>">
                    <?php echo htmlspecialchars($TicketsEtats[$t['version_etat']]['etat_nom']); ?>
                </span>
            </td>

            <td>
                <?php if(verifier('voir', $t['cat_id'])){ ?>
                <?php echo htmlspecialchars($t['cat_nom']); ?>
                <?php } else echo '(privé)'; ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } else{ ?>
<p>Aucune demande n'a été trouvée.</p>
<?php } ?>