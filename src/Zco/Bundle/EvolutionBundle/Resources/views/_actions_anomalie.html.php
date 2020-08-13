<div class="box">
	<span class="label">Priorité :</span>
	<span class="<?php echo htmlspecialchars($TicketsPriorites[$InfosTicket['version_priorite']]['priorite_class']); ?>">
		<?php echo htmlspecialchars($TicketsPriorites[$InfosTicket['version_priorite']]['priorite_nom']); ?>
	</span><br />

	<span class="label">État :</span>
	<span class="<?php echo htmlspecialchars($TicketsEtats[$InfosTicket['version_etat']]['etat_class']); ?>">
		<?php echo htmlspecialchars($TicketsEtats[$InfosTicket['version_etat']]['etat_nom']); ?>
	</span><br />

	<span class="label">Partie du site :</span>
	<?php if(verifier('voir', $InfosTicket['cat_id'])){ ?>
	<?php echo htmlspecialchars($InfosTicket['cat_nom']); ?>
	<?php } else echo '(privée)'; ?>
</div>
