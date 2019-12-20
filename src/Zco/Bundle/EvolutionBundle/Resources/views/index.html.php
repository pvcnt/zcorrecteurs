<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php') ?>

<h1>Aperçu rapide des anomalies et suggestions</h1>

<div class="UI_column_menu">
	<?php if(verifier('tracker_ajouter')){ ?>
	<div class="box">
		<a href="nouveau.html?type=bug">Signaler une anomalie</a><br />
		<a href="nouveau.html?type=tache">Ajouter une tâche</a>
	</div>
	<?php } ?>

	<div class="box">
		<h3>Contact</h3>
		Le responsable du développement est
		<a href="/membres/profil-31-vincent1870.html">vincent1870</a>. Si vous avez un doute ou souhaitez absolument le joindre directement, vous pouvez
		<a href="/mp/nouveau-31.html">lui envoyer un MP</a>.
	</div>
</div>

<div class="UI_column_text">
	<p>
		Bienvenue sur notre outil de gestion des demandes. Cet outil a pour objectif de
		faciliter la vie des développeurs en fournissant un moyen standard et
		centralisé de récolter les anomalies et les suggestions. N'hésitez pas à vous
		en servir dès que vous repérez un dysfonctionnement.<br />
		Pour ajouter une nouvelle demande, que ce soit une anomalie ou une suggestion,
		suivez les liens sur cette page. Les développeurs essaieront de donner suite
		à votre demande le plus rapidement possible.
	</p><br />

	<p>Merci de votre participation à la vie du site !</p>

	<table class="UI_boxes" cellspacing="7px">
		<tr>
			<td>
				<h2>Dernières anomalies</h2>
				<dl>
					<?php foreach($DerniersTickets as $t){ ?>
					<dd>
						<img src="/bundles/zcoevolution/img/bug_priorite_<?php echo rewrite($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>.png" alt="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>" title="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>" />
						<a href="demande-<?php echo $t['ticket_id']; ?>-<?php echo rewrite($t['ticket_titre']); ?>.html"><?php echo htmlspecialchars($t['ticket_titre']); ?></a>,
						<?php echo dateformat($t['ticket_date'], MINUSCULE); ?> par
						<a href="/membres/profil-<?php echo $t['utilisateur_id']; ?>-<?php echo rewrite($t['utilisateur_pseudo']); ?>.html" style="color:<?php echo $t['groupe_class']; ?>"><?php echo htmlspecialchars($t['utilisateur_pseudo']); ?></a>
					</dd>
					<?php } ?>
				</dl>
			</td>

			<td>
				<h2>Dernières tâches ajoutées</h2>
				<dl>
					<?php foreach($DernieresTaches as $t){ ?>
					<dd>
						<img src="/bundles/zcoevolution/img/bug_priorite_<?php echo rewrite($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>.png" alt="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>" title="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>" />
						<a href="demande-<?php echo $t['ticket_id']; ?>-<?php echo rewrite($t['ticket_titre']); ?>.html"><?php echo htmlspecialchars($t['ticket_titre']); ?></a>,
						<?php echo dateformat($t['ticket_date'], MINUSCULE); ?> par
						<a href="/membres/profil-<?php echo $t['utilisateur_id']; ?>-<?php echo rewrite($t['utilisateur_pseudo']); ?>.html" style="color:<?php echo $t['groupe_class']; ?>"><?php echo htmlspecialchars($t['utilisateur_pseudo']); ?></a>
					</dd>
					<?php } ?>
				</dl>
			</td>
		</tr>

		<tr>
			<td>
				<h2>Dernières anomalies mises à jour</h2>
				<dl>
					<?php foreach($DerniersTicketsModifies as $t){ ?>
					<dd>
						<img src="/bundles/zcoevolution/img/bug_priorite_<?php echo rewrite($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>.png" alt="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>" title="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>" />
						<a href="demande-<?php echo $t['ticket_id']; ?>-<?php echo rewrite($t['ticket_titre']); ?>.html"><?php echo htmlspecialchars($t['ticket_titre']); ?></a>,
						<?php echo dateformat($t['version_date'], MINUSCULE); ?> par
						<a href="/membres/profil-<?php echo $t['utilisateur_id']; ?>-<?php echo rewrite($t['utilisateur_pseudo']); ?>.html" style="color:<?php echo $t['groupe_class']; ?>"><?php echo htmlspecialchars($t['utilisateur_pseudo']); ?></a>
					</dd>
					<?php } ?>
				</dl>
			</td>

			<td>
				<h2>Dernières tâches mises à jour</h2>
				<dl>
					<?php foreach($DernieresTachesModifiees as $t){ ?>
					<dd>
						<img src="/bundles/zcoevolution/img/bug_priorite_<?php echo rewrite($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>.png" alt="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>" title="<?php echo htmlspecialchars($TicketsPriorites[$t['version_priorite']]['priorite_nom']); ?>" />
						<a href="demande-<?php echo $t['ticket_id']; ?>-<?php echo rewrite($t['ticket_titre']); ?>.html"><?php echo htmlspecialchars($t['ticket_titre']); ?></a>,
						<?php echo dateformat($t['version_date'], MINUSCULE); ?> par
						<a href="/membres/profil-<?php echo $t['utilisateur_id']; ?>-<?php echo rewrite($t['utilisateur_pseudo']); ?>.html" style="color:<?php echo $t['groupe_class']; ?>"><?php echo htmlspecialchars($t['utilisateur_pseudo']); ?></a>
					</dd>
					<?php } ?>
				</dl>
			</td>
		</tr>
	</table>
</div>
