<?php $view->extend('::layouts/default.html.php') ?>

<h1>Sauvegardes automatiques de zCode</h1>

<p>
	Les données contenues par un formulaire sont sauvegardées automatique toutes 
	les minutes. Vous pouvez ainsi retrouver ce que vous étiez en train d'écrire 
	en cas de coupure de courant, anomalie, etc. Les sauvegardes sont effacées 
	tous les jours, pour éviter qu'elles ne s'accumulent.
</p>

<?php if($ListerSauvegardes){ ?>
<table class="UI_items">
	<thead>
		<tr>
			<th style="width: 15%;">Date</th>
			<th style="width: 15%;">Formulaire</th>
			<?php if(!empty($_GET['id'])){ ?>
			<th style="width: 10%;">Récupérer</th>
			<?php } ?>
			<th style="width: 60%;">Texte</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($ListerSauvegardes as $s){ ?>
		<tr>
			<td class="centre"><?php echo dateformat($s['sauvegarde_date']); ?></td>
			<td class="centre">
				<a href="<?php echo htmlspecialchars($s['sauvegarde_url']); ?>">
					<?php echo htmlspecialchars($s['sauvegarde_url']); ?>
				</a>
			</td>
			<?php if(!empty($_GET['id'])){ ?>
			<td class="centre">
				<a href="#" onclick="parent.document.id('<?php echo $_GET['id']; ?>').value=document.id('save_<?php echo $s['sauvegarde_id']; ?>').value; return false;">
					<img src="/img/membres/inserer.png" alt="Récupérer le texte" />
				</a>
			</td>
			<?php } ?>
			<td>
				<textarea rows="8" id="save_<?php echo $s['sauvegarde_id']; ?>" style="height: 100px;"><?php echo htmlspecialchars($s['sauvegarde_texte']); ?></textarea>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<p class="italique">Aucune sauvegarde n'a été effectuée depuis hier.</p>
<?php } ?>
