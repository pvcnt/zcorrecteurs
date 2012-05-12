<?php $view->extend('::layouts/default.html.php') ?>

<h1>Gérer les messages automatiques</h1>

<p>Voici la liste des messages automatiques du site. Vous pouvez en éditer ou en supprimer si vous le souhaitez.</p>

<p class="gras centre"><a href="ajouter-message-auto.html">Ajouter un message automatique</a></p>

<?php if(count($ListerMessages) > 0){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 40%;">Nom</th>
			<th style="width: 15%;">Tag</th>
			<th style="width: 10%;">Ferme le sujet</th>
			<th style="width: 15%;">Met le sujet en résolu</th>
			<th style="width: 10%;">Editer</th>
			<th style="width: 10%;">Supprimer</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($ListerMessages as $message){ ?>
		<tr>
			<td><?php echo htmlspecialchars($message['nom']); ?></td>
			<td class="centre"><?php echo htmlspecialchars($message['tag']); ?></td>
			<td class="centre"><?php echo $message['ferme'] ? 'oui' : 'non'; ?></td>
			<td class="centre"><?php echo $message['resolu'] ? 'oui' : 'non'; ?></td>
			<td class="centre">
				<a href="editer-message-auto-<?php echo $message['id']; ?>.html">
					<img src="/img/editer.png" alt="Éditer" />
				</a>
			</td>
			<td class="centre">
				<a href="?supprimer=<?php echo $message['id']; ?>">
					<img src="/img/supprimer.png" alt="Supprimer" />
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php } else{ ?>
<p>Il n'y a aucun message automatique.</p>
<?php } ?>
