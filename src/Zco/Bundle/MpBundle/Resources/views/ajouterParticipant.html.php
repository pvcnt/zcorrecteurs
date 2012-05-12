<?php if (!$xhr): ?>
    <?php $view->extend('::layouts/default.html.php') ?>
    <h1>Ajouter un participant</h1>
<?php endif ?>

<fieldset>
    <legend>Ajout d'un participant</legend>
    <form action="" method="post">
        <label for="pseudo">Pseudo : </label>
        <input type="text" name="pseudo" id="pseudo" /><br />
        
		<?php $view['javelin']->initBehavior('autocomplete', array(
		    'id' => 'pseudo', 
		    'callback' => $view['router']->generate('zco_user_api_searchUsername'),
		)) ?>
		
        <label for="master">Maître de conversation : </label>
        <input type="checkbox" name="master" id="master" /><br />
        
        <div style="width: 0; margin: auto;">
            <input type="submit" value="Ajouter" />
        </div>
    </form>
</fieldset>
