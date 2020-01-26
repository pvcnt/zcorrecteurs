<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php', array('type' => $InfosTicket['ticket_type'])) ?>

<?php if(verifier('tracker_repondre')){ ?>
<div class="flot_droite">
	<a href="repondre-<?php echo $_GET['id']; ?>.html">
		<img src="/bundles/zcoforum/img/repondre.png" alt="" />
	</a>
</div>
<?php } ?>

<h1><?php echo htmlspecialchars($InfosTicket['ticket_titre']); ?></h1>

<p>
	<span class="flot_droite">
		<?php if($InfosTicket['ticket_critique']){ ?>
		<img src="/bundles/zcoevolution/img/depasse.png" title="Cette demande concerne une faille de sécurité." alt="Critique" />
		<?php } ?>

		<?php if($InfosTicket['ticket_prive']){ ?>
		<img src="/bundles/zcoforum/img/cadenas.png" title="Cette demande est privée." alt="Privé" />
		<?php } ?>

		Demande envoyée par <a href="/membres/profil-<?php echo $InfosTicket['id_demandeur']; ?>-<?php echo rewrite($InfosTicket['pseudo_demandeur']); ?>.html" style="color:<?php echo $InfosTicket['groupe_class_demandeur']; ?>"><?php echo htmlspecialchars($InfosTicket['pseudo_demandeur']); ?></a>
		<?php echo dateformat($InfosTicket['ticket_date'], MINUSCULE); ?>.
		<?php if($InfosTicket['version_date'] != $InfosTicket['ticket_date']){ ?>
		| Mis à jour <?php echo dateformat($InfosTicket['version_date'], MINUSCULE); ?>.
		<?php } ?>
	</span>

	<img src="/img/objets/<?php echo $InfosTicket['ticket_type'] == 'bug' ? 'bug' : 'tache' ?>.png" alt="" />
	<a href="demande-<?php echo $_GET['id']; ?>-<?php echo rewrite($InfosTicket['ticket_titre']); ?>.html"><?php echo $InfosTicket['ticket_type'] == 'bug' ? 'Anomalie' : 'Tâche' ?> n<sup>o</sup>&nbsp;<?php echo $_GET['id']; ?></a>
</p><br />


<div class="UI_column_menu">
	<?php include(dirname(__FILE__).'/_actions_anomalie.html.php'); ?>

	<?php if(verifier('connecte') || !empty($ListerSuivisTickets)){ ?>
	<div class="box">
		<h3>Suivi</h3>
		<?php if(verifier('connecte')){ ?>
		<?php if($InfosTicket['lunonlu_suivi']){ ?>
		<img src="/img/misc/delete.png" alt="" />
		<a href="?suivi=0" id="change_subscription_link">Ne plus suivre cette demande</a>
		<?php } else{ ?>
		<img src="/img/misc/ajouter.png" alt="" />
		<a href="?suivi=1" id="change_subscription_link">
		    Suivre cette demande par MP
		</a>
		<?php } ?><br />
		<?php $view['javelin']->initBehavior('ajax-link', array('id' => 'change_subscription_link')) ?>
		<?php } if(verifier('tracker_forcer_suivi')){ ?>
		<img src="/img/misc/ajouter.png" alt="" />
		<a href="editer-anomalie-<?php echo $_GET['id']; ?>-3.html">
			Inscrire quelqu'un d'autre
		</a><br />
		<?php } ?>

        <span id="liste_suivi">
		    <?php if(!empty($ListerSuivisTicket)){ ?><br />
			<?php foreach($ListerSuivisTicket as $s){ ?>
			<img src="/img/misc/user.png" alt="" />
			<a href="/membres/profil-<?php echo $s['utilisateur_id']; ?>-<?php echo rewrite($s['utilisateur_pseudo']); ?>.html">
				<?php echo htmlspecialchars($s['utilisateur_pseudo']); ?>
			</a><br />
			<?php } ?>
			<?php } ?>
		</span>
	</div>
	<?php } ?>
</div>

<div class="UI_column_text">
	<p>
		<?php echo $view['messages']->parse($InfosTicket['ticket_description'], array(
		    'core.anchor_prefix' => $InfosTicket['ticket_id'],
		    'files.entity_id' => $InfosTicket['ticket_id'],
		    'files.entity_class' => 'TracketTicket',
		)) ?>
	</p>

	<?php if(!empty($InfosTicket['ticket_url'])){ ?>
	<p>
		<img src="/img/misc/world_go.png" alt="" />
		<a href="<?php echo htmlspecialchars($InfosTicket['ticket_url']); ?>">Aller à l'adresse de l'anomalie →</a>
	</p>
	<?php } if(verifier('code') && $InfosTicket['ticket_user_agent'])
		echo '<p><strong>Navigateur : </strong>'.htmlspecialchars($InfosTicket['ticket_user_agent']).'</p>';
	?>


	<h2>Liste des réponses</h2>

	<?php if(count($ListerReponses) > 1){ ?>
	<?php
	$version = array();
	foreach($ListerReponses as $cle => $r){
	if($cle != 0){
	?>
	<div class="UI_msgbox" id="r<?php echo $r['version_id']; ?>">
		<p>
			Par <a href="/membres/profil-<?php echo $r['utilisateur_id']; ?>-<?php echo rewrite($r['utilisateur_pseudo']); ?>.html" style="color: <?php echo $r['groupe_class']; ?>;"><?php echo htmlspecialchars($r['utilisateur_pseudo']); ?></a>,
			<?php echo dateformat($r['version_date'], MINUSCULE); ?>.

			<?php if(verifier('tracker_repondre')){ ?>
			<a href="repondre-<?php echo $_GET['id']; ?>-<?php echo $r['version_id']; ?>.html">
				<img src="/bundles/zcoforum/img/citer.png" alt="Citer" title="Répondre en citant cette réponse" />
			</a>
			<?php } ?>

			<?php if(verifier('tracker_editer_reponses') ||
				($r['utilisateur_id'] == $_SESSION['id'] && verifier('tracker_editer_reponses_siennes'))
			){ ?>
			<a href="modifier-reponse-<?php echo $_GET['id']; ?>-<?php echo $r['version_id']; ?>.html" title="Modifier cette réponse">
				<img src="/img/editer.png" alt="Modifier" />
			</a>
			<?php } ?>

			<?php if(verifier('tracker_supprimer_reponses')){ ?>
			<a href="supprimer-reponse-<?php echo $_GET['id']; ?>-<?php echo $r['version_id']; ?>.html" title="Supprimer cette réponse">
				<img src="/img/supprimer.png" alt="Supprimer" />
			</a>
			<?php } ?>
		</p>
		<hr />

		<?php if(!empty($r['utilisateur_avatar'])){ ?>
            <?php echo $view['messages']->afficherAvatar($r) ?>
		<?php } ?>

		<ul>
			<?php if($r['version_priorite'] != $version['version_priorite']){ ?>
			<li>
				Priorité modifiée
				(<?php echo htmlspecialchars($TicketsPriorites[$version['version_priorite']]['priorite_nom']); ?> &rarr;
				<?php echo htmlspecialchars($TicketsPriorites[$r['version_priorite']]['priorite_nom']); ?>).
			</li>
			<?php } if($r['version_etat'] != $version['version_etat']){ ?>
			<li>
				État modifié
					(<?php echo htmlspecialchars($TicketsEtats[$version['version_etat']]['etat_nom']); ?> &rarr;
					<?php echo htmlspecialchars($TicketsEtats[$r['version_etat']]['etat_nom']); ?>).
				</li>
			<?php } if($r['id_admin'] != $version['id_admin'] && verifier('tracker_voir_assigne')){ ?>
			<li>
				<?php if($r['id_admin']){ ?>
				<?php if($version['id_admin']){ ?>
				Administrateur assigné modifié
				(<a href="/membres/profil-<?php echo $version['id_admin']; ?>-<?php echo rewrite($version['pseudo_admin']); ?>.html"><?php echo htmlspecialchars($version['pseudo_admin']); ?></a> &rarr;
				<a href="/membres/profil-<?php echo $r['id_admin']; ?>-<?php echo rewrite($r['pseudo_admin']); ?>.html"><?php echo htmlspecialchars($r['pseudo_admin']); ?></a>).
				<?php } else{ ?>
				Demande assignée à <a href="/membres/profil-<?php echo $r['id_admin']; ?>-<?php echo rewrite($r['pseudo_admin']); ?>.html"><?php echo htmlspecialchars($r['pseudo_admin']); ?></a>.
				<?php } } else{ ?>
				La demande n'est plus assignée à un administrateur
				(<span class="barre"><a href="/membres/profil-<?php echo $version['id_admin']; ?>-<?php echo rewrite($version['pseudo_admin']); ?>.html"><?php echo htmlspecialchars($version['pseudo_admin']); ?></a></span>).
				<?php } ?>
			</li>
			<?php } if($r['cat_id'] != $version['cat_id']){ ?>
			<li>
				Partie du site concernée modifiée
				(<?php echo htmlspecialchars($version['cat_nom']); ?> &rarr;
				<?php echo htmlspecialchars($r['cat_nom']); ?>).
			</li>
			<?php } ?>
		</ul>

		<?php echo $view['messages']->parse($r['version_commentaire'], array(
		    'core.anchor_prefix' => $r['version_id'],
		    'files.entity_id' => $r['version_id'],
		    'files.entity_class' => 'TracketTicketVersion',
		)) ?>
	</div>
	<?php  } $version = $r; } ?>
	<?php } else{ ?>
	<p>Aucune réponse n'a été déposée à la suite de cette demande.</p>
	<?php } ?>
</div>


<?php if(verifier('tracker_repondre')){ ?>
<div>
	<a href="repondre-<?php echo $_GET['id']; ?>.html">
		<img src="/bundles/zcoforum/img/repondre.png" alt="" />
	</a>
</div>
<?php } ?>
