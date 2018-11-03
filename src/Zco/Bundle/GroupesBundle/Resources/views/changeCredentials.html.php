<?php $view->extend('::layouts/bootstrap.html.php') ?>
<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css') ?>

<h1>
    <?php echo htmlspecialchars($InfosGroupe['groupe_nom']) ?>
    <small>Changer les droits</small>
</h1>

<form method="get" action="" class="form-inline">
    <label for="credential">Droit</label>
    <select name="credential" id="credential">
        <?php
        $current = 0;
        $i = 0;
        foreach($ListerDroits as $d)
        {
            if($current != $d['cat_id'])
            {
                $current = $d['cat_id'];
                if($i != 0)
                    echo '</optgroup>';
                echo '<optgroup label="'.htmlspecialchars($d['cat_nom']).'">';
                $i++;
            }
        ?>
        <option value="<?php echo $d['droit_id']; ?>"<?php if(!empty($InfosDroit) && $d['droit_id'] == $InfosDroit['droit_id']) echo ' selected="selected"'; ?>><?php echo htmlspecialchars($d['droit_description']); ?></option>
        <?php } echo '</optgroup>' ?>
    </select>

    <input type="submit" value="Modifier ce droit" class="btn" />
</form>

<?php if(!empty($InfosDroit)){ ?>
<?php if(!empty($InfosDroit['droit_description_longue'])){ ?>
    <div class="alert alert-info">
        <?php echo $view['messages']->parse($InfosDroit['droit_description_longue']); ?>
    </div>
<?php } ?>

<form method="post" action="" class="form-horizontal">
    <?php if(!$InfosDroit['droit_choix_binaire']){ ?>
    <div>
        <label for="valeur" class="nofloat gras">Valeur numérique</label>
        <input type="text" size="4" name="valeur" id="valeur" value="<?php if(!empty($ValeurDroit)) echo $ValeurNumerique; ?>" /><br />
    </div>
    <?php } if($InfosDroit['droit_choix_binaire'] && !$InfosDroit['droit_choix_categorie']){ ?>
    <div>
        <label for="valeur" class="nofloat gras">Attribuer ce droit</label>
        <input type="checkbox" name="valeur" id="valeur"<?php if(!empty($ValeurDroit) && $ValeurDroit['gd_valeur'] == 1) echo ' checked="checked"'; ?> /><br />
    </div>
    <?php } if($InfosDroit['droit_choix_categorie']){ ?>
    <label for="cat">Catégorie(s) : </label>
    <select name="cat[]" id="cat" size="<?php if(count($ListerEnfants) > 15) echo '20'; else echo '10'; ?>" multiple="multiple" style="min-width: 300px;">
        <?php
        foreach($ListerEnfants as $e)
        {
            $marqueur = '';
            $selected = '';

            for($i = 1 ; $i < $e['cat_niveau'] ; $i++)
                $marqueur .= '.....';
            foreach($ValeurDroit as $v)
            {
                if($v['gd_id_categorie'] == $e['cat_id'] && $v['gd_valeur'] > 0)
                    $selected = ' selected="selected"';
            }
        ?>
        <option value="<?php echo $e['cat_id']; ?>"<?php echo $selected; ?>><?php echo $marqueur.' '.htmlspecialchars($e['cat_nom']); ?></option>
        <?php } ?>
    </select>
    <p class="help-block">Vous pouvez sélectionner plusieurs catégories en maintenant CTRL ou MAJ enfoncée.</p>
    <?php } ?>

    <div class="form-actions">
        <input type="submit" value="Enregistrer" class="btn btn-primary" />
    </div>
	</form>
<?php } ?>
