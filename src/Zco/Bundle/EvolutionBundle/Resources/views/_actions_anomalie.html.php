<div class="box">
	<span class="label">Priorité :</span>
	<span class="<?php echo htmlspecialchars($TicketsPriorites[$InfosTicket['version_priorite']]['priorite_class']); ?>">
		<?php echo htmlspecialchars($TicketsPriorites[$InfosTicket['version_priorite']]['priorite_nom']); ?>
	</span><br />

	<span class="label">État :</span>
	<span class="<?php echo htmlspecialchars($TicketsEtats[$InfosTicket['version_etat']]['etat_class']); ?>">
		<?php echo htmlspecialchars($TicketsEtats[$InfosTicket['version_etat']]['etat_nom']); ?>
	</span><br />

	<?php if(verifier('roadmap_voir') && !empty($InfosTicket['projet_id'])){ ?>
	<span class="label">Projet cible :</span>
	<?php if(!empty($InfosTicket['projet_id']) && (!$InfosTicket['projet_prive'] || verifier('roadmap_voir_prives'))){ ?>
	<a href="roadmap-2-<?php echo $InfosTicket['projet_id']; ?>.html"><?php echo htmlspecialchars($InfosTicket['projet_nom']); ?></a>
	<?php } elseif($InfosTicket['projet_prive']) echo '(privé)'; else echo '-'; ?><br />
	<?php } ?>

	<?php if(verifier('tracker_voir_assigne')){ ?>
	<span class="label">Assigné à :</span>
	<?php if(!empty($InfosTicket['id_admin'])){ ?>
	<a href="/membres/profil-<?php echo $InfosTicket['id_admin']; ?>-<?php echo rewrite($InfosTicket['pseudo_admin']); ?>.html" style="color:<?php echo $InfosTicket['groupe_class_admin']; ?>"><?php echo htmlspecialchars($InfosTicket['pseudo_admin']); ?></a>
	<?php } else echo '-'; ?><br />
	<?php } ?>

	<span class="label">Partie du site :</span>
	<?php if(verifier('voir', $InfosTicket['cat_id'])){ ?>
	<?php echo htmlspecialchars($InfosTicket['cat_nom']); ?>
	<?php } else echo '(privée)'; ?>
</div>

<?php if(verifier('tracker_editer') || ($InfosTicket['id_demandeur'] == $_SESSION['id'] && verifier('tracker_editer_siens'))){ ?><div class="box">
	<img src="/img/editer.png" alt="" />
	<a href="editer-anomalie-<?php echo $_GET['id']; ?>-1.html">Modifier la demande</a><br />
</div>
<?php } ?>
