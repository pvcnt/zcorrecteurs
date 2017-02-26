<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoTwitterBundle::tabs.html.php', array('currentTab' => 'mentions')) ?>

<p>
    Les mentions sont des <em>tweets</em> de la forme « @<?php echo htmlspecialchars($account['nom']) ?> » 
	qui requièrent votre attention. Elles sont souvent utilisées quand une réponse
    est requise de la part de l'utilisateur cité, ou bien pour attirer son
    attention sur un <em>tweet</em>.
</p>

<?php echo $view['knp_pagination']->render($mentions) ?>

<?php foreach ($mentions as $mention): ?>
<div class="box message" id="t<?php echo $mention['id']; ?>">
	<p>
		<a style="float: right"
		   href="http://twitter.com/<?php
		   echo rawurlencode($mention['pseudo']);
		   ?>/status/<?php echo $mention['id'] ?>">
			<img src="/bundles/zcotwitter/img/bouton.png"
				 alt="Twitter" title="Voir sur Twitter"/>
		</a>
		<?php if ($mention['reponse_id']): ?>
		<a style="float: right"
		   href="http://twitter.com/<?php
		   echo rawurlencode($account['nom']);
		   ?>/status/<?php echo $mention['reponse_id'] ?>">
			<img src="/pix.gif" title="Vous avez répondu à ce tweet" alt="Répondu"
			     style="float: right" class="fff arrow_rotate_clockwise"/>
			&nbsp;
		</a>
		<?php endif ?>

		<a href="#t<?php echo $mention['id'] ?>">#</a>
		Par
		<a href="http://twitter.com/<?php echo rawurlencode($mention['pseudo']); ?>">
			<?php echo htmlspecialchars($mention['pseudo']) ?>
		</a> (<?php echo htmlspecialchars($mention['nom']) ?>),

		<?php echo dateformat($mention['creation'], MINUSCULE) ?>

		- <a href="<?php echo $view['router']->path('zco_twitter_newTweet', array('id' => $mention['id'])) ?>">Répondre</a>
	</p>
	<hr />
	<img src="<?php echo $mention['avatar'] ?>" alt="Avatar" class="avatar" />
	<?php echo $view->get('messages')->parseTwitter($view->get('messages')->parseLiens(htmlspecialchars($mention['texte']))) ?>
</div>
<?php endforeach ?>

<?php if(count($mentions) > 7): ?>
	<?php echo $view['knp_pagination']->render($mentions) ?>
<?php endif ?>