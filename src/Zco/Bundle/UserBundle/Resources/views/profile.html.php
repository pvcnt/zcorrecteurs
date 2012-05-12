<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Profil d<?php echo $art.htmlspecialchars($user->getUsername()) ?></h1>

<p class="center">
	<a href="#presentation">Présentation</a> |
	<a href="#communiquer">Communiquer</a> |
	<a href="#infos">Informations supplémentaires</a> |
	<?php if (!empty($user['signature'])){ ?><a href="#signature">Signature</a> |<?php } ?>
	<?php if ($user['absent']){ ?><a href="#absence">Absence</a> |<?php } ?>
	<?php if (!empty($user['biographie'])){ ?><a href="#biographie">Biographie</a> |<?php } ?>
	<?php if (verifier('membres_voir_ch_pseudos')){ ?><a href="#pseudos">Changements de pseudo</a><?php } ?>
	<?php if (verifier('membres_voir_avertos')){ ?> | <a href="#avertos">Avertissements</a><?php } ?>
	<?php if (verifier('voir_sanctions')){ ?> | <a href="#sanctions">Sanctions</a><?php } ?>
	<?php if (verifier('voir_historique_groupes')) { ?> | <a href="#groupes">Changements de groupe</a><?php } ?>
</p>

<table class="table table-bordered">
	<thead>
		<tr>
			<th colspan="2">
				<?php if(verifier('options_editer_navigation') || verifier('gerer_comptes_valides') || verifier('suppr_comptes') || $user->getId() == $_SESSION['id']){ ?>
				<span class="liens_modo">
					<?php if(verifier('options_editer_navigation') || $_SESSION['id'] == $user->getId()){ ?>
					<a href="/options/navigation-<?php echo $user->getId() ?>.html" title="Modifier les options de navigation">
						<img src="/bundles/zcooptions/img/navigation.png" alt="Modifier les options de navigation" />
					</a>
					<?php } if(verifier('gerer_comptes_valides')){ ?>
						<?php if ($user->isAccountValid()): ?>
							<a href="<?php echo $view['router']->generate('zco_user_admin_unvalidateAccount', array('id' => $user->getId())) ?>" 
								title="Dévalider le compte">
								<img src="/img/membres/devalider.png" alt="Dévalider le compte" />
							</a>
						<?php else: ?>
							<a href="<?php echo $view['router']->generate('zco_user_admin_validateAccount', array('id' => $user->getId())) ?>"
								title="Valider le compte">
								<img src="/img/membres/valider.png" alt="Valider le compte" />
							</a>
						<?php endif ?>
					<?php } if(verifier('suppr_comptes')){ ?>
					<a href="/membres/supprimer-<?php echo $user->getId() ?>.html" title="Supprimer le compte">
						<img src="/img/supprimer.png" alt="Supprimer le compte" />
					</a>
					<?php } ?>
				</span>
				<?php } ?>

				<span class="pseudo">
				<?php /*
					<img src="/img/<?php echo $InfosMembre['statut_connecte']; ?>" 
						alt="<?php echo $InfosMembre['statut_connecte_label']; ?>" 
						title="<?php echo $InfosMembre['statut_connecte_label']; ?>" /> */ ?>
					<strong>Pseudonyme : </strong> 
					<?php echo htmlspecialchars($user->getUsername()) ?>
				</span>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="2" class="center">
				<?php if(verifier('groupes_changer_membre')){ ?>
				<span class="liens_modo">
					<a href="/groupes/changer-membre-groupe-<?php echo $user->getId() ?>.html" 
						title="Changer de groupe">
						<img src="/img/membres/changer_groupe.png" alt="Changer de groupe" />
					</a>
				</span>
				<?php } ?>

				<strong>Groupe : </strong> 
				<span style="color: <?php echo htmlspecialchars($user->Groupe['class']) ?>;">
					<?php echo htmlspecialchars($user->Groupe['nom']) ?>
				</span>

                <?php if (verifier('voir_groupes_secondaires')): ?>
					<br /><strong>Groupes secondaires : </strong>
					<?php if (($c = count($user->SecondaryGroups)) === 0): ?>
						<em>aucun</em>
					<?php else: ?>
						<?php foreach ($user->getSecondaryGroups() as $i => $group): ?>
							<span style="color: <?php echo htmlspecialchars($group->getGroup()->getCssClass()) ?>;">
								<?php echo htmlspecialchars($group->getGroup()->getName()) ?>
							</span>
							<?php if ($i != $c - 1): ?> - <?php endif ?>
						<?php endforeach ?>
	                <?php endif ?>
				<?php endif ?>
			</td>
		</tr>

		<tr>
			<td class="cellule_large">
				<?php if(verifier('options_editer_avatars') || verifier('options_editer_pass') || verifier('membres_editer_titre') || verifier('membres_editer_pseudos') || $_SESSION['id'] == $user->getId()){ ?>
					<span class="liens_modo">
						<?php if(verifier('membres_editer_pseudos')){ ?>
						<a href="/membres/changement-pseudo-<?php echo $user->getId() ?>.html" title="Modifier le pseudonyme">
							<img src="/img/membres/ch_pseudo.png" alt="Modifier le pseudonyme" />
						</a>
						<?php } if(verifier('membres_editer_titre') || ($_SESSION['id'] == $user->getId() && verifier('membres_editer_propre_titre'))){ ?>
						<a href="<?php echo $view['router']->generate('zco_user_editTitle', array('id' => $user->getId())) ?>" title="Modifier le titre">
							<img src="/img/misc/titre.png" alt="Modifier le titre" />
						</a>
						<?php } if(verifier('options_editer_pass') || $_SESSION['id'] == $user->getId()){ ?>
						<a href="/options/modifier-mot-de-passe-<?php echo $user->getId() ?>.html" title="Modifier le mot de passe">
							<img src="/bundles/zcooptions/img/modifier_pass.png" alt="Modifier le mot de passe" />
						</a>
						<?php } if(verifier('options_editer_avatars') || $_SESSION['id'] == $user->getId()){ ?>
						<a href="/options/modifier-avatar-<?php echo $user->getId() ?>.html" title="Modifier l'avatar">
							<img src="/bundles/zcooptions/img/modifier_avatar.png" alt="Modifier l'avatar" />
						</a>
						<?php } ?>
					</span>
				<?php } ?>

				<h3 id="presentation">Présentation</h3>
				<div id="avatar">
					<?php if ($user->hasCitation()): ?>
						«&nbsp;<?php echo htmlspecialchars($user->getCitation()) ?>&nbsp;»
					<?php endif ?><br />
					<?php if ($user->hasAvatar()): ?>
						<img src="/uploads/avatars/<?php echo htmlspecialchars($user->getAvatar()) ?>" alt="Avatar" />
					<?php else: ?>
						<em>Aucun avatar</em>
					<?php endif ?>
					<br/><?php echo $view->get('messages')->userGroup($user) ?>
				</div>

				<ul>
					<?php if ($user->hasTitle()){ ?>
					<li>
						<strong>Titre :</strong> 
						<?php echo htmlspecialchars($user->getTitle()) ?>
					</li>
					<?php } ?>
					<li>
						<strong>Date d'inscription :</strong> 
						<?php echo dateformat($user->getRegistrationDate(), MINUSCULE); ?>
					</li>
					<li>
						<strong>Dernière visite :</strong>
						<?php echo dateformat($user->getLastActionDate(), MINUSCULE); ?>
					</li>
				</ul>
			</td>

			<td class="cellule_large">
				<?php if(verifier('options_editer_mails') || verifier('options_editer_profils') || $_SESSION['id'] == $user->getId()){ ?>
					<span class="liens_modo">
						<?php if(verifier('options_editer_profils') || $_SESSION['id'] == $user->getId()){ ?>
						<a href="/options/modifier-profil-<?php echo $user->getId(); ?>.html#im" title="Modifier les adresses courriel et de messageries instantanées">
							<img src="/bundles/zcooptions/img/modifier_profil.png" alt="Éditer le profil" />
						</a>
						<?php } if(verifier('options_editer_mails') || $_SESSION['id'] == $user->getId()){ ?>
						<a href="/options/modifier-mail-<?php echo $user->getId(); ?>.html" title="Modifier l'adresse courriel">
							<img src="/bundles/zcooptions/img/modifier_mail.png" alt="Modifier l'adresse courriel" />
						</a>
						<?php }  if(verifier('options_editer_absence') || $_SESSION['id'] == $user->getId()){ ?>
						<a href="/options/gerer-absence-<?php echo $user->getId();?>.html" title="Gérer les absences">
							<img src="/bundles/zcooptions/img/gerer_absence.png" alt="Gérer les absences" />
						</a>
						<?php } ?>
					</span>
				<?php } ?>

				<h3 id="communiquer">Communiquer</h3>
				<ul>
					<li><strong>Adresse courriel : </strong>
					<?php if (verifier('rechercher_mail')): ?>
						<?php echo htmlspecialchars($user->getEmail()) ?>
					<?php elseif ($user->isEmailDisplayed()): ?>
						<img src="/uploads/membres/mail/<?php echo $user->getId() ?>.png" alt="Adresse courriel" />
					<?php else: ?>
						<em>ne souhaite pas l'afficher</em>
					<?php endif ?>
					<?php if (verifier('rechercher_mail')){ ?>
						<a href="/membres/rechercher-mail.html?mail=<?php echo htmlspecialchars($user->getEmail()) ?>" title="Rechercher cette adresse">
							<img src="/img/recherche.png" alt="Rechercher cette adresse" />
						</a>
					<?php } ?>
					</li>
					<?php if ($_SESSION['id'] != $user->getId() && verifier('mp_voir') && $user->getId() != ID_COMPTE_AUTO && ($_SESSION['MPs'] < verifier('mp_quota') OR verifier('mp_quota') == -1)) { ?>
					<li><img src="/bundles/zcoforum/img/envoyer_mp.png" alt="MP" title="Envoyer un message privé" /> <a href="/mp/nouveau-<?php echo $user->getId(); ?>.html">Lui envoyer un MP</a></li>
					<?php } ?>
					
					<?php if(verifier('membres_voir_cle_pgp') &&
					verifier('options_ajouter_cle_pgp', 0, $user->getGroupId())){ ?>
					<li style="margin-top: 10px">
						<strong>Clé PGP :</strong>
						<?php if ($user->hasPGPKey()){ ?>
							<a href="#cle_pgp" onclick="document.id('cle_pgp').slide(); return false;">
								Voir sa clé PGP
							</a>
							<div id="cle_pgp" >
								<?php echo nl2br(htmlspecialchars($user->getPGPKey())) ?>
							</div>
						<?php } else echo '<em>non spécifiée</em>'; ?>
					</li>
					<?php } ?>

					<?php if ($user->isAbsent()){ ?>
					<li style="margin-top: 20px"><a href="#absence">
						Ce membre est absent</a>. Fin de l'absence : <?php
						if (is_null($user->getAbsenceEndDate()))
						{
							echo 'indéterminée';
						}
						else
						{
							echo dateformat($user->getAbsenceEndDate(), DATE, MINUSCULE);
						} ?>.</li><?php } ?>
				</ul>
			</td>
		</tr>
	</tbody>
</table>

<table class="table table-bordered">
	<tr id="infos">
		<th colspan="2">
			<?php if (verifier('options_editer_profils') || $_SESSION['id'] == $user->getId()): ?>
				<span class="liens_modo">
					<a href="/options/modifier-profil-<?php echo $user->getId(); ?>.html#infos" title="Modifier les informations">
						<img src="/bundles/zcooptions/img/modifier_profil.png" alt="Éditer le profil" />
					</a>
				</span>
			<?php endif ?>
			Informations supplémentaires
		</th>
	</tr>
	<tr>
		<td colspan="2" class="cellule_large">
			<?php if (verifier('stats_zcorrecteurs') || verifier('quiz_stats') || verifier('voir_tutos_corriges') || verifier('ips_analyser') || verifier('voir_tutos_corriges') || $user->getId() == $_SESSION['id']): ?>
			<div class="flot_droite">
				<ul>
					<?php if(verifier('stats_zcorrecteurs')){ ?>
					<li>
						<img src="/img/membres/stats_zco.png" alt="" /> 
						<a href="/statistiques/zcorrecteur-<?php echo $user->getId(); ?>.html">
							Voir ses statistiques de zCorrection
						</a>
					</li>
					<?php } if(verifier('quiz_stats')|| $_SESSION['id'] == $user->getId()){ ?>
					<li>
						<img src="/img/membres/stats_quiz.png" alt="" /> 
						<a href="/quiz/mes-statistiques-<?php echo $user->getId(); ?>.html">
							Voir <?php echo $user->getId() == $_SESSION['id'] ? 'mes' : 'ses'; ?> statistiques de quiz
						</a>
					</li>
					<?php } if(verifier('voir_tutos_corriges')){ ?>
					<li>
						<img src="/img/membres/soumissions.png" alt="" />
						<a href="/zcorrection/corrections.html?auteur=<?php echo rawurlencode($user->getUsername()) ?>">
							Voir toutes ses soumissions
						</a>
					</li>
					<?php } if(verifier('voir_tutos_corriges')){ ?>
					<li>
						<img src="/img/membres/corrections.png" alt="" /> 
						<a href="/zcorrection/corrections.html?zcorrected=1&amp;zco=<?php echo rawurlencode($user->getUsername()); ?>">
							Voir toutes ses corrections
						</a>
					</li>
					<?php } if(verifier('recrutements_voir_candidatures')){ ?>
					<li>
						<img src="/img/recrutement/postuler.png" alt="" /> 
						<a href="/recrutement/candidatures-membre-<?php echo $user->getId(); ?>.html">
							Voir ses candidatures
						</a>
					</li>
					<?php } if (verifier('ips_analyser')){ ?>
					<li style="margin-top: 10px">
						<strong>Dernière IP connue :</strong>
						<a href="/ips/analyser.html?ip=<?php echo long2ip($user->getLastIpAddress()) ?>">
							<?php echo long2ip($user->getLastIpAddress()) ?>
						</a>
						<?php if(verifier('ips_bannir')){ ?> -
						<a href="/ips/bannir.html?ip=<?php echo long2ip($user->getLastIpAddress()) ?>">
							Bannir cette IP
						</a>
						<?php } ?>
					</li>
					<li>
						<a href="/ips/membre-<?php echo $user->getId() ?>.html">
							Voir l'historique des adresses IP de ce membre
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php endif ?>

			<ul>
				<li>
					<a href="/forum/messages-<?php echo $user->getId() ?>-<?php echo rewrite($user->getUsername()) ?>.html">
						Voir ses messages
					</a>
				</li>
				<li>
					<a href="/forum/detail-messages-<?php echo $user->getId(); ?>.html">Voir le détail de son activité sur le forum
						<?php if (verifier('voir_nb_messages')) echo ' ('.$user->getNbMessages().' message'.pluriel($user->getNbMessages()).')' ?>
					</a>
				</li>
				<li>
					<a href="/forum/sujets-participe-<?php echo $user->getId(); ?>.html">
						Voir les sujets auxquels ce membre a participé
					</a>
				</li>
	   			<li>
					<a href="/blog/billets-rediges-<?php echo $user->getId(); ?>.html">
						Voir les billets que ce membre a rédigés
					</a>
				</li>
				
				<li style="margin-top: 20px">
					<strong>Âge : </strong>
					<?php if ($user->hasBirthDate()): ?>
						<?php echo $user->getAge() ?> ans 
						(né(e) <?php echo $view['humanize']->dateformat($user->getBirthDate(), MINUSCULE, DATE); ?>)
					<?php else: ?>
						<em>non spécifié</em>
					<?php endif ?>
				</li>
				<li>
					<strong>Profession / études : </strong>
					<?php if ($user->hasJob()): ?>
					 	<?php echo htmlspecialchars($user->getJob()) ?>
					<?php else: ?>
						<em>non spécifié</em>
					<?php endif ?>
				</li>
				<li>
					<strong>Passions : </strong>
					<?php if ($user->hasHobbies()): ?>
					 	<?php echo htmlspecialchars($user->getHobbies()) ?>
					<?php else: ?>
						<em>non spécifiées</em>
					<?php endif ?>
				</li>
				<?php if(verifier('voir_adresse') &&
					verifier('modifier_adresse', 0, $user->getGroupId())): ?>
	   			<li>
					<strong>Adresse (ou ville) : </strong>
					<?php if ($user->hasAddress()): ?>
					 	<?php echo htmlspecialchars($user->getAddress()) ?>
					<?php else: ?>
						<em>non spécifiée</em>
					<?php endif ?>
				</li>
	   			<?php endif ?>
				<?php if ($user->isCountryDisplayed() && $user->hasLocalisation()): ?>
	   			<li>
					<strong>Pays : </strong>
					<?php echo htmlspecialchars($user->getLocalisation()) ?>
				</li>
				<?php endif ?>
				
	   			<li style="margin-top: 20px">
	   				<strong>Site web : </strong>
					<?php if ($user->hasWebsite()): ?>
						<?php if (filter_var($user->getWebsite(), FILTER_VALIDATE_URL)): ?>
							<a href="<?php echo htmlspecialchars($user->getWebsite()) ?>">
								<?php echo htmlspecialchars($user->getWebsite()) ?>
							</a>
						<?php else: ?>
							<?php echo htmlspecialchars($user->getWebsite()) ?>
						<?php endif ?>
					<?php else: ?>
						<em>non spécifié</em>
					<?php endif ?>
				</li>
			</ul>
		</td>
	</tr>
</table>

<?php if ($user->isAbsent()): ?>
<table class="table table-bordered">
	<tr class="bigcat cellule_titre" id="absence">
		<td colspan="2">
			Motif de l'absence
		</td>
	</tr>
	<tr>
		<td colspan="2" class="cellule_large"><?php
			if ($user->hasAbsenceReason()): ?>
				<?php echo $view['messages']->parse($user->getAbsenceReason()) ?>
			<?php else: ?>
				Ce membre n'a laissé aucun motif pour son absence.
			<?php endif ?>
		</td>
	</tr>
</table>
<?php endif ?>

<?php if ($user->hasSignature()): ?>
<table class="table table-bordered">
	<thead>
		<tr id="signature">
			<th colspan="2">
				<?php if(verifier('options_editer_profils') || $_SESSION['id'] == $user->getId()){ ?>
					<span class="liens_modo">
						<a href="/options/modifier-profil-<?php echo $user->getId(); ?>.html#signature" title="Modifier la signature"><img src="/bundles/zcooptions/img/modifier_profil.png" alt="Éditer le profil" /></a>
					</span>
				<?php } ?>

				Signature
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="2">
				<?php echo $view['messages']->parse($user->getSignature()) ?>
			</td>
		</tr>
	</tbody>
</table>
<?php endif ?>

<?php if ($user->hasBiography()): ?>
<table class="table table-bordered">
	<thead>
		<tr id="biographie">
			<th colspan="2">
				<?php if(verifier('options_editer_profils') || $_SESSION['id'] == $user->getId()){ ?>
					<span class="liens_modo">
						<a href="/options/modifier-profil-<?php echo $user->getId() ?>.html#biographie" title="Modifier la biographie">
							<img src="/bundles/zcooptions/img/modifier_profil.png" alt="Éditer le profil" />
						</a>
					</span>
				<?php } ?>

				Biographie
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="2" class="cellule_large">
				<?php echo $view['messages']->parse($user->getBiography(), array('core.anchor_prefix' => 'bio')) ?>
			</td>
		</tr>
	</tbody>
</table>
<?php endif ?>

<?php if (verifier('membres_voir_ch_pseudos')): ?>
<table class="table table-bordered">
	<tr id="pseudos">
		<th colspan="8">Changements de pseudo</th>
	</tr>
	<tr>
		<td colspan="8">
			<ul>
				<li><strong>Nombre de changements de pseudo : </strong> <?php echo count($newPseudo) ?></li>
			</ul>
		</td>
	</tr>
	<?php if (count($newPseudo) > 0): ?>
	<?php $ch_etats = array(CH_PSEUDO_ACCEPTE => '<span class="vertf">Accepté</span>', CH_PSEUDO_ATTENTE => 'En attente', CH_PSEUDO_AUTO => 'Automatique', CH_PSEUDO_REFUSE => '<span class="rouge">Refusé</span>'); ?>
		<tr>
			<th style="width: 9%;">Ancien pseudo</th>
			<th style="width: 10%;">Nouveau pseudo</th>
			<th style="width: 8%;">Admin</th>
			<th style="width: 10%;">Date</th>
			<th style="width: 10%;">Date réponse</th>
			<th style="width: 5%;">État</th>
			<th style="width: 25%;">Raison</th>
			<th style="width: 25%;">Réponse</th>
		</tr>
		<?php foreach ($newPseudo as $query): ?>
		<tr>
			<td><?php echo htmlspecialchars($query->getOldUsername()) ?></td>
			<td><?php echo htmlspecialchars($query->getNewUsername()) ?></td>
			<td>
				<?php if ($query->getStatus() != CH_PSEUDO_ATTENTE): ?>
				<?php if ($query->getAdmin()): ?>
					<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $query->getAdminId(), 'slug' => rewrite($query->getAdmin()->getUsername()))) ?>">
						<?php echo htmlspecialchars($query->getAdmin()->getUsername()) ?>
					</a>
				<?php else: ?>
					Anonyme
				<?php endif ?>
				<?php else: ?>
				-
				<?php endif ?>
			</td>
			<td class="center"><?php echo dateformat($query->getDate(), DATE); ?></td>
			<td class="center">
				<?php if (in_array($query->getStatus(), array(CH_PSEUDO_ACCEPTE, CH_PSEUDO_REFUSE))): ?>
				 	<?php echo dateformat($query->getResponseDate(), DATE) ?>
				<?php else: ?>
				 	-
				<?php endif ?>
			</td>
			<td><?php echo $ch_etats[$query->getStatus()] ?></td>
			<td><?php echo $view['messages']->parse($query->getReason()) ?></td>
			<td><?php echo $view['messages']->parse($query->getAdminResponse()) ?></td>
		</tr>
		<?php endforeach ?>
	<?php endif ?>
</table>
<?php endif ?>

<?php if (verifier('membres_voir_avertos')): ?>
<table class="table table-bordered">
	<tr id="avertos">
		<th colspan="6">Avertissements</th>
	</tr>
	<tr>
		<td colspan="6">
			<ul>
				<li>
					<strong>Pourcentage actuel : </strong> <?php echo $user->getPercentage() ?> %
					<?php if (verifier('membres_avertir')): ?> - 
						<a href="<?php echo $view['router']->generate('zco_user_admin_warn', array('id' => $user->getId())) ?>">
							Modifier le niveau d'avertissement de ce membre
						</a>
					<?php endif ?>
				</li>
			</ul>
		</td>
	</tr>
	<?php if (count($warnings) > 0): ?>
		<tr>
			<th style="width: 10%;">Admin</th>
			<th style="width: 15%;">Date</th>
			<th style="width: 10%;">Niveau</th>
			<th style="width: 5%;">Lien</th>
			<th style="width: 30%;">Raison admin</th>
			<th style="width: 30%;">Raison</th>
		</tr>
		<?php foreach ($warnings as $warning): ?>
		<tr>
			<td>
				<?php if ($warning->getAdmin()): ?>
				<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $warning->getAdminId(), 'slug' => rewrite($warning->getAdmin()->getUsername()))) ?>">
					<?php echo htmlspecialchars($warning->getAdmin()->getUsername()) ?>
				</a>
				<?php else: ?>
				Anonyme
				<?php endif ?>
			</td>
			<td class="center">
				<?php echo dateformat($warning->getDate(), DATE) ?>
			</td>
			<td class="center">
				<span class="<?php if ($warning->getPercentage() < 0) echo ' vertf'; elseif ($warning->getPercentage() > 0) echo 'rouge'; ?>">
					<?php echo $warning->getPercentage() ?> %
				</span>
			</td>
			<td class="center">
				<?php if ($warning->hasLink()): ?>
				<a href="<?php echo htmlspecialchars($warning->getLink()); ?>">Lien</a>
				<?php else: ?>
				-
				<?php endif ?>
			</td>
			<td><?php echo $view['messages']->parse($warning->getAdminReason()) ?></td>
			<td><?php echo $view['messages']->parse($warning->getReason()) ?></td>
		</tr>
		<?php endforeach ?>
	<?php endif ?>
</table>
<?php endif ?>

<?php if (verifier('voir_sanctions')): ?>
<table class="table table-bordered">
	<tr id="sanctions">
		<th colspan="<?php echo verifier('sanctionner') ? 8 : 7 ?>">Sanctions</th>
	</tr>
	<tr>
		<td colspan="<?php echo verifier('sanctionner') ? 8 : 7 ?>" class="cellule_large">
			<ul>
				<li>
					<strong>Nombre de sanctions : </strong> 
					<?php echo $user->getNbSanctions() ?>
					<?php if (verifier('sanctionner')): ?> - 
					 	<a href="<?php echo $view['router']->generate('zco_user_admin_punish', array('id' => $user->getId())) ?>">
							Sanctionner ce membre
						</a>
					<?php endif ?>
				</li>
			</ul>
		</td>
	</tr>
	<?php if (count($punishments) > 0): ?>
	<tr>
		<th style="width: 8%;">Admin</th>
		<th style="width: 8%;">Sanction</th>
		<th style="width: 10%;">Date</th>
		<th style="width: 5%;">Litige</th>
		<th style="width: 30%;">Raison admin</th>
		<th style="width: 30%;">Raison</th>
		<th style="width: 10%;">Durée</th>
		<?php if (verifier('sanctionner')): ?>
		<th style="width: 5%;">Arrêter</th>
		<?php endif ?>
	</tr>
	<?php foreach ($punishments as $punishment): ?>
	<tr>
		<td>
			<?php if ($punishment->getAdmin()): ?>
			<a href="<?php echo $view['router']->generate('zco_user_profile', array('id' => $punishment->getAdminId(), 'slug' => rewrite($punishment->getAdmin()->getUsername()))) ?>">
				<?php echo htmlspecialchars($punishment->getAdmin()->getUsername()) ?>
			</a>
			<?php else: ?>
			Anonyme
			<?php endif ?>
		</td>
		<td><?php echo htmlspecialchars($punishment->getGroup()) ?></td>
		<td class="center">
			<?php echo dateformat($punishment->getDate(), DATE) ?>
		</td>
		<td class="center">
			<?php if ($punishment->hasLink()): ?>
			<a href="<?php echo htmlspecialchars($punishment->getLink()); ?>">Lien</a>
			<?php else: ?>
			-
			<?php endif ?>
		</td>
		<td><?php echo $view['messages']->parse($punishment->getAdminReason()) ?></td>
		<td><?php echo nl2br(htmlspecialchars($punishment->getReason())) ?></td>
		<td class="center">
			<?php if (!$punishment->isUnlimited()): ?>
				<?php echo $punishment->getDuration() ?> jour<?php echo pluriel($punishment->getDuration()) ?>
				<?php if (!$punishment->isFinished()): ?><br /> 
				(<em><?php echo $punishment->getRemainingDuration() ?> restant<?php echo pluriel($punishment->getRemainingDuration()) ?></em>)
				<?php endif ?>
			<?php else: ?>
				À vie
			<?php endif ?>
		</td>
		<?php if (verifier('sanctionner')): ?>
		<td class="center">
			<?php if (!$punishment->isFinished()): ?>
				<a href="<?php echo $view['router']->generate('zco_user_admin_cancelPunishment', array('id' => $punishment->getId())) ?>" title="Arrêter la sanction">
					<img src="/img/misc/delete.png" alt="Arrêter" />
				</a>
			<?php else: ?>
				Finie
			<?php endif ?>
		</td>
		<?php endif ?>
	</tr>
	<?php endforeach ?>
	<?php endif ?>
</table>
<?php endif ?>

<?php if (verifier('voir_historique_groupes')): ?>
<table class="table table-bordered">
	<tr id="groupes">
		<th colspan="4">Changements de groupe</th>
	</tr>
	<tr>
		<td colspan="4">
			<ul>
				<li><strong>Nombre de changements de groupe : </strong> <?php echo count($ListerGroupes); ?></li>
			</ul>
		</td>
	</tr>
	<?php if($ListerGroupes){ ?>
		<tr>
			<th style="width:15%">Responsable du changement</th>
			<th>Date</th>
			<th>Ancien groupe</th>
			<th>Nouveau groupe</th>
		</tr>
		<?php foreach($ListerGroupes as $ch){ ?>
		<tr>
			<td class="center"><?php if(!empty($ch['utilisateur_id'])) { ?><a href="profil-<?php echo $ch['utilisateur_id'];?>-<?php echo rewrite($ch['pseudo_responsable']);?>.html"><?php echo htmlspecialchars($ch['pseudo_responsable']);?></a><?php } else { echo htmlspecialchars($ch['pseudo_responsable']); }?></td>
			<td class="center"><?php echo dateformat($ch['chg_date'], DATE); ?></td>
			<td class="center"><span style="color:<?php echo $ch['couleur_ancien_groupe']; ?>"><?php echo $ch['ancien_groupe']; ?></span></td>
			<td class="center"><span style="color:<?php echo $ch['couleur_nouveau_groupe']; ?>"><?php echo $ch['nouveau_groupe']; ?></span></td>
		</tr>
		<?php } ?>
	<?php } ?>
</table>
<?php endif ?>

<?php $view['javelin']->onload("if (document.id('cle_pgp')) document.id('cle_pgp').slide('hide');") ?>
<?php $view['vitesse']->requireResources(array(
    '@ZcoCoreBundle/Resources/public/css/zcode.css',
    '@ZcoUserBundle/Resources/public/css/profil.css',
)) ?>