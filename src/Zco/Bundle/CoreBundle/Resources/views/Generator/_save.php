<input type="submit" name="save" value="Sauvegarder" />
<input type="submit" name="save_edit" value="Sauvegarder et continuer à modifier" />
<?php if($action == 'edit' && isset($config['edit']['save_as']) && $config['edit']['save_as'] == true){ ?>
<input type="submit" name="save_as" value="Sauvegarder comme un nouvel élément" />
<?php } else{ ?>
<input type="submit" name="save_new" value="Sauvegarder et ajouter un nouveau" />
<?php } ?>