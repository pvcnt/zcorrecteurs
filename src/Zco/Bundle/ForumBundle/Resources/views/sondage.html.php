<?php $view->extend('::layouts/default.html.php') ?>

<?php
if($InfosSujet['sondage_ferme'])
{
	$th = '<span class="rouge">fermé</span>';
	$DejaVote = true;
}
else
{
	$th = '<span class="vertf">ouvert</span>';
}
?>
<table class="UI_items">
	<thead>
		<tr>
			<th>Sondage <em>(<?php echo $th; ?>)</em>
				<?php if(verifier('editer_sondages', $InfosSujet['sujet_forum_id'])){ ?>
					<a href="editer-sondage-<?php echo $InfosSujet['sujet_sondage'];?>-<?php echo rewrite($InfosSujet['sondage_question']);?>.html">
						<img src="/img/editer.png" alt="Modifier" title="Modifier le sondage" />
					</a>
				<?php } if(verifier('supprimer_sondages', $InfosSujet['sujet_forum_id'])) { ?>
					<a href="supprimer-sondage-<?php echo $InfosSujet['sujet_sondage'];?>.html" onclick="return confirm('Êtes-vous sûr ?');">
						<img src="/img/supprimer.png" alt="Supprimer" title="Supprimer le sondage" />
					</a>
				<?php } ?>
			</th>
		</tr>
	</thead>

	<tbody>
<?php
//Si on a déjà voté, on affiche les résultats.
if($DejaVote OR $InfosSujet['sujet_corbeille'])
{
?>
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
				<?php
				if(verifier('voir_votants', $InfosSujet['sujet_forum_id']))
				{
				?>
				<div id="persones_qui_ont_vote">
				</div>

				<p class="centre">
					<input type="button" name="xhr" id="xhr"
						onclick="afficher_votants(this, <?php echo $InfosSujet['sujet_forum_id']; ?>, <?php echo $InfosSujet['sujet_sondage']; ?>);"
						value="Voyons voir qui a voté…" />
				</p>
				<?php
				}
				?>
			</td>
		</tr>
<?php
}
else //Si on n'a pas encore voté, on affiche le formulaire pour voter.
{
?>
	<form action="voter.html" method="post">
		<input type="hidden" name="s" value="<?php echo $_GET['id']; ?>" />
		<input type="hidden" name="sondage" value="<?php echo $InfosSujet['sujet_sondage']; ?>" />
		<tr>
			<td>
			<p class="centre"><strong><?php echo htmlspecialchars($InfosSujet['sondage_question']); ?></strong></p>
			<p>
			<?php
			foreach($ListerResultatsSondage as $clef => $valeur)
			{
				if($valeur['choix_id'] != 0)
				{
			?>
				<label for="monchoix<?php echo $valeur['choix_id']; ?>" style="width:400px;"><?php echo htmlspecialchars($valeur['choix_texte']); ?></label> <input type="radio" name="choix" id="monchoix<?php echo $valeur['choix_id']; ?>" value="<?php echo $valeur['choix_id']; ?>" /><br />
			<?php
				}
			}
			?>
			</p>
			<p class="centre"><input type="submit" name="voter" value="Voter" /> <input type="submit" name="blanc" value="Blanc" /></p>
			</td>
		</tr>
	</form>
<?php
}
?>
	</tbody>
</table>
