<?php $view->extend('::layouts/default.html.php') ?>

<h1><?php echo htmlspecialchars($InfoMP['mp_titre']); ?></h1>
<h2><?php echo htmlspecialchars($InfoMP['mp_sous_titre']); ?></h2>
<?php
if(!empty($InfoMP['mp_alerte_id']) AND empty($InfoMP['mp_participant_mp_id']))
{
	echo '<p class="rmq attention"><strong>Vous êtes en mode infiltration</strong> : vous ne faites pas partie des participants. Vous pouvez ainsi consulter le MP incognito.<br />Vous pouvez effectuer toutes les actions de votre choix sans être participant (répondre, fermer etc.). Si vous le désirez, il est aussi possible de vous ajouter à la conversation.</p>';
}
elseif(!$autoriser_ecrire)
{
	exit('oopas');
}
?>
<p>Participants à la conversation :</p>
<ul>
<?php
$NombreParticipants = 0;
foreach($ListerParticipants as $valeur)
{
	if($valeur['mp_participant_statut'] != MP_STATUT_SUPPRIME)
	{
		$NombreParticipants++;
	}
	echo '<li>';
	switch($valeur['mp_participant_statut'])
	{
		case MP_STATUT_NORMAL:
			if($InfoMP['mp_participant_statut'] == MP_STATUT_OWNER OR (verifier('mp_tous_droits_participants') AND $autoriser_ecrire))
			{
				echo '<a href="statut-master-'.$_GET['id'].'-'.$valeur['mp_participant_id'].'.html"><img src="/bundles/zcomp/img/monitor_add.png" alt="Rendre maître de conversation" title="Ajouter le statut de maître de conversation" /></a> ';
				if($valeur['mp_participant_id'] != $_SESSION['id'])
				{
					echo '<a href="supprimer-participant-'.$_GET['id'].'-'.$valeur['mp_participant_id'].'.html"><img src="/bundles/zcomp/img/user_delete.png" alt="Supprimer" title="Supprimer le participant de la conversation" /></a> ';
				}
			}
			elseif($InfoMP['mp_participant_statut'] == MP_STATUT_MASTER)
			{
				echo '<a href="supprimer-participant-'.$_GET['id'].'-'.$valeur['mp_participant_id'].'.html"><img src="/bundles/zcomp/img/user_delete.png" alt="Supprimer" title="Supprimer le participant de la conversation" /></a> ';
			}
		break;
		case MP_STATUT_MASTER:
			if($InfoMP['mp_participant_statut'] == MP_STATUT_OWNER OR (verifier('mp_tous_droits_participants') AND $autoriser_ecrire))
			{
				if($valeur['mp_participant_id'] != $_SESSION['id'])
				{
					echo '<a href="statut-normal-'.$_GET['id'].'-'.$valeur['mp_participant_id'].'.html"><img src="/bundles/zcomp/img/monitor_delete.png" alt="Rendre normal" title="Retirer le statut de maître de conversation" /></a> ';
					echo '<a href="supprimer-participant-'.$_GET['id'].'-'.$valeur['mp_participant_id'].'.html">
<img src="/bundles/zcomp/img/user_delete.png" alt="Supprimer" title="Supprimer le participant de la conversation" /></a> ';
				}
			}
			if($InfoMP['mp_participant_statut'] == MP_STATUT_MASTER AND $valeur['mp_participant_id'] == $_SESSION['id'])
			{
				echo '<a href="statut-normal-'.$_GET['id'].'-'.$valeur['mp_participant_id'].'.html"><img src="/bundles/zcomp/img/monitor_delete.png" alt="Me rendre normal" title="Me retirer le statut de maître de conversation" /></a> ';
			}
			echo '<em title="Maître de conversation">';
		break;
		case MP_STATUT_OWNER:
			echo '<strong title="Créateur du MP">';
		break;
		case MP_STATUT_SUPPRIME:
			echo '<strike title="Participant supprimé">';
		break;
	}
	if($valeur['mp_participant_id'] == $_SESSION['id'])
	{
		echo '<a href="supprimer-'.$_GET['id'].'.html"><img src="/bundles/zcomp/img/user_delete.png" alt="Supprimer" title="Me supprimer de la conversation" /></a> ';
	}
	echo '<a href="/membres/profil-'.$valeur['mp_participant_id'].'-'.rewrite($valeur['utilisateur_pseudo']).'.html">';
	echo htmlspecialchars($valeur['utilisateur_pseudo']);
	echo '</a> ';
	switch($valeur['mp_participant_statut'])
	{
		case MP_STATUT_MASTER:
			echo '</em>';
		break;
		case MP_STATUT_OWNER:
			echo '</strong>';
		break;
		case MP_STATUT_SUPPRIME:
			echo '</strike>';
		break;
	}
	echo '</li>';
}
?>
</ul>
<?php
if(	($InfoMP['mp_participant_statut'] >= MP_STATUT_MASTER
		|| (verifier('mp_tous_droits_participants') && $autoriser_ecrire))
	&& ($NombreParticipants < verifier('mp_nb_participants_max')
		|| verifier('mp_nb_participants_max') == -1)
	&& !$InfoMP['mp_crypte']
)
{
	echo '<p><a id="ajouter-participant" href="ajouter-participant-'.$_GET['id'].'.html"><img src="/bundles/zcomp/img/user_add.png" alt="Ajouter" /> Ajouter un membre à la conversation</a>';
	if(!verifier('mp_limite_participants'))
	{
		echo ' ('.(verifier('mp_nb_participants_max') == -1 ? 'participants illimités' : verifier('mp_nb_participants_max').' participants max.').')';
	}
	echo '</p>';
}
if($autoriser_ecrire AND ($NombreParticipants > 1 OR $_SESSION['MPs'] < verifier('mp_quota') OR verifier('mp_quota') == -1))
{
	echo '<p class="reponse_ajout_sujet">';
}
if($NombreParticipants > 1 AND $autoriser_ecrire)
{
?>

	<?php
	if($InfoMP['mp_ferme'])
	{
		if(verifier('mp_repondre_mp_fermes'))
		{
			echo '<a href="repondre-'.$_GET['id'].'.html">';
		}
	?>
		<img src="/bundles/zcoforum/img/ferme.png" alt="Fermé" title="MP fermé" />
	<?php
		if(verifier('mp_repondre_mp_fermes'))
		{
			echo '</a>';
		}
	}
	else
	{
	?>
	<a href="repondre-<?php echo $_GET['id']; ?>.html"><img src="/bundles/zcoforum/img/repondre.png" alt="Répondre" title="Répondre au MP" /></a>
	<?php }
	echo '&nbsp;';
}
if($autoriser_ecrire AND ($MPTotal < verifier('mp_quota') OR verifier('mp_quota') == -1))
{
	echo '<a href="nouveau.html"><img src="/bundles/zcoforum/img/nouveau.png" alt="Nouveau" title="Nouveau MP" /></a>';
}
if($autoriser_ecrire AND ($NombreParticipants > 1 OR $MPTotal < verifier('mp_quota') OR verifier('mp_quota') == -1))
{
	echo '</p>';
}
?>
<table class="UI_items messages">
	<thead>
		<tr>
			<td colspan="2">Page :
			<?php
			foreach($ListePages as $element)
			{
				echo $element.'';
			}
			?>
			</td>
		</tr>
		<tr>
			<th style="width: 13%;">Auteur</th>
			<th style="width: 87%;">Message</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="2">Page :
			<?php
			foreach($ListePages as $element)
			{
				echo $element.'';
			}
			?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	//Ici on fait une boucle qui va nous lister tous les message du MP.
	if($ListerMessages) //Si il y a au moins un message à lister, on liste !
	{
		$numero_message = 0;
		foreach($ListerMessages as $clef => $valeur)
		{
			$numero_message++;
		?>
		<tr class="header_message">
			<td class="pseudo_membre">
			<?php if($valeur['utilisateur_absent']==1) { ?>
			<span class="commandes_textuelles"><a href="/membres/profil-<?php echo $valeur['mp_message_auteur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html#absence"><img src="/bundles/zcooptions/img/gerer_absence.png" alt="Absent" title="Membre absent. Fin : <?php
			if(is_null($valeur['utilisateur_fin_absence']))
			{
				echo 'indéterminée';
			}
			else
			{
				echo dateformat($valeur['utilisateur_fin_absence'], DATE, MINUSCULE);
			}  ?>" /></a></span><?php } ?>
			<a href="/membres/profil-<?php echo $valeur['mp_message_auteur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html">
			<?php echo htmlspecialchars($valeur['utilisateur_pseudo']); ?>
			</a>
			</td>
			<td class="dates">
				<span id="m<?php echo $valeur['mp_message_id'];?>"><a href="lire-<?php echo $_GET['id'].'-'.$valeur['mp_message_id'].'.html'; ?>" rel="nofollow">#</a></span>
				Posté <?php echo dateformat($valeur['mp_message_date'], MINUSCULE); ?>
				<?php if($autoriser_ecrire AND ((!$InfoMP['mp_ferme'] OR verifier('mp_repondre_mp_fermes')) AND $NombreParticipants > 1)) { ?>
				<a href="repondre-<?php echo $_GET['id'].'-'.$valeur['mp_message_id'];?>.html"><img src="/bundles/zcoforum/img/citer.png" alt="Citer" title="Citer" /></a>
				<?php } ?>
				<?php if( ($MPTotal < verifier('mp_quota') OR verifier('mp_quota') == -1) AND $_SESSION['id'] != $valeur['mp_message_auteur_id']) { ?>
				<a href="nouveau-<?php echo $valeur['mp_message_auteur_id']; ?>.html"><img src="/bundles/zcoforum/img/envoyer_mp.png" alt="MP" title="Envoyer un message privé" /></a>
				<?php }

				if($autoriser_ecrire AND (!isset($valeur['pas_autoriser_edition']) AND $_SESSION['id'] == $valeur['mp_message_auteur_id']))
				{
					if(verifier('mp_repondre_mp_fermes') OR !$InfoMP['mp_ferme'])
					{
				?>
					<a href="editer-<?php echo $valeur['mp_message_id']; ?>.html"><img src="/img/editer.png" alt="Éditer" title="Éditer" /></a>
				<?php
					}
				}
				?>
			</td>
		</tr>
		<tr>
			<td class="infos_membre">
			<?php
				if(!empty($valeur['utilisateur_citation'])){ echo htmlspecialchars($valeur['utilisateur_citation']) . '<br />'; }
				if(!empty($valeur['utilisateur_avatar']))
				{
				?>
				<a href="/membres/profil-<?php echo $valeur['mp_message_auteur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html"><img src="/uploads/avatars/<?php echo $valeur['utilisateur_avatar']; ?>" alt="<?php echo htmlspecialchars($valeur['utilisateur_pseudo']); ?>" /></a><br />
				<?php
				}
			echo $view['messages']->afficherGroupe($valeur) ?><br/>
			<?php if(!empty($valeur['utilisateur_titre']))
			{
				echo htmlspecialchars($valeur['utilisateur_titre']).'<br />';
			}
			if(verifier('sanctionner'))
			{
			?>
			<br /><a href="<?php echo $view['router']->path('zco_user_admin_punish', array('id' => htmlspecialchars($valeur['mp_message_auteur_id']))) ?>">
				Sanctionner (<?php echo $valeur['utilisateur_nb_sanctions']; ?>)
			</a>
			<?php
			}
			elseif(verifier('voir_sanctions') && $valeur['utilisateur_nb_sanctions'] > 0){
			?>
			<br /><a href="/membres/profil-<?php echo $valeur['mp_message_auteur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html#sanctions">Sanction(s) : <?php echo $valeur['utilisateur_nb_sanctions']; ?></a>
			<?php
			}
			if(verifier('ips_analyser') && !empty($valeur['mp_message_ip']))
			{
				echo '<br /><br />IP : <a href="'.$view['router']->path('zco_user_ips_locate', ['ip' => long2ip($valeur['mp_message_ip'])]).'">'.long2ip($valeur['mp_message_ip']).'</a>';
			}
				?>
			</td>
			<td class="message">
				<div class="msgbox">
					<?php
					if($numero_message == 1 AND $page > 1 AND $page <= ceil(($InfoMP['mp_reponses']+1) / 20))
					{
						echo $view['messages']->parse('<position valeur="centre"><gras>Reprise du dernier message de la page précédente :</gras></position>');
						echo '<br />';
					}
					?>
					<?php
					//Affichage du message
					echo preg_replace('`&amp;#(\d+);`', '&#$1;', $view['messages']->parse($valeur['mp_message_texte']));
					?>
					<?php
					if(!empty($valeur['utilisateur_signature']))
					{
					?>
					<div class="signature"><hr />
					<?php echo $view['messages']->parse($valeur['utilisateur_signature']); ?>
					</div>
					<?php
					}
					?>
					<div class="cleaner">&nbsp;</div>
				</div>
			</td>
		</tr>
		<?php
		}
	}
	else
	{
		?>
		<tr class="sous_cat">
			<td colspan="2" class="centre">Ce MP ne contient pas de message.</td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>
<?php if($autoriser_ecrire)
{
	echo '<p class="centre"><a href="index.html"><strong>Retour à la liste des MP</strong></a></p>';
}
else
{
	echo '<p class="centre"><a href="/admin/index.html"><strong>Retour à l\'administration</strong></a></p>';
}

if($autoriser_ecrire AND ($NombreParticipants > 1 OR $MPTotal < verifier('mp_quota') OR verifier('mp_quota') == -1))
{
	echo '<p class="reponse_ajout_sujet">';
}
if($autoriser_ecrire AND ($NombreParticipants > 1))
{
?>

	<?php
	if($InfoMP['mp_ferme'])
	{
		if(verifier('mp_repondre_mp_fermes'))
		{
			echo '<a href="repondre-'.$_GET['id'].'.html">';
		}
	?>
		<img src="/bundles/zcoforum/img/ferme.png" alt="Fermé" title="MP fermé" />
	<?php
		if(verifier('mp_repondre_mp_fermes'))
		{
			echo '</a>';
		}
	}
	else
	{
	?>
	<a href="repondre-<?php echo $_GET['id']; ?>.html"><img src="/bundles/zcoforum/img/repondre.png" alt="Répondre" title="Répondre au MP" /></a>
	<?php }
	echo '&nbsp;';
}
if($autoriser_ecrire AND ($MPTotal < verifier('mp_quota') OR verifier('mp_quota') == -1))
{
	echo '<a href="nouveau.html"><img src="/bundles/zcoforum/img/nouveau.png" alt="Nouveau" title="Nouveau MP" /></a>';
}
if($autoriser_ecrire AND ($NombreParticipants > 1 OR $MPTotal < verifier('mp_quota')))
{
	echo '</p>';
}

$ReponseRapide = '
<div id="reponse_rapide">
<form action="repondre-'.$_GET['id'].'.html" method="post">
	<fieldset id="rep_rapide">
		Réponse rapide :<br />
		<textarea name="texte" id="texte" tabindex="10" cols="40" rows="10" class="zcode_rep_rapide"></textarea>
		<br />
		<input type="hidden"
		       name="dernier_message"
		       value="'.$InfoMP['mp_dernier_message_id'].'"
		/>
		<input type="submit" name="send_reponse_rapide" value="Envoyer" tabindex="20" accesskey="s" /> <input type="submit" name="plus_options" value="Plus d\'options" tabindex="30" />
	</fieldset>
</form>
</div>';

if($NombreParticipants > 1 AND $autoriser_ecrire)
{
	if($InfoMP['mp_ferme'] AND verifier('mp_repondre_mp_fermes'))
	{
		echo $ReponseRapide;
	}
	elseif(!$InfoMP['mp_ferme'])
	{
		echo $ReponseRapide;
	}
}

if($autoriser_ecrire)
{
	include(__DIR__.'/_options_bas_mp.html.php');
}
