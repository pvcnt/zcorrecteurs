<?php $this->extend('::layouts/default.html.php') ?>

<h1>Ajouter une épreuve de recrutement</h1>

<form method="post" action="">
	<fieldset>
		<legend>Description de l'épreuve</legend>
		<label for="nom">Nom :</label>
		<input type="text" name="nom" id="nom" size="40" /><br />

		<label for="actif">Active ?</label>
		<input type="checkbox" name="actif" id="actif" />
	</fieldset>
	
	<fieldset>
		<legend>Contenu de l'épreuve</legend>
		<div style="float: right; width: 380px;">
			<table class="UI_items">
				<tr>
					<th>Faute</th>
					<th>Correction</th>
					<th>Type</th>
				</tr>
				<tr>
					<td>ectoplasm</td>
					<td><input type="text" value="ectoplasme" /></td>
					<td><select><option>Orthographe</option><option>Grammaire</option></td>
				</tr>
			</table>
		</div>

		<div style="margin-right: 400px;">
			<textarea style="width: 100%;"></textarea>
		</div>
	</fieldset>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>