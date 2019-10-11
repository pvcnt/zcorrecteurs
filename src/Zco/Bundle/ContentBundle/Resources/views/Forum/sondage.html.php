<?php $view->extend('::layouts/default.html.php') ?>

<table class="UI_items">
	<thead>
		<tr>
			<th>Sondage</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>
				<p class="centre"><strong><?php echo htmlspecialchars($InfosSujet['sondage_question']); ?></strong> <em>(<?php echo $nombre_total_votes; ?> vote<?php echo pluriel($nombre_total_votes); ?>)</em></p>
				<dl>
				<?php
				foreach($ListerResultatsSondage as $clef => $valeur)
				{
					//Calcul du pourcentage de chaque choix
					@$pourcentage = round(($valeur['nombre_votes'] / $nombre_total_votes) * 100 , 2);

					//Calcul de la taille de barre de pourcentage de chaque choix
					$taille_barre = (int)(($pourcentage * 400) / 100);
				?>
					<dt><?php echo htmlspecialchars($valeur['choix_texte']); ?> (<?php echo $valeur['nombre_votes']; ?> vote<?php echo pluriel($valeur['nombre_votes']); ?>)</dt>
					<dd>
					<img src="/bundles/zcosondages/img/barre_gauche.png" alt="" /><img src="/bundles/zcosondages/img/barre_centre.png" alt="" style="width:<?php echo $taille_barre; ?>px; height:8px;" /><img src="/bundles/zcosondages/img/barre_droite.png" alt="" /> <?php echo $pourcentage; ?> %
					</dd>
				<?php
				}
				?>
				</dl>
			</td>
		</tr>
	</tbody>
</table>
