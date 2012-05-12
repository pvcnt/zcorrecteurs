<?php $view->extend('ZcoFileBundle::layout.html.php') ?>

<h1>Gestionnaire de fichiers</h1>

<?php if ($ratio >= 100): ?>
<div class="alert alert-error alert-block">
	<h4 class="alert-heading">Quota dépassé !</h4>
	Vous avez dépassé le quota de <?php echo $quota ?> Mo qui vous était alloué 
	et ne pouvez donc plus envoyer de fichiers depuis votre compte. Nous vous 
	invitons à faire le ménage parmi vos anciens fichiers, ou à contacter un 
	administrateur si vous souhaitez obtenir plus d’espace en indiquant 
	la raison.
</div>

<?php else: ?>

<div class="progress progress-<?php echo $usageClass ?>"<?php if ($quota <= -1) echo ' style="display: none;"' ?>>
	<div class="bar" style="width: <?php echo $ratio > 5 ? $ratio : 5 ?>%;"><?php echo $ratio ?> %</div>
</div>

<form method="post" action="<?php echo $view['router']->generate('zco_file_api_upload') ?>" enctype="multipart/form-data" id="uploadForm">
    <div class="droppable" id="droppable">
        <p>Déposez les fichiers depuis votre ordinateur dans cette zone.</p>
    </div>
    
    <input type="file" id="file" name="file[]" multiple="multiple" />
        
    <ul class="thumbnails" id="files"></ul>
    
    <div class="submit-upload">
        <input type="submit" class="btn btn-primary" value="Importer les fichiers" />
        <?php if (!empty($_SESSION['fichiers']['last_import'])): ?>
            <a class="btn" href="<?php echo $redirectUrl ?>">Voir le dernier import</a>
        <?php endif ?>
    </div>
</form>
<?php endif ?>

<?php $view['javelin']->initBehavior('zco-files-drag-and-drop-area', array(
    'redirect_url' => $redirectUrl,
)) ?>