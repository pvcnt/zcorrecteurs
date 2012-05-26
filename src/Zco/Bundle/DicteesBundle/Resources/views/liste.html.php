<?php $view->extend('::layouts/default.html.php') ?>

<?php
function lienOrdre($col, $tri)
{
	echo '<a href="liste'.
		($_GET['p'] == 1 ? '' : '-p'.$_GET['p'])
		.'.html?tri='.($tri == $col ? '-' : '').$col.'">';
}
function flecheOrdre($col, $tri)
{
	if($tri == $col)
		echo '&nbsp;<img src="/bundles/zcocore/img/generator/arrow-up.gif" alt="Tri croissant" />';
	elseif($tri == '-'.$col)
		echo '&nbsp;<img src="/bundles/zcocore/img/generator/arrow-down.gif" alt="Tri décroissant" />';
}
?>

<h1>Liste des dictées</h1>

<p>
	Dans le tableau ci-dessous se trouvent les dictées
	en ligne.<br/>
	En cliquant sur les titres des colonnes, vous pouvez
	trier les dictées selon le critère choisi, par ordre croissant ou
	décroissant.
</p>
<?php if($pager->countAll()): ?>
<table class="UI_items">
	<thead>
		<tr>
			<td colspan="8">
				Page : <?php echo $view['ui']->render($pager) ?>
			</td>
		</tr>
		<tr class="header_message">
			<th><?php echo lienOrdre('titre', $tri) ?>Titre<?php echo flecheOrdre('titre', $tri) ?></a></th>
			<th style="width: 20%;">Source</th>
			<th style="width: 15%;">Auteur</th>
			<th style="width: 7%;"><?php echo lienOrdre('temps_estime', $tri) ?>Durée<?php echo flecheOrdre('temps_estime', $tri) ?></a></th>
			<th style="width: 8%;"><?php echo lienOrdre('difficulte', $tri) ?>Difficulté<?php echo flecheOrdre('difficulte', $tri) ?></a></th>
			<th style="width: 8%;"><?php echo lienOrdre('participations', $tri) ?>Participations<?php echo flecheOrdre('participations', $tri) ?></a></th>
			<th style="width: 15%;"><?php echo lienOrdre('creation', $tri) ?>Création<?php echo flecheOrdre('creation', $tri) ?></a></th>
		</tr>
	</thead>

	<tbody>
	<?php foreach($pager as $Dictee): ?>
		<tr>
			<td <?php if (!$Dictee->icone) echo 'style="text-indent:55px; height:50px; vertical-align:middle;"'; ?>>
			<?php if ($Dictee->icone) :?>
				<img src="<?php echo htmlspecialchars($Dictee->icone); ?>" height="50" width="50"/>
			<?php endif; ?>
				<a href="dictee-<?php echo $Dictee->id.'-'.rewrite($Dictee->titre); ?>.html">
					<?php echo htmlspecialchars($Dictee->titre); ?>
				</a>
			</td>
			<td>
				<?php echo htmlspecialchars($Dictee->source) ?>
			</td>
			<td>
				<?php if($Dictee->Auteur): ?>
					<a href="/auteurs/auteur-<?php echo $Dictee->Auteur->id.'-'.rewrite($Dictee->Auteur) ?>.html">
						<?php echo htmlspecialchars($Dictee->Auteur) ?></a>
				<?php endif ?>
			</td>
			<td><?php echo $Dictee->temps_estime ?> min</td>
			<td>
			<?php echo str_repeat(
				'<img title="'.$DicteeDifficultes[$Dictee->difficulte].'"
				alt="'.$DicteeDifficultes[$Dictee->difficulte].'"
				src="/bundles/zcoquiz/img/etoile.png" />',
				$Dictee->difficulte);
			?>
			</td>
			<td class="centre"><?php echo $Dictee->participations ?></td>
			<td><?php echo dateformat($Dictee->creation); ?></td>
		</tr>
	<?php endforeach; ?>
		<tr>
			<td colspan="8" class="centre gras">
				<?php echo $pager->countAll() ?> dictée<?php echo pluriel($pager->countAll()) ?>
			</td>
		</tr>
	</tbody>
</table>

<?php else: ?>
<p>Il n'y a aucune dictée proposée.</p>
<?php endif; ?>
