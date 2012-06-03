<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoRecrutementBundle::_onglets.html.php') ?>

<h1><?php echo htmlspecialchars($recrutement['nom']) ?></h1>

<?php if (verifier('recrutements_postuler') && ($recrutement->depotPossible() || !empty($maCandidature))){ ?>
<div class="box center">
	<?php if ($recrutement->depotPossible() && empty($maCandidature)){ ?>
	<a href="postuler-<?php echo $recrutement['id']; ?>.html" class="bold">Postuler maintenant</a>
	<?php } elseif (!empty($maCandidature) && $maCandidature['etat'] == \RecrutementCandidature::ATTENTE_TEST){ ?>
	<a href="postuler-<?php echo $recrutement['id']; ?>.html" class="bold">Passer le test</a>
	<?php } elseif (!empty($maCandidature)){ ?>
	<a href="postuler-<?php echo $recrutement['id']; ?>.html" class="bold">Voir ma candidature</a>
	<?php } ?>
</div>
<?php } ?>

<p class="italic">
	<a href="https://twitter.com/share?text=<?php echo urlencode(str_replace('zCorrecteurs', '@zCorrecteurs', $recrutement['nom'])) ?>&url=<?php echo URL_SITE ?>/recrutement/recrutement-<?php echo $recrutement['id'] ?>-<?php echo rewrite($recrutement['nom']) ?>.html">
		<img src="/bundles/zcotwitter/img/oiseau_16px.png" alt="Twitter" />
		Faire passer le mot sur Twitter
	</a>
</p>

<div class="fluid-row"><div class="span9">
	<?php echo $view['messages']->parse($recrutement['texte']); ?>
</div>

<div class="span3">
	<div class="box">
		<?php if ($recrutement['lien']){ ?>
		<p class="bold center">
			<a href="<?php echo htmlspecialchars($recrutement['lien']) ?>">
				» Posez toutes vos questions sur le forum !
			</a>
		</p><br />
		<?php } ?>

		<ul>
		    <?php if ($recrutement['etat'] != \Recrutement::OUVERT): ?>
			<li>
				<strong>État : </strong>
				<?php echo $recrutement->getEtatAffichage() ?>
			</li>
			<li>
				<strong>Début : </strong>
				<?php echo dateformat($recrutement['date'], MINUSCULE) ?>
			</li>
			<?php endif; ?>
			<li>
				<strong>Fin des candidatures : </strong>
				<?php echo dateformat($recrutement['date_fin_depot'], MINUSCULE) ?>
			</li>
			<li>
				<strong>Affichages : </strong>
				<?php echo $view['humanize']->numberformat($recrutement['nb_lus'], 0) ?>
			</li>
		</ul>
	</div>

	<?php if (verifier('recrutements_editer') || verifier('recrutements_supprimer') || verifier('recrutements_voir_candidatures') || (verifier('recrutements_postuler') && ($InfosRecrutement['depot_possible'] || !empty($InfosCandidature)))){ ?>
	<div class="box">
		<ul>
			<?php if (verifier('recrutements_voir_candidatures')){ ?>
			<li>
				<img src="/img/recrutement/voir.png" alt="" />
				<a href="#candidatures">
					<?php echo count($candidatures) ?> candidature<?php echo pluriel(count($candidatures)) ?>
				</a>
			</li>
			<?php } if (verifier('recrutements_postuler') && !empty($InfosCandidature) && verifier('recrutements_desistement') && !in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_REDACTION, CANDIDATURE_DESISTE, CANDIDATURE_REFUSE, CANDIDATURE_ACCEPTE))) { ?>
			<li>
				<img src="/img/recrutement/desister.png" alt="" />
				<a href="desister-<?php echo $InfosCandidature['candidature_id']; ?>.html">
					Se désister
				</a>
			</li>
			<?php } if(verifier('recrutements_editer')){ ?>
			<li>
				<img src="/img/editer.png" alt="" />
				<a href="editer-recrutement-<?php echo $recrutement['id']; ?>.html">
					Modifier le recrutement
				</a>
			</li>
			<?php } if(verifier('recrutements_supprimer')){ ?>
			<li>
				<img src="/img/supprimer.png" alt="" />
				<a href="supprimer-recrutement-<?php echo $recrutement['id']; ?>.html">
					Supprimer le recrutement
				</a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
</div></div>

<div class="cleaner">&nbsp;</div>

<?php if (verifier('recrutements_voir_candidatures')){ ?>
<h2 id="candidatures">Liste des candidatures</h2>

<p class="bold center">
	<?php if(empty($_GET['tri']) || $_GET['tri'] == 'etat'){ ?>
	Trier par état
	<?php } else{ ?>
	<a href="?#candidatures">Trier par état</a>
	<?php } ?> -

	<?php if(!empty($_GET['tri']) && $_GET['tri'] == 'avis'){ ?>
	Trier par avis
	<?php } else{ ?>
	<a href="?tri=avis#candidatures">Trier par avis</a>
	<?php } ?> -

	<?php if(!empty($_GET['tri']) && $_GET['tri'] == 'note'){ ?>
	Trier par note
	<?php } else{ ?>
	<a href="?tri=note#candidatures">Trier par note</a>
	<?php } ?> -

	<?php if(!empty($_GET['tri']) && $_GET['tri'] == 'id'){ ?>
	Trier par numéro
	<?php } else{ ?>
	<a href="?tri=id#candidatures">Trier par numéro</a>
	<?php } ?>
</p>

<?php if(count($candidatures) > 0){ ?>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>Candidature</th>
			<?php if (verifier('recrutements_avis')): ?>
				<th style="width: 10%;">Avis</th>
			<?php endif; ?>
			<th style="width: 10%;">État</th>
			<th style="width: 20%;">Envoi</th>
			<?php if ($recrutement['quiz_id']): ?>
				<th style="width: 5%;">Score QCM</th>
			<?php endif; ?>
			<?php if ($recrutement['test']): ?>
				<th style="width: 15%;">Correcteur</th>
				<th style="width: 5%;">Note</th>
			<?php endif; ?>
			<?php if (verifier('recrutements_repondre')): ?>
				<th style="width: 10%;">Actions</th>
			<?php endif; ?>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($candidatures as $candidature){ ?>
		<tr>
			<td>
				<?php if (!$candidature['dernier_commentaire']) { ?>
				<img src="/pix.gif" class="fff lightbulb_off" alt="Aucun commentaire" title="Il n'y a pas de commentaire" />
				<?php } elseif (empty($candidature['LuNonLu'])) { ?>
				<a href="candidature-<?php echo $candidature['id'] ?>.html#shoutbox" title="Nouvelles réponses, jamais participé">
					<img src="/pix.gif" class="fff lightbulb" alt="Nouvelles réponses, jamais participé" />
				</a>
				<?php } else { ?>
				<?php if (!$candidature['LuNonLu'][0]['participe']){ ?>
				    <?php if ($candidature['LuNonLu'][0]['commentaire_id'] == $candidature['dernier_commentaire']){ ?>
				        <a href="candidature-<?php echo $candidature['id'] ?>-<?php echo $candidature['LuNonLu'][0]['commentaire_id'];?>.html" title="Pas de nouvelle réponse, jamais participé">
					        <img src="/pix.gif" class="fff lightbulb_off" alt="Pas de nouvelle réponse, jamais participé" />
				        </a>
				    <?php } else{ ?>
				        <a href="candidature-<?php echo $candidature['id'] ?>-<?php echo $candidature['LuNonLu'][0]['commentaire_id'];?>.html" title="Nouvelles réponses, jamais participé">
					        <img src="/pix.gif" class="fff lightbulb" alt="Nouvelles réponses, jamais participé" />
				        </a>
			        <?php } ?>
			    <?php } else { ?>
			        <?php if ($candidature['LuNonLu'][0]['commentaire_id'] == $candidature['dernier_commentaire']){ ?>
				        <a href="candidature-<?php echo $candidature['id'] ?>-<?php echo $candidature['LuNonLu'][0]['commentaire_id'];?>.html" title="Pas de nouvelle réponse, participé">
					        <img src="/pix.gif" class="fff lightbulb_off_add" alt="Pas de nouvelle réponse, participé" />
				        </a>
				    <?php } else{ ?>
				        <a href="candidature-<?php echo $candidature['id'] ?>-<?php echo $candidature['LuNonLu'][0]['commentaire_id'];?>.html" title="Nouvelles réponses, participé">
					        <img src="/pix.gif" class="fff lightbulb_add" alt="Nouvelles réponses, participé" />
				        </a>
			        <?php } ?>
			    <?php } ?>
				<?php } ?>
				<a href="candidature-<?php echo $candidature['id'] ?>.html">
					Candidature de <?php echo ($candidature['Utilisateur']['id']) ? htmlspecialchars($candidature['Utilisateur']['pseudo']) : htmlspecialchars($candidature['pseudo']) ?>
				</a>
			</td>
			<?php if (verifier('recrutements_avis')){ ?>
			<td class="center"<?php if ($candidature['mon_avis']) echo ' style="background: #D1FFD1"' ?>>
				<span class="bold" style="<?php if ($candidature['mon_avis'] === '0') echo 'text-decoration: underline; ' ?>color: <?php echo $avisType[0]['couleur'] ?>;" title="<?php echo $avisType[0]['nom'] ?>"><?php echo $candidature['nb_oui'] ?></span> |
				<span class="bold" style="<?php if ($candidature['mon_avis'] == 1) echo 'text-decoration: underline; ' ?>color: <?php echo $avisType[1]['couleur'] ?>;" title="<?php echo $avisType[1]['nom'] ?>"><?php echo $candidature['nb_non'] ?></span> |
				<span class="bold" style="<?php if ($candidature['mon_avis'] == 2) echo 'text-decoration: underline; ' ?>color: <?php echo $avisType[2]['couleur'] ?>;" title="<?php echo $avisType[2]['nom'] ?>"><?php echo $candidature['nb_reserve'] ?></span>
			</td>
			<?php } ?>
			<td class="center">
				<?php if ($candidature['etat'] == \RecrutementCandidature::ATTENTE_TEST && !$candidature->correctionPossible()): ?>
					<span class="rouge">En test</span>
				<?php else: ?>
					<?php echo $candidature->getEtatAffichage() ?>
				<?php endif; ?>
			</td>
			<td class="center"><?php echo dateformat($candidature['date']) ?></td>
			<?php if ($recrutement['quiz_id']): ?>
				<td class="center">
					<?php if (!empty($candidature['quiz_score'])): ?>
						<?php echo $candidature['quiz_score'] ?>/20
					<?php else: ?>
						-
					<?php endif; ?>
				</td>
			<?php endif; ?>
			<?php if ($recrutement['test']): ?>
			<td class="center">
				<?php if (in_array($candidature['etat'], array(\RecrutementCandidature::TESTE, \RecrutementCandidature::ATTENTE_TEST)) && verifier('recrutements_attribuer_copie')): ?>
					<?php if (!$candidature['correcteur_id']): ?>
						<a href="corriger-<?php echo $candidature['id'] ?>.html">Corriger cette copie</a>
					<?php else: ?>
						<a href="/membres/profil-<?php echo $candidature['Correcteur']['id'] ?>-<?php echo rewrite($candidature['Correcteur']['pseudo']) ?>.html">
							<?php echo htmlspecialchars($candidature['Correcteur']['pseudo']) ?>
						</a>
						<?php if ($candidature['Correcteur']['id'] == $_SESSION['id'] || verifier('recrutements_desattribuer_copie')): ?>
							<a href="corriger-<?php echo $candidature['id'] ?>.html?delete">
								<img src="/img/supprimer.png" title="Supprimer le correcteur" alt="Supprimer le correcteur" />
							</a>
						<?php endif; ?>
					<?php endif; ?>
				<?php else: ?>
					-
				<?php endif; ?>
			</td>
			<td class="center">
				<?php if (!empty($candidature['correcteur_note'])): ?>
					<?php echo $view['humanize']->numberformat($candidature['correcteur_note']) ?>&nbsp;%
				<?php else: ?>
					-
				<?php endif; ?>
			</td>
			<?php endif; ?>
			<?php if (verifier('recrutements_repondre')): ?>
			<td class="center">
				<?php if (in_array($candidature['etat'], array(\RecrutementCandidature::ENVOYE, \RecrutementCandidature::TESTE))): ?>
				<a href="repondre-<?php echo $candidature['id'] ?>.html">
					<img src="/img/recrutement/repondre.png" alt="Répondre" />
				</a>
				<?php else: ?>
				-
				<?php endif; ?>
				<a href="editer-candidature-<?php echo $candidature['id'] ?>.html">
					<img src="/img/editer.png" alt="Modifier" />
				</a>
			</td>
			<?php endif; ?>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p>Aucune candidature n'a encore été déposée.</p>
<?php } ?>
<?php } ?>
