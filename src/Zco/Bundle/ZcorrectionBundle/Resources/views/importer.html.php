<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoZcorrectionBundle::_onglets.html.php', array('s' => $s)) ?>

<h1>Importer un tutoriel corrigé</h1>

<p>Cette page vous permet d'importer une correction réalisée hors-ligne avec zEditor.
Assurez-vous qu'aucune modification n'ait été apportée au tutoriel depuis votre précédent 
export, elles seraient alors écrasées par la nouvelle version.</p>

<p>Pour récupérer la dernière version du tutoriel,
<a href="exporter-<?php echo $_GET['id'] ?>.html">procédez à un export</a>.</p>

<form action="" method="post" enctype="multipart/form-data">
        <fieldset>
                <legend>Importer un tutoriel</legend>
		<label for="tuto">Fichier :</label>
		<input type="file" name="tuto" id="tuto" />
		
		<input type="submit" />
	</fieldset>
</form>
