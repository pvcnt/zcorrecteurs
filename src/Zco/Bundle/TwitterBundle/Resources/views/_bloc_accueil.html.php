<p class="centre italique"><a href="/twitter/">Tous les tweets</a></p>

<?php foreach ($Tweets as $tweet): ?>
	<div class="tweet">
		<em>
			<a style="float: right"
			   href="/membres/profil-<?php echo $tweet->Utilisateur->id
				?>-<?php echo rewrite($tweet->Utilisateur->pseudo) ?>.html">
				<?php echo htmlspecialchars($tweet->Utilisateur->pseudo) ?></a>

			<?php echo dateformat($tweet->creation) ?>
		</em>
		<div style="background: #BBBBBB; margin-top: 2px; margin-bottom: 10px; height: 1px">
		</div>
		<a style="float: right"
			   href="http://twitter.com/<?php
			   echo rawurlencode($tweet->Compte->nom);
			   ?>/status/<?php echo $tweet->twitter_id ?>">
				<img src="/bundles/zcotwitter/img/accueil-oiseau.png"
					 alt="Twitter" title="Voir sur Twitter"/>
		</a>
		<?php echo $view->get('messages')->parseTwitter(
			$view->get('messages')->parseLiens(htmlspecialchars($tweet->texte))) ?>
	</div>
<?php endforeach ?>


