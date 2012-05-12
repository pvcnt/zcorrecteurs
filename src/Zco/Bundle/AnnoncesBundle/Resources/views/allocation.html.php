<?php $view->extend('::layouts/default.html.php') ?>
<?php echo $view->render('ZcoAnnoncesBundle::_onglets.html.php') ?>

<h1>Allocation des annonces globales</h1>

<form method="post" action="">
	<fieldset>
		<legend>Analyser l'allocation des annonces</legend>
		<label for="domaine">Domaine :</label>

		<select name="domaine" id="domaine">
			<?php foreach ($domaines as $domaine): ?>
			<option value="<?php echo $domaine ?>"<?php if (isset($attrDomaine) && $domaine == $attrDomaine) echo ' selected="selected"' ?>>
				<?php echo $domaine ?>
			</option>
			<?php endforeach; ?>
		</select><br />
		
		<label for="groupe">Groupe :</label>
		<select name="groupe" id="groupe">
			<?php foreach ($groupes as $groupe){ ?>
			<option value="<?php echo $groupe['id'] ?>"<?php if ((isset($attrGroupe) && $groupe['id'] == $attrGroupe) || (!isset($attrGroupe) && $groupe['id'] == GROUPE_VISITEURS)) echo ' selected="selected"' ?>>
				<?php echo htmlspecialchars($groupe['nom']) ?>
			</option>
			<?php } ?>
		</select><br />

		<label for="categorie">Section :</label>
		<select name="categorie" id="categorie">
			<?php foreach ($categories as $categorie){ ?>
			<option value="<?php echo $categorie['id'] ?>"<?php if ((isset($attrCategorie) && $categorie['id'] == $attrCategorie) || (!isset($attrCategorie) && $categorie['nom'] === 'Accueil')) echo ' selected="selected"' ?>>
				<?php echo htmlspecialchars($categorie['nom']) ?>
			</option>
			<?php } ?>
		</select><br />

		<label for="pays">Pays :</label>
		<select name="pays" id="pays" style="min-width: 200px;">
			<?php foreach ($pays as $p){ ?>
			<option value="<?php echo $p['id'] ?>"<?php if ((isset($attrPays) && $p['id'] == $attrPays) || (!isset($attrPays) && $p['nom'] === 'France')) echo ' selected="selected"' ?>>
				<?php echo htmlspecialchars($p['nom']) ?>
			</option>
			<?php } ?>
		</select>
		
		<div class="send">
			<input type="submit" name="submit" value="Envoyer" />
		</div>
	</fieldset>
</form>

<?php if (isset($annonces)): ?>
<?php if (count($annonces) > 0): ?>
<table class="UI_items">
    <thead>
        <tr>
            <th>Nom</th>
            <th style="width: 20%;">Date de début</th>
            <th style="width: 20%;">Date de fin</th>
            <th style="width: 10%;">Probabilité</th>
			<th style="width: 10%;">Poids</th>
            <th style="width: 10%;">Actions</th>
        </tr>
    </thead>
    
    <tbody>
        <?php foreach ($annonces as $annonce): ?>
        <tr>
            <td>
                <a onmouseover="$('annonce<?php echo $annonce['id'] ?>').setStyle('display', 'block');" onmouseout="$('annonce<?php echo $annonce['id'] ?>').setStyle('display', 'none');" title="Visualiser la bannière en action" href="/?_annonce=<?php echo $annonce['id'] ?>"><?php echo htmlspecialchars($annonce['nom']) ?></a>
                <div id="annonce<?php echo $annonce['id'] ?>" style="position: absolute; display: none; width: 80%; font-weight: normal;">
                    <?php echo $annonce->genererHTML() ?>
                </div>
            </td>
            <td class="centre"><?php echo dateformat($annonce['date_debut']) ?></td>
            <td class="centre"><?php echo dateformat($annonce['date_fin']) ?></td>
			<td class="centre"><?php echo $view['humanize']->numberformat(100 * $annonce['poids'] / $sommePoids, 2) ?> %</td>
			<td class="centre"><?php echo $annonce['poids'] ?></td>
            <td class="centre">
				<?php if (verifier('annonces_ajouter')): ?>
				<a href="ajouter-<?php echo $annonce['id'] ?>.html"><img src="/img/copier.png" alt="Copier" /></a>
				<?php endif; ?>
                <?php if (verifier('annonces_modifier')): ?>
                <a href="modifier-<?php echo $annonce['id'] ?>.html"><img src="/img/editer.png" alt="Modifier" /></a>
                <?php endif; ?>
                <?php if (verifier('annonces_supprimer')): ?>
                <a href="supprimer-<?php echo $annonce['id'] ?>.html"><img src="/img/supprimer.png" alt="Supprimer" /></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>Aucune annonce n'a été trouvée.</p>
<?php endif; ?>
<?php endif; ?>