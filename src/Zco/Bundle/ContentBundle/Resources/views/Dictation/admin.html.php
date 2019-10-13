<?php $view->extend('::layouts/bootstrap.html.php') ?>

<div class="float-right">
    <a href="<?php echo $view['router']->path('zco_dictation_new') ?>" class="btn btn-primary">
        <i class="icon-plus-sign icon-white"></i>
        Ajouter une dictée
    </a>
</div>

<h1>Gestion des dictées</h1>

<?php if(!$nb = count($Dictees)): ?>
<p>Il n'y a aucune dictée en cours de préparation.</p>
<?php else: ?>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Titre</th>
			<th>État</th>
			<th>Difficulté</th>
			<th>Création</th>
			<th>Modification</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach($Dictees as $Dictee): ?>
		<tr>
			<td>
			<?php if ($Dictee->icone) :?>
				<img src="<?php echo htmlspecialchars($Dictee->icone); ?>" height="50" width="50"/>
			<?php endif; ?>
				<a href="<?php echo $view['router']->path('zco_dictation_show', ['id' => $Dictee->id, 'slug' => rewrite($Dictee->titre)]) ?>">
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
				src="/bundles/zcoquiz/img/etoile.png" />',
				$Dictee->difficulte);
			?>
			</td>
			<td><?php echo dateformat($Dictee->creation); ?></td>
			<td><?php echo dateformat($Dictee->edition); ?></td>
			<td class="center">
                <a href="<?php echo $view['router']->path('zco_dictation_edit', ['id' => $Dictee->id]) ?>" title="Modifier cette dictée"
                ><img title="Éditer" alt="Éditer" class="fff pencil" src="/pix.gif"/></a>
				<a href="<?php echo $view['router']->path('zco_dictation_delete', ['id' => $Dictee->id]) ?>" title="Supprimer cette dictée"
                ><img title="Supprimer" alt="Supprimer" class="fff cross" src="/pix.gif"/></a>
				<?php if($Dictee->etat == DICTEE_VALIDEE): ?>
				<a href="<?php echo $view['router']->path('zco_dictation_publish', ['id' => $Dictee->id, 'status' => 0, 'token' => $_SESSION['token']]) ?>" title="Dévalider"
                ><img title="Dévalider" alt="Dévalider" class="fff forbidden" src="/pix.gif"/></a>
				<?php else: ?>
				<a href="<?php echo $view['router']->path('zco_dictation_publish', ['id' => $Dictee->id, 'status' => 1, 'token' => $_SESSION['token']]) ?>" title="Valider"
                ><img alt="Valider" class="fff tick" src="/pix.gif"/></a>
				<?php endif; ?>
            </td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
