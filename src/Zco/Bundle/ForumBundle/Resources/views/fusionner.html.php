<?php $view->extend('::layouts/default.html.php') ?>

<h1>Fusionner un sujet</h1>
<h2><?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?></h2>

<p>Vous vous apprêtez à fusionner le sujet <strong><a href="sujet-<?php echo $InfosSujet['sujet_id'].'-'.rewrite($InfosSujet['sujet_titre']); ?>.html"><?php echo htmlspecialchars($InfosSujet['sujet_titre']); ?></a></strong> avec d'autres sujets.
Ce sujet restera le sujet parent, les autres sujets sélectionnés disparaitront, seuls leurs messages seront intégrés dans ce sujet.</p>

<fieldset>
	<legend>Sujets sélectionnés</legend>
	<form method="post" action="">
		<p>Voici les sujets sélectionnés pour être fusionnés. Vous pouvez à tout moment en décocher pour les retirer de la liste. Cliquez ensuite sur le bouton de mise à jour pour les déselectionner.<br />
		Une fois prêt, cliquez sur Fusionner pour lancer le processus.</p>

		<table class="UI_items">
			<thead>
				<tr>
					<th style="width: 35%;">Titre du sujet</th>
					<th style="width: 35%;">Forum</th>
					<th style="width: 20%;">Créé le</th>
					<th style="width: 10%;">Sélection</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($ListerSujetsSelectionnes as $s){ ?>
				<tr>
					<td>
						<a href="sujet-<?php echo $s['sujet_id']; ?>.html"><?php echo htmlspecialchars($s['sujet_titre']); ?></a>
						<?php if($s['sujet_id'] == $_GET['id']){ ?><strong>(sujet parent)</strong><?php } ?>
						<?php if(!empty($s['sujet_sous_titre'])){ ?><br />
						<span class="sous_titre"><?php echo htmlspecialchars($s['sujet_sous_titre']); ?></span>
						<?php } ?>
					</td>
					<td><?php echo htmlspecialchars($s['cat_nom']); ?></td>
					<td class="centre"><?php echo dateformat($s['sujet_date']); ?></td>
					<td class="centre"><input type="checkbox" name="sujet[<?php echo $s['sujet_id']; ?>]" id="sujet[<?php echo $s['sujet_id']; ?>]" checked="checked" /></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<br />
		<div class="send">
			<input type="submit" name="maj" value="Mettre à jour" /> <input type="submit" name="submit" value="Fusionner !" />
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>Sélection des messages à déplacer</legend>

	<form method="post" action="">
		<?php foreach($ListerSujetsSelectionnes as $s) echo '<input type="hidden" name="sujet['.$s['sujet_id'].']" value="on" />'; ?>
		
		<p>Vous pouvez entrer des fragments du titre d'un sujet. Les sujets cochés seront ajoutés à la liste des sujets à fusionner.</p>
		
		<label for="titre">Titre à rechercher : </label> 
		<input type="text" size="35" name="titre" id="titre" value="<?php if(isset($_POST['titre']) && isset($_POST['search'])) echo htmlspecialchars($_POST['titre']); ?>" /> 
		<input type="submit" name="search" value="Chercher" />
		
		<?php $view['javelin']->initBehavior('autocomplete', array(
		    'id' => 'titre', 
		    'callback' => '/forum/ajax-autocomplete-titre.html',
		)) ?>
	</form>


	<form method="post" action="">
		<?php foreach($ListerSujetsSelectionnes as $s) echo '<input type="hidden" name="sujet['.$s['sujet_id'].']" value="on" />'; ?>
		<input type="hidden" name="titre" value="<?php echo htmlspecialchars($_POST['titre']); ?>" />
		<?php if(!empty($ListerSujets)){ ?>
		<table class="UI_items">
			<thead>
				<tr>
					<th style="width: 35%;">Titre du sujet</th>
					<th style="width: 35%;">Forum</th>
					<th style="width: 20%;">Créé</th>
					<th style="width: 10%;">Sélection</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($ListerSujets as $s){ ?>
				<tr>
					<td>
						<a href="sujet-<?php echo $s['sujet_id']; ?>.html"><?php echo htmlspecialchars($s['sujet_titre']); ?></a>
						<?php if(!empty($s['sujet_sous_titre'])){ ?><br />
						<span class="sous_titre"><?php echo htmlspecialchars($s['sujet_sous_titre']); ?></span>
						<?php } ?>
					</td>
					<td><?php echo htmlspecialchars($s['cat_nom']); ?></td>
					<td class="centre"><?php echo dateformat($s['sujet_date']); ?></td>
					<td class="centre"><?php if($s['sujet_id'] != $_GET['id']){ ?><input type="checkbox" name="sujet[<?php echo $s['sujet_id']; ?>]" id="sujet[<?php echo $s['sujet_id']; ?>]" /><?php } else echo '-'; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<br />
		<div class="send">
			<input type="submit" name="add" value="Ajouter" />
		</div>

		<?php } elseif(isset($_POST['search'])){ ?>
		<p>Aucun sujet n'a été trouvé.</p>
		<?php } ?>
	</form>
</fieldset>
