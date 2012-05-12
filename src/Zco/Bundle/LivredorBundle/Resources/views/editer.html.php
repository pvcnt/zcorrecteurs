<?php $view->extend('::layouts/default.html.php') ?>

<h1>Modifier un message</h1>

<form id="formulaire_livreor" method="post" action="" name="formulaire">
	<input type="hidden" name="note" id="note" value="4" />
	<ul class="star-rating">
		<li class="current-rating" id="current-rating" style="width: <?php echo $msg->note * 30; ?>px;"><?php echo $msg->note; ?>/5 étoiles par défaut</li>
		<li><a href="#" title="1 étoile sur 5" class="one-star" onclick="changerNote(1);return false">1</a></li>
		<li><a href="#" title="2 étoiles sur 5" class='two-stars' onclick="changerNote(2);return false">2</a></li>
		<li><a href="#" title="3 étoiles sur 5" class='three-stars' onclick="changerNote(3);return false">3</a></li>
		<li><a href="#" title="4 étoiles sur 5" class='four-stars' onclick="changerNote(4);return false">4</a></li>
		<li><a href="#" title="5 étoiles sur 5" class='five-stars' onclick="changerNote(5);return false">5</a></li>
	</ul>

	<label for="message">Votre message :</label><br />
	<textarea name="message" id="message"><?php echo htmlspecialchars($msg->message); ?></textarea><br />

	<p>Note : le zCode est <strong>désactivé</strong>.</p>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>

<?php $view['javelin']->initBehavior('livredor-stars', array(
    'id' => 'formulaire_livreor', 
    'note_id' => 'note', 
    'textarea_id' => 'message',
)) ?>