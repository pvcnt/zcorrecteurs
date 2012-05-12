<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php', array('type' => !empty($_GET['id']) && $_GET['id'] == 2 ? 'tache' : 'bug')) ?>

<h1>Liste des <?php echo !empty($_GET['id']) && $_GET['id'] == 2 ? 'tâches' : 'anomalies' ?></h1>

<div class="UI_column_menu">
	<div class="box">
		<h3>Filtres</h3>
		<?php if($filtre == 'recherche'){ ?>
		<a href="rechercher-anomalie.html" class="gras">Recherche</a> (<?php echo $CompterTickets; ?>)<br />
		<?php } ?>
		<a href="demandes-<?php echo $type ?>.html?filtre=open"<?php if($filtre == 'open') echo ' class="gras"'; ?>>Ouverts</a> (<?php echo $CompterTicketsEtat['open']; ?>)<br />
		<a href="demandes-<?php echo $type ?>.html?filtre=new"<?php if($filtre == 'new') echo ' class="gras"'; ?>>Nouveaux</a> (<?php echo $CompterTicketsEtat['new']; ?>)<br />
		<a href="demandes-<?php echo $type ?>.html?filtre="<?php if(is_null($filtre)) echo ' class="gras"'; ?>>Tous</a> (<?php echo $CompterTicketsEtat['all']; ?>)<br />
		<a href="demandes-<?php echo $type ?>.html?filtre=solved"<?php if($filtre == 'solved') echo ' class="gras"'; ?>>Résolus</a> (<?php echo $CompterTicketsEtat['solved']; ?>)<br />
		<?php if(verifier('connecte')){ ?>
		<a href="demandes-<?php echo $type ?>.html?filtre=new_comment"<?php if($filtre == 'new_comment') echo ' class="gras"'; ?>>Non lus</a>
		<?php } ?>
	</div>
</div>

<div class="UI_column_text">
	<?php if(verifier('tracker_ajouter')){ ?>
	<p class="flot_droite">
		<a href="nouveau.html?type=<?php echo $type == 2 ? 'tache' : 'bug' ?>">
			<img src="/bundles/zcoforum/img/nouveau.png" alt="Rapporter une anomalie" />
		</a>
	</p>
	<?php } ?>

	<form method="post" action="demandes-<?php echo $type ?>.html?filtre=<?php echo $filtre; ?>">
		<input type="text" name="titre" id="titre"<?php if(!empty($_POST['titre'])) echo 'value="'.htmlspecialchars($_POST['titre']).'"'; ?> />
		<select name="orderby" id="orderby">
			<option value="priorite"<?php if(!empty($_POST['orderby']) && $_POST['orderby'] == 'priorite') echo ' selected="selected"'; ?>>par importance</option>
			<option value="etat"<?php if(!empty($_POST['orderby']) && $_POST['orderby'] == 'etat') echo ' selected="selected"'; ?>>par état</option>
			<option value="recent"<?php if(!empty($_POST['orderby']) && $_POST['orderby'] == 'recent') echo ' selected="selected"'; ?>>le plus récent en premier</option>
			<option value="ancien"<?php if(!empty($_POST['orderby']) && $_POST['orderby'] == 'ancien') echo ' selected="selected"'; ?>>le plus ancien en premier</option>
			<option value="edition"<?php if(!empty($_POST['orderby']) && $_POST['orderby'] == 'edition') echo ' selected="selected"'; ?>>par date de mise à jour</option>
		</select>

		<input type="submit" value="Rechercher" name="submit" />&nbsp;&nbsp;&nbsp;
		<a href="rechercher-anomalie.html">Recherche avancée</a>
	</form><br />

	<?php if(!empty($ListerTickets)){ ?>
	<table class="UI_items">
		<thead>
			<tr>
				<td colspan="<?php echo $colspan; ?>">Page : <?php foreach($tableau_pages as $p) echo $p; ?></td>
			</tr>
			<tr>
				<th style="min-width: 10%;">Priorité</th>
				<th style="min-width: 45%;">Titre</th>
				<th style="width: 15%;">État</th>
				<?php if(verifier('tracker_voir_assigne')){ ?>
				<th style="width: 15%;">Assigné à</th>
				<?php } ?>
				<th style="max-width: 15%;">Module concerné</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan="<?php echo $colspan; ?>">Page : <?php foreach($tableau_pages as $p) echo $p; ?></td>
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

				<?php if(verifier('tracker_voir_assigne')){ ?>
				<td>
					<?php if(!empty($t['id_admin'])){ ?>
					<a href="/membres/profil-<?php echo $t['id_admin']; ?>-<?php echo rewrite($t['pseudo_admin']); ?>.html" style="color:<?php echo $t['class_admin']; ?>"><?php echo htmlspecialchars($t['pseudo_admin']); ?></a>
					<?php } else echo '-'; ?>
				</td>
				<?php } ?>

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
</div>
