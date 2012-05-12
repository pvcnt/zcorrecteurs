<?php $view->extend('::layouts/default.html.php') ?>

<h1>Ajouter un message</h1>

<div id="descr">
	<p>Rédigez votre message et attribuez une note au service apporté par le site.</p>
</div>

<form id="formulaire_livreor" method="post" action="" name="formulaire">
	<input type="hidden" name="note" id="note" value="-1" />
	<ul class="star-rating">
		<li class="current-rating" id="current-rating" style="width:0px;">0 / 5</li>
		<li><a href="#" title="1 étoile sur 5" class="one-star">1</a></li>
		<li><a href="#" title="2 étoiles sur 5" class="two-stars">2</a></li>
		<li><a href="#" title="3 étoiles sur 5" class="three-stars">3</a></li>
		<li><a href="#" title="4 étoiles sur 5" class="four-stars">4</a></li>
		<li><a href="#" title="5 étoiles sur 5" class="five-stars">5</a></li>
	</ul>

	<label for="message">Votre message :</label><br />
	<textarea name="message" id="message"></textarea><br />

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