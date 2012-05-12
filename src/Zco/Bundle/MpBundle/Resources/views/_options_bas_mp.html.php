<fieldset>
	<legend>Contrôles</legend>
	<ul>
		<?php
		//DÉBUT alerter les modos
		if(verifier('mp_signaler'))
		{
		?>
			<li><span><img src="/bundles/zcomp/img/alerter.png" alt="Alerter" title="Alerter les modérateurs" /></span>
			<a href="alerter-<?php echo $_GET['id']; ?>.html">Alerter les modérateurs</a> (l'équipe de modération pourra alors lire le MP).</li>
		<?php
		}
		//FIN alerter les modos

		//DÉBUT fermer/ouvrir MP
		if(verifier('mp_fermer'))
		{
			if($InfoMP['mp_ferme'])
			{
			?>
			<li><span><img src="/bundles/zcoforum/img/cadenas_ouvert.png" alt="Ouvrir" title="Ouvrir le MP" /></span>
			<a href="<?php echo 'changer-statut-'.$_GET['id']; ?>-0.html">Ouvrir le MP</a></li>
			<?php
			}
			else
			{
			?>
			<li><span><img src="/bundles/zcoforum/img/cadenas_ferme.png" alt="Fermer" title="Fermer le MP" /></span>
			<a href="<?php echo 'changer-statut-'.$_GET['id']; ?>-1.html">Fermer le MP</a></li>
			<?php
			}
		}
		//FIN fermer/ouvrir MP

		//DÉBUT marquer comme non-lu
		if(!empty($InfoMP['mp_participant_mp_id']))
		{
		?>
			<li><span><img src="/bundles/zcomp/img/nouveau_msg.gif" alt="Non-lu" title="Marquer ce MP comme non-lu" /></span>
			<a href="<?php echo 'marquer-non-lu-'.$_GET['id']; ?>.html">Marquer le MP comme non-lu</a></li>
		<?php
		}
		//FIN marquer comme non-lu

		//DÉBUT déplacer sujet
		if(!empty($InfoMP['mp_participant_mp_id']))
		{
		if($ListerDossiers)
		{
		?>
		<form action="" method="post">
		<li>
			<span><img src="/bundles/zcoforum/img/deplace.png" alt="Déplacer" title="Déplacer le MP dans un autre dossier" /></span> Déplacer le MP vers :
		<select name="deplacer_lieu">
		<?php
		if($InfoMP['mp_participant_mp_dossier_id'] != 0)
		{
		?>
			<option value="0">Accueil</option>
		<?php
		}
		foreach($ListerDossiers as $valeur)
		{
			if($InfoMP['mp_participant_mp_dossier_id'] != $valeur['mp_dossier_id'])
			{
				echo '<option value="'.$valeur['mp_dossier_id'].'">'.htmlspecialchars($valeur['mp_dossier_titre']).'</option>';
			}
		}
		?>
		</select>
		<input type="submit" value="Déplacer" />
		</li>
		</form>
		<?php
		}
		}
		//FIN déplacer sujet

		if(!empty($InfoMP['mp_participant_mp_id']))
		{
		//DÉBUT supprimer MP
			?>
			<li><span><img src="/img/supprimer.png" alt="Supprimer" title="Supprimer le MP" /></span>
			<a href="<?php echo 'supprimer-'.$_GET['id']; ?>.html">Supprimer le MP</a></li>
			<?php
		//FIN supprimer MP
		}
		?>
	</ul>
</fieldset>
