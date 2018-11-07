<?php $view->extend('::layouts/bootstrap.html.php') ?>

<h1><?php echo htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']) ?></h1>

<form method="post" action="" class="form-horizontal">
    <div class="control-group">
        <label for="groupe" class="control-label">Groupe principal</label>
        <div class="controls">
            <select id="groupe" name="groupe">
                <?php foreach ($ListerGroupes as $g) {
                    if ($g['groupe_id'] == $InfosUtilisateur['utilisateur_id_groupe'])
                        $selected = ' selected="selected"';
                    else
                        $selected = '';
                    echo '<option value="' . $g['groupe_id'] . '" ' . $selected . ' >' . htmlspecialchars($g['groupe_nom']) . ' (' . $g['groupe_effectifs'] . ')</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <?php if (!empty($ListerGroupesSecondaires)) { ?>
        <div class="control-group">
            <label for="groupes" class="control-label">Groupes secondaires</label>
            <div class="controls">
                <select id="groupes"
                        name="groupes_secondaires[]"
                        multiple="multiple"
                        size="<?php echo count($ListerGroupesSecondaires) ?>">
                    <?php
                    foreach ($ListerGroupesSecondaires as $g) {
                        if (in_array($g['groupe_id'], $GroupesSecondaires))
                            $selected = ' selected="selected"';
                        else
                            $selected = '';
                        echo '<option value="' . $g['groupe_id'] . $selected . '>' . htmlspecialchars($g['groupe_nom']) . ' (' . $g['groupe_effectifs'] . ')</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    <?php } ?>

    <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Enregistrer"/>
    </div>
</form>
