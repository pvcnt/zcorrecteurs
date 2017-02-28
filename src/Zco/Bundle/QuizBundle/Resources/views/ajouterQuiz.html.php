<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1>Ajouter un quiz</h1>

<form method="post" action="" class="form-horizontal">
    <div class="control-group">
		<label for="nom" class="control-label">Nom</label>
        <div class="controls">
		    <input type="text" name="nom" id="nom" size="40" />
        </div>
    </div>

    <div class="control-group">
		<label for="description" class="control-label">Description</label>
        <div class="controls">
		    <input type="text" name="description" id="description" class="input-xxlarge" />
        </div>
    </div>

    <div class="control-group">
		<label for="difficulte" class="control-label">Difficulté</label>
        <div class="controls">
            <select name="difficulte" id="difficulte">
                <?php foreach ($Difficultes as $cle => $valeur){ ?>
                <option value="<?php echo $cle; ?>"><?php echo htmlspecialchars($valeur); ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="control-group">
		<label for="categorie" class="control-label">Catégorie</label>
        <div class="controls">
            <select name="categorie" id="categorie">
                <?php foreach ($ListerCategories as $categorie){ ?>
                <option value="<?php echo $categorie['cat_id']; ?>">
                    <?php echo htmlspecialchars($categorie['cat_nom']); ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="control-group">
		<label for="aleatoire" class="control-label">Nombre de réponses choisies dans un ordre aléatoire</label>
        <div class="controls">
            <input type="number" name="aleatoire" id="aleatoire" value="0" min="0"/>
		    <p class="help-block">Le fait de choisir zéro permet d'afficher toutes les questions et dans l'ordre (mode aléatoire désactivé).</p>
        </div>
	</div>

	<div class="form-actions">
		<input type="submit" class="btn btn-primary" value="Envoyer" />
        <a class="btn" href="gestion.html">Annuler</a>
	</div>
</form>
