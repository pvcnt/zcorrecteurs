<?php $view->extend('::layouts/default.html.php') ?>

<h1>Mes dictées</h1>

<p class="gras centre">
	<?php if(verifier('dictees_proposer')): ?>
	<a href="ajouter.html">Ajouter une dictée</a> -
	<?php endif; ?>
	<a href="statistiques.html">Mes statistiques</a>
</p>

<?php if(!$nb = count($Dictees)): ?>
<p>Il n'y a aucune dictée proposée.</p>
<?php else: ?>
<p>Vous avez <?php echo $nb; ?> dictée<?php echo pluriel($nb); ?> dans votre espace personnel.</p>

<table class="UI_items">
	<thead>
		<tr class="header_message">
			<th style="width: 24%;">Titre</th>
			<th style="width: 12%;">État</th>
			<th style="width: 10%;">Difficulté</th>
			<th style="width: 20%;">Création</th>
			<th style="width: 20%;">Modification</th>
			<th style="width: 14%;">Actions</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach($Dictees as $Dictee): ?>
		<tr>
			<td>
			<?php if ($Dictee->icone) :?>
				<img src="<?php echo htmlspecialchars($Dictee->icone); ?>" height="50" width="50"/>
			<?php endif; ?>
				<a href="dictee-<?php echo $Dictee->id.'-'.rewrite($Dictee->titre); ?>.html">
					<?php echo htmlspecialchars($Dictee->titre); ?>
				</a>
			</td>
			<td>
				<?php echo $DicteeEtats[$Dictee->etat]; ?>
			</td>
			<td>
			<?php echo str_repeat(
				'<img title="'.$DicteeDifficultes[$Dictee->difficulte].'"
				alt="'.$DicteeDifficultes[$Dictee->difficulte].'"
				src="/img/quiz/etoile.png" />',
				$Dictee->difficulte);
			?>
			</td>
			<td><?php echo dateformat($Dictee->creation); ?></td>
			<td><?php echo dateformat($Dictee->edition); ?></td>
			<td class="centre">
			<?php if(DicteeDroit($Dictee, 'editer')): ?>
				<a href="editer-<?php echo $Dictee->id.'-'.rewrite($Dictee->titre);
				?>.html" title="Modifier cette dictée">
					<img title="Éditer" alt="Éditer" class="fff pencil" src="/pix.gif"/>
				</a>
			<?php endif; if(DicteeDroit($Dictee, 'supprimer')): ?>
				<a href="supprimer-<?php echo $Dictee->id.'-'.rewrite($Dictee->titre);
				?>.html" title="Supprimer cette dictée">
					<img title="Supprimer" alt="Supprimer" class="fff cross" src="/pix.gif"/>
				</a>
			<?php endif; if($Dictee->etat == DICTEE_BROUILLON && verifier('dictees_proposer')): ?>
				<a href="proposer-<?php echo $Dictee->id; ?>.html" title="Proposer cette dictée">
					<img alt="Proposer" class="fff folder_go" src="/pix.gif"/>
				</a>
			<?php endif; if(verifier('dictees_publier')): ?>
				<?php if($Dictee->etat == DICTEE_VALIDEE): ?>
				<a href="valider-<?php echo $Dictee->id; ?>-0.html?token=<?php echo $_SESSION['token'] ?>" title="Dévalider">
					<img title="Dévalider" alt="Dévalider" class="fff forbidden" src="/pix.gif"/>
				</a>
				<?php else: ?>
				<a href="valider-<?php echo $Dictee->id; ?>-1.html?token=<?php echo $_SESSION['token'] ?>" title="Valider">
					<img alt="Valider" class="fff tick" src="/pix.gif"/>
				</a>
				<?php endif; ?>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
