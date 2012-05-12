<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoRecrutementBundle::_onglets.html.php') ?>

<p class="good">
	Nous nous appuyons uniquement sur des bénévoles pour faire vivre 
	zCorrecteurs.fr et l’association Corrigraphie. Afin d’assurer la pérennité 
	de notre action, nous sommes en permanence à la recherche de personnes prêtes 
	à donner de leur temps pour nous aider à poursuivre nos activités.
</p>

<?php if (count($recrutements) > 0): ?>
<table class="table table-bordered table-small">
	<tbody>
		<?php $etats = array(\Recrutement::FINI => 'terminés', \Recrutement::OUVERT => 'en cours');
		$etat = 0;
		foreach ($recrutements as $recrutement): ?>
			<?php if ($etat != $recrutement['etat']): ?>
				<?php $etat = $recrutement['etat'] ?>
				<tr>
					<th style="width: 70%;">Recrutements <?php echo $etats[$recrutement['etat']] ?></th>
					<th style="width: 30%;">Fin de dépôt des candidatures</th>
				</tr>
			<?php endif; ?>
			<tr>
				<td>
					<a href="recrutement-<?php echo $recrutement['id']; ?>-<?php echo rewrite($recrutement['nom']); ?>.html">
						<?php echo htmlspecialchars($recrutement['nom']); ?>
					</a>
				</td>
				<td class="centre">
					<?php echo dateformat($recrutement['date_fin_depot']); ?>
				</td>
			</tr>
			<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
