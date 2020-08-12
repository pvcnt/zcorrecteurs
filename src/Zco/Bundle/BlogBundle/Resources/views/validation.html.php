<?php $view->extend('::layouts/default.html.php') ?>

<h1>Historique de validation</h1>

<p>
	Voici l'historique de validation de ce billet. Il comprend les demandes de
	validation, ainsi que les réponses de l'administrateur.
</p>

<?php if($InfosBillet['blog_etat'] == BLOG_PROPOSE && verifier('blog_valider')){ ?>
<p class="gras centre">
	<a href="repondre-<?php echo $_GET['id']; ?>.html">Répondre à la proposition</a>
</p>
<?php } ?>

<?php if($Historique){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 25%;">Pseudo</th>
			<th style="width: 25%;">Date</th>
			<th style="width: 20%;">Version concernée</th>
			<th style="width: 20%;">Décision</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($Historique as $h){ ?>
		<tr>
			<td>
				<a href="/membres/profil-<?php echo $h['utilisateur_id']; ?>-<?php echo rewrite($h['utilisateur_pseudo']); ?>.html">
					<?php echo htmlspecialchars($h['utilisateur_pseudo']); ?>
				</a>
			</td>
			<td class="centre">
				<?php echo dateformat($h['valid_date']); ?>
			</td>
			<td class="centre">
				N<sup>o</sup>&nbsp;<?php echo $h['valid_id_version']; ?> -
				<a href="billet-<?php echo $_GET['id']; ?>.html?version=<?php echo $h['valid_id_version']; ?>">Voir</a>
			</td>
			<td class="centre">
				<?php if($h['valid_decision'] == DECISION_VALIDER) echo '<span class="vertf">Validé</span>';
				elseif($h['valid_decision'] == DECISION_REFUSER) echo '<span class="rouge">Refusé</span>';
				elseif($h['valid_decision'] == DECISION_NONE) echo '(proposition)'; ?>
			</td>
		</tr>
		<tr>
			<td colspan="<?php echo verifier('ips_analyser') ? 5 : 4; ?>">
				<strong>Commentaire : </strong><br />
				<?php if($h['valid_decision'] == DECISION_NONE) echo $view['messages']->parse($h['valid_commentaire']);
				else echo nl2br(htmlspecialchars($h['valid_commentaire'])); ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php } else{ ?>
<p>Ce billet n'a jamais été envoyé à la validation.</p>
<?php } ?>
