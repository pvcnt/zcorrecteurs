<p>Page : <?php echo implode($Pages); ?></p>

<?php foreach ($Resultats as $resultat): ?>
	<?php echo $view->render('ZcoTwitterBundle::_tweet.html.php', array('tweet' => $resultat)) ?>
<?php endforeach; ?>

<p>Page : <?php echo implode($Pages); ?></p>
