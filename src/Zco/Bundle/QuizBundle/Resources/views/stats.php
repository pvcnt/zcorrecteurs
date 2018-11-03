<?php $view->extend('::layouts/default.html.php') ?>
<?php $listeMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre') ?>

<?php if (!isset($quiz)){ ?>
<h1>Statistiques générales d'utilisation des quiz</h1>
<?php } else{ ?>
<h1>Statistiques d'utilisation du quiz « <?php echo htmlspecialchars($quiz['nom']) ?> »</h1>
<?php } ?>

<form method="get" action="statistiques.html">
	<fieldset>
	<legend>Voir les statistiques d'un quiz</legend>
	<label for="id">Quiz : </label>
		<select name="id" id="id">
		<option value="0">Statistiques générales</option>
		<?php
		$current = null;
		foreach($listeQuiz as $cle => $qquiz)
		{
			if ($qquiz['categorie_id'] != $current)
			{
				$current = $qquiz['categorie_id'];
				if ($cle > 0)
				{
					echo '</optgroup>';
				}
				echo '<optgroup label="'.htmlspecialchars($qquiz->Categorie['nom']).'">';
			}
			echo '<option value="'.$qquiz['id'].'"'.($qquiz['id'] === $quiz['id'] ? ' selected="selected"' : '').'>'.htmlspecialchars($qquiz['nom']).'</option>';
		}
		echo '</optgroup>';
		?>
		</select>
		<input type="submit" value="Consulter les statistiques" />
		<a href="statistiques-popularite.html" class="bold" style="margin-left: 30px;">Voir les statistiques de popularité</a>
	</fieldset>
</form><br />


<div class="center">
	<?php if (isset($_GET['notes']) && $_GET['notes']){ ?>
	<p class="bold center"><a href="?notes=0">Voir les statistiques d'utilisation</a></p>

	<p>
		Le graphique de répartition des notes ne tient pas compte de la période
		sélectionnée et affiche des statistiques globales depuis le début. Pour
		des informations par période vous pouvez vous référer au tableau de
		valeurs en-dessous.
	</p><br />

	<img
		alt="Graphique de répartition des notes"
		src="graphique-notes.html<?php if (isset($quiz)){ ?>?quiz=<?php echo $quiz['id'] ?><?php } ?>"
	/>
	<?php } elseif (!isset($jour)){ ?>
	<p class="bold center"><a href="?notes=1">Voir le graphique de répartition des notes</a></p><br />
	<img
		alt="Graphique de statistiques"
		src="graphique-statistiques.html<?php if (isset($quiz)){ ?>?quiz=<?php echo $quiz['id'] ?><?php } ?>"
	/><br /><br />

	<img
		alt="Graphique de statistiques"
		src="graphique-statistiques.html?annee=<?php echo $annee ?><?php if (isset($quiz)){ ?>&quiz=<?php echo $quiz['id'] ?><?php } ?>"
	/><br /><br />

	<img
		alt="Graphique de statistiques"
		src="graphique-statistiques.html?annee=<?php echo $annee ?>&mois=<?php echo $mois ?><?php if (isset($quiz)){ ?>&quiz=<?php echo $quiz['id'] ?><?php } ?>"
	/>
	<?php } else{ ?>
	<img
		alt="Graphique de statistiques"
		src="graphique-statistiques.html?annee=<?php echo $annee ?>&mois=<?php echo $mois ?>&jour=<?php echo $jour ?><?php if (isset($quiz)){ ?>&quiz=<?php echo $quiz['id'] ?><?php } ?>"
	/>
	<?php } ?>
</div>

<table class="UI_items">
	<thead>
		<tr>
			<td colspan="5" class="center">
				<form method="get" action="">
					<?php if (isset($jour)){ ?>
					<a class="bold" href="?annee=<?php echo $annee ?>&mois=<?php echo $mois ?><?php if (isset($quiz)){ ?>&id=<?php echo $quiz['id'] ?><?php } ?>">
						Retour aux statistiques de <?php echo lcfirst($listeMois[$mois-1]) ?> <?php echo $annee ?>
					</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
					<input type="hidden" name="jour" value="<?php echo $jour ?>" />
					<?php } if (isset($quiz)){ ?>
					<input type="hidden" name="quiz" value="<?php echo $quiz['id'] ?>" />
					<?php } ?>

					<a href="?annee=<?php echo $annee_precedent ?>&mois=<?php echo $mois_precedent ?><?php if (isset($quiz)){ ?>&id=<?php echo $quiz['id'] ?><?php } ?>">
						« Précédent
					</a>

					<select name="mois">
						<?php foreach ($listeMois as $i => $nomMois){ ?>
						<option value="<?php echo $i + 1 ?>"<?php if ($i + 1 === $mois) echo ' selected="selected"' ?>>
							<?php echo $nomMois ?>
						</option>
						<?php } ?>
					</select>
					<select name="annee">
						<?php for ($i = 2007 ; $i <= date('Y') ; $i ++){ ?>
						<option value="<?php echo $i ?>"<?php if ($i === $annee) echo ' selected="selected"' ?>>
							<?php echo $i ?>
						</option>
						<?php } ?>
					</select>

					<input type="submit" value="Aller" />

					<a href="?annee=<?php echo $annee_suivant ?>&mois=<?php echo $mois_suivant ?><?php if (isset($quiz)){ ?>&id=<?php echo $quiz['id'] ?><?php } ?>">
						Suivant »
					</a>
				</form>
			</td>
		</tr>
		<tr>
			<th><?php echo isset($jour) ? 'Heure' : 'Jour' ?></th>
			<th>Validations par des membres</th>
			<th>Validations par des visiteurs</th>
			<th>Validations totales</th>
			<th>Note moyenne</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td class="bold center">Totaux <?php if (!isset($jour)) echo 'sur le mois' ?> :</td>
			<td class="bold center"><?php echo $view['humanize']->numberformat($donnees['totaux']['validations_membres'], 0) ?></td>
			<td class="bold center"><?php echo $view['humanize']->numberformat($donnees['totaux']['validations_visiteurs'], 0) ?></td>
			<td class="bold center"><?php echo $view['humanize']->numberformat($donnees['totaux']['validations_totales'], 0) ?></td>
			<td class="bold center">
				<?php if ($donnees['totaux']['validations_totales'] > 0){ ?>
				<?php echo $view['humanize']->numberformat($donnees['totaux']['note_moyenne']) ?>/20
				<?php } else echo '-' ?>
			</td>
		</tr>

		<?php if (!isset($jour)){ ?>
		<tr>
			<td style="background-color: #e0e0e0;" class="bold center">Totaux globaux :</td>
			<td style="background-color: #e0e0e0;" class="bold center"><?php echo $view['humanize']->numberformat($validationsMembres, 0) ?></td>
			<td style="background-color: #e0e0e0;" class="bold center"><?php echo $view['humanize']->numberformat($validationsVisiteurs, 0) ?></td>
			<td style="background-color: #e0e0e0;" class="bold center"><?php echo $view['humanize']->numberformat($validationsTotales, 0) ?></td>
			<td style="background-color: #e0e0e0;" class="bold center">
				<?php if ($validationsTotales > 0){ ?>
				<?php echo $view['humanize']->numberformat($noteMoyenne) ?>/20
				<?php } else echo '-' ?>
			</td>
		</tr>
		<?php } ?>
	</tfoot>

	<tbody>
		<?php $i = 0; foreach ($donnees['lignes'] as $cle => $ligne){ ?>
		<tr class="<?php echo $i % 2 ? 'odd' : 'even' ?>">
			<td class="center">
				<?php if (!isset($jour)){ ?>
					<a href="?annee=<?php echo $annee ?>&mois=<?php echo $mois ?>&jour=<?php echo $cle ?><?php if (isset($quiz)){ ?>&id=<?php echo $quiz['id'] ?><?php } ?>">
						<?php echo $cle ?>
					</a>
				<?php } else echo $cle ?>
			</td>
			<td class="center"><?php echo $view['humanize']->numberformat($ligne['validations_membres'], 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($ligne['validations_visiteurs'], 0) ?></td>
			<td class="center"><?php echo $view['humanize']->numberformat($ligne['validations_totales'], 0) ?></td>
			<td class="center">
				<?php if ($ligne['validations_totales'] > 0){ ?>
				<?php echo $view['humanize']->numberformat($ligne['note_moyenne']) ?>/20
				<?php } else echo '-' ?>
			</td>
		</tr>
		<?php $i++; } ?>
	</tbody>
</table>
