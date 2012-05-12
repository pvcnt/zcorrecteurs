<?php $view->extend('::layouts/default.html.php') ?>

<h1>Corriger une copie</h1>

<?php if(isset($_GET['delete'])) { ?>
<form method="post" action="">
<fieldset><legend>Correction</legend>
<p>En validant, vous supprimerez l'attribution de la correction de cette copie à son correcteur actuel. La copie se retrouvera de nouveau sans correcteur.</p>
<div class="send">
	<input type="submit" name="submit" value="Supprimer le correcteur actuel" />
</div>
</fieldset>
</form>
<?php } else { ?>
<form method="post" action="">
<fieldset><legend>Correction</legend>
<p>En validant, vous devenez le correcteur de cette copie. Les autres correcteurs seront alors prévenus que vous avez pris en charge la correction de cette copie.</p>
<div class="send">
	<input type="submit" name="submit" value="Prendre en charge la copie" />
</div>
</fieldset>
</form>
<?php } ?>
