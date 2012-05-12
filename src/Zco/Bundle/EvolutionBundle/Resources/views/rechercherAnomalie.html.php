<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoEvolutionBundle::_onglets.html.php') ?>

<h1>Recherche avancée</h1>

<form method="get" action="">
	<input type="text" name="titre" id="titre" />
	<select name="orderby" id="orderby">
		<option value="priorite">par importance</option>
		<option value="etat">par état</option>
		<option value="recent">le plus récent en premier</option>
		<option value="ancien">le plus vieux en premier</option>
		<option value="edition">par date de mise à jour</option>
	</select>

	<input type="submit" value="Rechercher" name="submit" />&nbsp;&nbsp;&nbsp;
	<a href="demandes.html">Recherche simple</a><br />

	<input type="checkbox" name="rechercher_description" id="input_rechercher_description"/>
	<label for="input_rechercher_description" class="nofloat">Rechercher dans la description</label>

	<fieldset>
		<legend>Type de demande</legend>
		<label for="input_type">Type de demande :</label>
		<select name="type" id="input_type">
			<option value="0">Indifférent</option>
			<option value="1">Anomalie</option>
			<option value="2">Tâche</option>
		</select>

	</fieldset>
	<fieldset>
		<legend>État et importance</legend>
		<table style="min-width: 50%;">
			<tr>
				<td style="width: 50%; vertical-align: top;">
					<p>État de l'anomalie :</p>
					<?php foreach($TicketsEtats as $cle => $valeur){ ?>
					<input type="checkbox" name="etat[<?php echo $cle; ?>]" id="e<?php echo $cle; ?>" />&nbsp;
					<label for="e<?php echo $cle; ?>" class="nofloat">
						<?php echo htmlspecialchars($valeur['etat_nom']); ?>
					</label><br />
					<?php } ?>
				</td>

				<td style="width: 50%; vertical-align: top;">
					<p>Priorité de l'anomalie :</p>
					<?php foreach($TicketsPriorites as $cle => $valeur){ ?>
					<input type="checkbox" name="priorite[<?php echo $cle; ?>]" id="p<?php echo $cle; ?>" />&nbsp;
					<label for="p<?php echo $cle; ?>" class="nofloat">
						<?php echo htmlspecialchars($valeur['priorite_nom']); ?>
					</label><br />
					<?php } ?>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Partie du site touchée</legend>
		<p>Catégorie du site affectée par l'anomalie :</p>
		<?php foreach($ListerCategories as $c){ ?>
		<?php if($c['cat_niveau'] <= 1){ ?>
		<input type="checkbox" name="cat[<?php echo $c['cat_id']; ?>]" id="c<?php echo $c['cat_id']; ?>" />&nbsp;
		<label for="c<?php echo $c['cat_id']; ?>" class="nofloat">
			<?php echo ($c['cat_niveau'] > 0 ? '..... ' : '').htmlspecialchars($c['cat_nom']); ?>
		</label><br />
		<?php } } ?>
	</fieldset>

	<?php if(verifier('tracker_voir_assigne')){ ?><br />
	<fieldset>
		<legend>Autres options</legend>
		
		<p>Administrateur assigné à la résolution :</p>
		<input type="radio" name="admin" value="-1" id="admin_nimporte" checked="checked" />
    	<label for="admin_nimporte" class="nofloat">N'importe</label><br />

    	<input type="radio" name="admin" value="0" id="admin_aucun" />
		<label for="admin_aucun" class="nofloat">Aucun</label><br />

    	<input type="radio" name="admin" value="1" id="admin_un" />
    	<label for="admin_un" class="nofloat">Pseudo : </label>
		<input type="text" name="admin_pseudo" id="admin_pseudo" />
	</fieldset>
	<?php } ?>

    <div class="send">
	    <input type="submit" value="Rechercher" name="submit" />
	</div>
</form>

