<?php $view->extend('::layouts/default.html.php') ?>

<h1>Voir la liste des alertes</h1>

<p>Vous êtes en train de visualiser la liste des alertes des Messages Privés.</p>

<p>Voir les alertes :</p>
<ul>
	<li><?php if(isset($_GET['solved']) AND !$_GET['solved']){echo '<strong>';}?>
	<a href="alertes.html?solved=0">Non résolues</a>
	<?php if(isset($_GET['solved']) AND !$_GET['solved']){echo '</strong>';}?></li>

	<li><?php if(isset($_GET['solved']) AND $_GET['solved']){echo '<strong>';}?>
	<a href="alertes.html?solved=1">Résolues</a>
	<?php if(isset($_GET['solved']) AND $_GET['solved']){echo '</strong>';}?></li>

	<li><?php if(!isset($_GET['solved'])){echo '<strong>';}?><a href="alertes.html">Toutes</a>
	<?php if(!isset($_GET['solved'])){echo '</strong>';}?></li>
</ul>

<p class="rmq attention">La résolution d'une alerte est <strong>irréversible</strong>, réfléchissez bien avant. Le MP se refermera et vous n'y aurez plus accès.</p>

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
			<td colspan="7">Page :
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
if($ListerAlertes)
{
	foreach($ListerAlertes as $valeur)
	{
?>
		<tr class="header_message">
			<td class="pseudo_membre">
			<a href="/membres/profil-<?php echo $valeur['utilisateur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html" style="color: <?php echo $valeur['groupe_class']; ?>;">
			<?php echo htmlspecialchars($valeur['utilisateur_pseudo']); ?>
			</a>
			</td>
			<td class="dates">
				<span id="a<?php echo $valeur['mp_alerte_id'];?>"><a href="alertes-<?php echo $valeur['mp_alerte_id'];?>.html" rel="nofollow">#</a></span>
				Postée <?php echo dateformat($valeur['mp_alerte_date']); ?>

				<?php if( ($_SESSION['MPs'] < verifier('mp_quota') OR verifier('mp_quota') == -1) AND $_SESSION['id'] != $valeur['utilisateur_id']) { ?>
				<a href="nouveau-<?php echo $valeur['utilisateur_id']; ?>.html"><img src="/bundles/zcoforum/img/envoyer_mp.png" alt="MP" title="Envoyer un message privé" /></a>
				<?php }
				/*
				if($valeur['mp_alerte_resolu'])
				{
				?>
				<a href="?nonresolu=<?php echo $valeur['mp_alerte_id']; ?>"><img src="/bundles/zcoforum/img/resolu.png" alt="Plus résolu" title="Marquer cette alerte comme non-résolue" /></a>
				<?php }
				*/
				//else
				if(!$valeur['mp_alerte_resolu'])
				{
				?>
				<a href="?resolu=<?php echo $valeur['mp_alerte_id']; ?>" onclick="if(confirm('Étes-vous sûr de vouloir résoudre cette alerte ?\nVous n\'aurez alors plus accès au MP.\nNotez que la résolution d\'une alerte est irréversible.')) { return true; } else { return false; }"><img src="/bundles/zcoforum/img/resolu.png" alt="Résoudre" title="Marquer cette alerte comme résolue" /></a>
				<?php
				} ?>
				 - MP concerné : <?php echo '"<strong><a href="lire-'.$valeur['mp_id'].'.html">'.$valeur['mp_titre'].'</a></strong>"'; ?>
			</td>
		</tr>
		<tr>
			<td class="infos_membre">
			<?php
				if(!empty($valeur['utilisateur_citation'])){ echo $valeur['utilisateur_citation'] . '<br />'; }
				if(!empty($valeur['utilisateur_avatar']))
				{
				?>
				<a href="/membres/profil-<?php echo $valeur['utilisateur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html">
                    <?php echo $view['messages']->afficherAvatar($valeur) ?>
                </a><br />
				<?php echo $view->get('messages')->afficherGroupe($valeur) ?><br/>
			<?php if(verifier('membres_avertir'))
			{
			?>
			<br /><a href="<?php echo $view['router']->generate('zco_user_admin_warn', array('id' => htmlspecialchars($valeur['utilisateur_id']))) ?>">
				Avertir : <?php echo $valeur['utilisateur_pourcentage']; ?> %
			</a>
			<?php
			}
			elseif(verifier('membres_voir_avertos') AND $valeur['utilisateur_pourcentage'] > 0){
			?>
			<br /><a href="/membres/profil-<?php echo $valeur['utilisateur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html#avertos">Averto(s) : <?php echo $valeur['utilisateur_pourcentage']; ?> %</a>
			<?php
			}
			if(verifier('sanctionner'))
			{
			?>
			<br /><a href="<?php echo $view['router']->generate('zco_user_admin_punish', array('id' => htmlspecialchars($valeur['utilisateur_id']))) ?>">
				Sanctionner (<?php echo $valeur['utilisateur_nb_sanctions']; ?>)
			</a>
			<?php
			}
			elseif(verifier('voir_sanctions') && $valeur['utilisateur_nb_sanctions'] > 0){
			?>
			<br /><a href="/membres/profil-<?php echo $valeur['utilisateur_id']; ?>-<?php echo rewrite($valeur['utilisateur_pseudo']); ?>.html#sanctions">Sanction(s) : <?php echo $valeur['utilisateur_nb_sanctions']; ?></a>
			<?php
			}
			if(verifier('ips_analyser') && !empty($valeur['mp_alerte_ip']))
			{
				echo '<br /><br />IP : <a href="/ips/analyser.html?ip='.long2ip($valeur['mp_alerte_ip']).'">'.long2ip($valeur['mp_alerte_ip']).'</a>';
			}
				if($valeur['mp_alerte_resolu'])
				{
					echo '<br /><br />État : <strong style="color: #009900;">résolue</strong>';
					echo '<br />Réglée par ';
					if(!empty($valeur['modo_id']))
					{
						echo '<a href="/membres/profil-'.$valeur['modo_id'].'-'.rewrite($valeur['modo_pseudo']).'.html"';
						if(!empty($valeur['modo_groupe_class']))
						{
							echo ' style="color:'.$valeur['modo_groupe_class'].';"';
						}
						echo '>';
					}
					echo htmlspecialchars($valeur['modo_pseudo']);
					if(!empty($valeur['modo_id']))
					{
						echo '</a>';
					}
				}
				else
				{
					echo '<br /><br />État : <strong style="color: #ff0000;">non-résolue</strong>';
				}
			?>
			</td>
			<td class="message">
				<div class="msgbox">
					<?php
					//Affichage de la raison
					echo $view['messages']->parse($valeur['mp_alerte_raison']);
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
}
else
{
?>
	<tr><td class="centre" colspan="2">Il n'y a aucune alerte à afficher.</td></tr>
<?php } ?>
	</tbody>
</table>
