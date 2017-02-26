<?php $view->extend('::layouts/default.html.php') ?>

<h1>Messages privés</h1>

<?php if($_SESSION['MPsnonLus'] > 0){ ?>
<p class="gras centre">
	Vous avez <?php echo $_SESSION['MPsnonLus'] ?> message<?php echo pluriel($_SESSION['MPsnonLus']) ?>
	non lu<?php echo pluriel($_SESSION['MPsnonLus']) ?> !
</p>
<?php } ?>

<fieldset>
	<p><a href="ajouter-dossier.html">Ajouter un dossier</a></p>

	<?php if (count($ListerDossiers) >= 8){ /* On met en colonnes à partir de 8 dossiers */ ?>
	<table class="wrapper" style="width: 98%;"><tr><td style="width: 33%;">
	<?php } ?><ul>
		<li><a href="index.html">
		<?php if($_GET['id'] === '') { ?><strong><?php } ?>Tous les dossiers
		<?php if($_GET['id'] === '') { ?></strong><?php } ?></a> (<?php echo $_SESSION['MPs'];?>)</li>
		<li><a href="index-0.html">
		<?php if($_GET['id'] === '0') { ?><strong><?php } ?>Accueil
		<?php if($_GET['id'] === '0') { ?></strong><?php } ?></a> (<?php echo $MPDansAccueil; ?>)</li>
		<?php
		if($ListerDossiers)
		{
			foreach($ListerDossiers as $i => $dossier)
			{
				if (count($ListerDossiers) >= 8 && ($i+2) % ceil((count($ListerDossiers)+2)/3) === 0)
					echo ($i > 0 ? '</ul></td>' : '').'<td style="width: 33%; vertical-align: top;"><ul>';

				echo '<li>';
				if($_GET['id'] == $dossier['mp_dossier_id'])
					echo '<strong>';
				echo '<a href="index-'.$dossier['mp_dossier_id'].'.html">'.$dossier['mp_dossier_titre'].'</a>';
				if($_GET['id'] == $dossier['mp_dossier_id'])
					echo '</strong>';
				echo ' ('.$dossier['nombre_dans_dossier'].') ';
				echo '<a href="renommer-dossier-'.$dossier['mp_dossier_id'].'.html"><img src="/img/editer.png" alt="Renommer" title="Renommer le dossier" /></a>
				<a href="supprimer-dossier-'.$dossier['mp_dossier_id'].'.html"><img src="/img/supprimer.png" alt="Supprimer" title="Supprimer le dossier" /></a></li>';
			}
		}
		?>
	</ul><?php if (count($ListerDossiers) >= 8){ ?></td></tr><?php } ?>
	</table>
</fieldset>

<p style="margin-top: 10px; margin-bottom: 10px; vertical-align: middle;">
<span style="float: right;">
	<?php if ($_SESSION['MPs'] < verifier('mp_quota') OR verifier('mp_quota') == -1){ ?>
	<a href="nouveau.html"><img src="/bundles/zcoforum/img/nouveau.png" alt="Nouveau" title="Nouveau MP" /></a>
	<?php } else{ ?>
	Vous avez atteint ou dépassé votre quota.
	<?php } ?>
</span>

<p>
	Vous avez <?php echo $_SESSION['MPs'] ?>/<?php echo (verifier('mp_quota') == -1) ? '(illimité)' : verifier('mp_quota') ?>
	messages.
</p>

<form method="post">
	<label class="gras" for="recherche_mp">Titre/sous-titre :</label>
	<input type="text" name="recherche_mp" id="recherche_mp" size="40" value="<?php if (isset($recherche)) echo htmlspecialchars($recherche) ?>" />
	<input type="submit" value="Rechercher" />
</form>


</p>

<?php if ($_SESSION['MPs'] > 0){ ?>
<form name="action_etendue" id="action_etendue" action="" method="post">
<table class="liste_cat" onclick="InverserEtat(event);" onmouseover="InverserEtat(event);" onmouseout="InverserEtat(event);">
<?php } else { ?>
<table class="liste_cat">
<?php } ?>
	<thead>
		<tr>
			<td colspan="7">Page : <?php echo ($ListePages = implode('', $ListePages)) ?></td>
		</tr>
		<tr>
			<th class="mp_colonne_flag"></th>
			<th class="mp_colonne_titre">Titre du message</th>
			<th class="mp_colonne_page">Pages</th>
			<th class="mp_colonne_participants centre">Participants</th>
			<th class="mp_colonne_reponses centre">Rép.</th>
			<th class="mp_colonne_dernier_msg centre" colspan="2">Dernier message</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="7">Page : <?php echo $ListePages ?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	if($ListerMP && $_SESSION['MPs'] > 0) //Si il y a au moins un MP à lister, on liste !
	{
		foreach($ListerMP as $MP)
		{
			?>
			<tr class="sous_cat">
				<td class="centre">
				<a href="/mp/
				<?php
				echo 'lire-'.$MP['mp_id'].'-'.$MP['mp_dernier_message_id'].'.html';
				?>
				">
				<img src="/bundles/zcoforum/img/<?php
					echo $MP['_lu']['image']; ?>" title="<?php
					echo $MP['_lu']['title']; ?>" alt="<?php
					echo $MP['_lu']['title']; ?>" /></a>
				<?php
				if($MP['mp_ferme'])
				{
				?>
				<img src="/bundles/zcoforum/img/cadenas.png"
				     alt="Fermé" title="MP fermé par un modérateur" />
				<?php
				}
				?>
				</td>
				<td title="MP commencé <?php echo dateformat($MP['mp_date'], MINUSCULE); ?>">
				<?php
				if($MP['_lu']['fleche'])
				{
					echo '<a href="/mp/lire-'.$MP['mp_id'].'-'.$MP['mp_lunonlu_actuel_dernier_message_lu'].'.html"><img src="/bundles/zcoforum/img/fleche.png" alt="Aller au dernier message lu" title="Aller au dernier message lu" /></a>';
				}
				?>
				<a href="/mp/<?php echo 'lire-'.$MP['mp_id']; ?>.html"><?php
					echo htmlspecialchars($MP['mp_titre']); ?></a>
				<?php if(!empty($MP['mp_sous_titre'])){ ?><br />
				<span class="sous_titre"><?php echo htmlspecialchars($MP['mp_sous_titre']); ?></span>
				<?php } ?>
				</td>

				<td class="centre">
					<?php
					$i = 0;
					foreach($MP['_pages'] as $element)
					{
						$i++;
						echo '<a href="/mp/lire-'.$MP['mp_id'].'-p'.substr($element, 0, 1).'.html">'.$element.'</a>';
						if($i == 3)
						{
							$i = 0;
							echo '<br />';
						}
					}
					?>
				</td>

				<td class="centre">
				<?php
				foreach($MP['_participants'] as $part)
				{
					?>
					<a href="/mp/lire-<?php echo $MP['mp_id'];
					if(!empty($part['mp_lunonlu_participant_message_id']))
						echo '-'.$part['mp_lunonlu_participant_message_id'];
					?>.html">
					<img src="/bundles/zcoforum/img/<?php echo $part['_lu']['image']; ?>"
					     title="Aller au dernier message lu du membre"
					     alt="Aller au dernier message lu du membre" /></a>
				<?php
					switch($part['mp_participant_statut'])
					{
						case MP_STATUT_MASTER:
							echo '<em>';
						break;
						case MP_STATUT_OWNER:
							echo '<strong>';
						break;
						case MP_STATUT_SUPPRIME:
							echo '<strike>';
						break;
					}
					echo '<a href="/membres/profil-'.$part['mp_participant_id'].'-'.rewrite($part['utilisateur_pseudo']).'.html"';
					if(!empty($part['groupe_class']))
					{
						echo ' style="color: '.$part['groupe_class'].';"';
					}
					echo '>';
					echo htmlspecialchars($part['utilisateur_pseudo']);
					echo '</a> ';
					switch($part['mp_participant_statut'])
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
				}
				?></td>

				<td class="centre"><?php echo $MP['mp_reponses']; ?></td>

				<td class="dernier_msg centre">
				<?php
				echo '<a href="/mp/lire-'.$MP['mp_id'].'-'.$MP['mp_dernier_message_id'].'.html">'.dateformat($MP['mp_dernier_message_date']).'</a><br />';
				echo '<a href="/membres/profil-'.$MP['mp_dernier_message_auteur'].'-'.rewrite($MP['mp_dernier_message_pseudo']).'.html"';
				if(!empty($MP['mp_dernier_message_auteur_groupe_class']))
				{
					echo ' style="color: '.$MP['mp_dernier_message_auteur_groupe_class'].';"';
				}
				echo '>'.htmlspecialchars($MP['mp_dernier_message_pseudo']).'</a>';
				?>
				</td>
				<td class="centre"><a href="supprimer-<?php echo $MP['mp_id']; ?>.html"><img src="/img/supprimer.png" alt="Supprimer" title="Supprimer le MP" /></a> <input type="checkbox" name="MP[<?php echo $MP['mp_id']; ?>]" id="MP[<?php echo $MP['mp_id']; ?>]" value="<?php echo $MP['mp_id']; ?>" onclick="ColorerLigneOncheck(event)"/></td>
			</tr>
			<?php
		}
	}
	else
	{
	?>
		<tr>
			<td colspan="7" class="centre">Il n'y a aucun MP à lister.</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
<?php
if($_SESSION['MPs'] > 0)
{
?>
<script type="text/javascript">
function changer_select(valeur)
{
	if(valeur == 'deplacer')
	{
		document.getElementById('deplacer_mp').style.display = 'inline';
	}
	else
	{
		document.getElementById('deplacer_mp').style.display = 'none';
	}
}
</script>
	<div class="btn_supprimer_msg">
		<input type="button" name="select_all" id="select_all" onclick="generator_switch_checkbox('action_etendue');" value="Tout sélectionner" />
		<select id="action" name="action" onchange="changer_select(this.value);">
		<optgroup label="Action étendue à plusieurs MP">
			<option value="">Choisissez une action…</option>
			<?php if(verifier('mp_fermer')) { ?>
			<option value="ouvrir">Ouvrir les MP</option>
			<option value="fermer">Fermer les MP</option>
			<?php } ?>
			<option value="lus">Marquer comme lus</option>
			<option value="nonlus">Marquer comme non lus</option>
			<?php
			if($ListerDossiers)
			{
				echo '<option value="deplacer">Déplacer dans un autre dossier</option>';
			}
			?>
			<option value="supprimer">Supprimer</option>
		</optgroup>
		</select>
		<?php
		if($ListerDossiers)
		{
		?>
		<div id="deplacer_mp" style="display:none;">
		<select name="deplacer_lieu">
		<option value="0">Accueil</option>
		<?php
		foreach($ListerDossiers as $MP)
		{
			echo '<option value="'.$MP['mp_dossier_id'].'">'.htmlspecialchars($MP['mp_dossier_titre']).'</option>';
		}
		?>
		</select>
		</div>
		<?php } ?>
		<input type="submit" name="send" value="Envoyer" accesskey="s" />
	</div>
</form>
<?php } ?>
<p class="reponse_ajout_sujet">
<?php
if($_SESSION['MPs'] < verifier('mp_quota') OR verifier('mp_quota') == -1)
{
?>

	<a href="nouveau.html"><img src="/bundles/zcoforum/img/nouveau.png" alt="Nouveau" title="Nouveau MP" /></a>
<?php
}
else
{
	echo 'Vous avez atteint ou dépassé votre quota.';
}
?>
</p>