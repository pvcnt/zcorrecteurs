<div class="box message" id="t<?php echo $tweet['twitter_id'] ?>">
	<p>
		<a style="float: right"
		   href="http://twitter.com/<?php
		   echo rawurlencode($tweet['nom_compte']);
		   ?>/status/<?php echo $tweet['twitter_id'] ?>">
			<img src="/bundles/zcotwitter/img/bouton.png"
				 alt="Twitter" title="Voir sur Twitter"/>
		</a>
		<a href="#t<?php echo $tweet['twitter_id'] ?>">#</a>
		Par <?php echo $view->get('messages')->pseudo($tweet['Utilisateur'], 'id', 'pseudo') ?>,
		<?php echo dateformat($tweet['creation'], MINUSCULE) ?>,
		<em>
			via
			<a href="http://twitter.com/<?php
			   echo rawurlencode($tweet['nom_compte']) ?>">
				@<?php echo htmlspecialchars($tweet['nom_compte']) ?></a></em>.
	</p>
	<hr />
	<p>
		<?php echo $view->get('messages')->avatar($tweet['Utilisateur'], 'avatar') ?>
		<?php echo $view->get('messages')->parseTwitter($view->get('messages')->parseLiens(htmlspecialchars($tweet['texte']))) ?>
	</p>
</div>