<?php $view->extend('::layouts/default.html.php') ?>

<h1>Répondre à une soumission : <?php echo htmlspecialchars($Dictee->titre); ?></h1>
<?php echo $view->render('ZcoDicteesBundle::_dictee.html.php', compact('Dictee', 'DicteeEtats')); ?>

<h2>Texte</h2>
<p><?php echo nl2br(htmlspecialchars($Dictee->texte)); ?></p>

<h2>Fichier audio</h2>
<?php echo $view->render('ZcoDicteesBundle::_audio.html.php', compact('Dictee')); ?>


<h2>Répondre à la soumission</h2>
<form action="" method="post">
<?php echo $Form; ?>
<p class="centre">
	<input type="submit" value="Envoyer" name="envoyer" />
</p>
</form>
