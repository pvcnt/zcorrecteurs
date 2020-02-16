<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des dictées proposées</h1>

<p>
	Dans le tableau ci-dessous se trouvent les dictées
	<strong>proposées</strong>. Vous pouvez alors choisir de les valider, ou de
	les refuser en donnant un petit mot d'explication.
</p>
<?php if($nb = count($Dictees)): ?>
<p>Il y a <?php echo $nb; ?> dictée<?php echo pluriel($nb); ?> proposée<?php echo pluriel($nb); ?>.</p>
<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 30%;">Titre</th>
			<th style="width: 10%;">Auteur</th>
			<th style="width: 10%;">Difficulté</th>
			<th style="width: 15%;">Création</th>
			<th style="width: 14%;">Modification</th>
			<?php if(verifier('dictees_editer_toutes')): ?>
				<th style="width: 7%;">Modifier</th>
			<?php endif; if(verifier('dictees_publier')): ?>
				<th style="width: 7%;">Répondre</th>
			<?php endif; ?>
		</tr>
	</thead>

	<tbody>
	<?php foreach($Dictees as $Dictee): ?>
		<tr>
			<td>
			<?php if ($Dictee->icone) :?>
				<img src="<?php echo $this['messages']->blobUrl($Dictee->icone); ?>" height="50" width="50"/>
			<?php endif; ?>
				<a href="dictee-<?php echo $Dictee->id.'-'.rewrite($Dictee->titre); ?>.html">
					<?php echo htmlspecialchars($Dictee->titre); ?>
				</a>
			</td>
			<td>
				<a href="/membres/profil-<?php echo $Dictee->utilisateur_id.'-'
					.rewrite($Dictee->Utilisateur->pseudo); ?>.html">
					<?php echo htmlspecialchars($Dictee->Utilisateur->pseudo); ?>
				</a>
			</td>
			<td>
			<?php echo str_repeat(
				'<img title="'.$DicteeDifficultes[$Dictee->difficulte].'"
				alt="'.$DicteeDifficultes[$Dictee->difficulte].'"
				src="/bundles/zcoquiz/img/etoile.png" />',
				$Dictee->difficulte);
			?>
			</td>
			<td><?php echo dateformat($Dictee->creation); ?></td>
			<td><?php echo dateformat($Dictee->edition); ?></td>
			<?php if(verifier('dictees_editer_toutes')): ?>
			<td class="centre">
				<a href="editer-<?php echo $Dictee->id.'-'.rewrite($Dictee->titre);
				?>.html" title="Modifier cette dictée">
					<img title="Éditer" alt="Éditer" class="fff pencil" src="/pix.gif"/>
				</a>
			</td>
			<?php endif; if(verifier('dictees_publier')): ?>
			<td class="centre">
				<a href="repondre-<?php echo $Dictee->id; ?>.html" title="Répondre à cette proposition">
					<img alt="Valider" class="fff tick" src="/pix.gif"/>&nbsp;/&nbsp;
					<img alt="Refuser" class="fff forbidden" src="/pix.gif"/>
				</a>
			</td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>Il n'y a aucune dictée proposée.</p>
<?php endif; ?>
