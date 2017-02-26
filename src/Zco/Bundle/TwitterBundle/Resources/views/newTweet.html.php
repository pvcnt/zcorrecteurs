<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoTwitterBundle::tabs.html.php', array('currentTab' => 'newTweet')) ?>

<?php if ($mention): ?>
<div class="box message" id="t<?php echo $mention['id']; ?>">
	<p>
		<a style="float: right"
		   href="http://twitter.com/<?php
		   echo rawurlencode($mention['pseudo']);
		   ?>/status/<?php echo $mention['id'] ?>">
			<img src="/bundles/zcotwitter/img/bouton.png"
				 alt="Twitter" title="Voir sur Twitter"/>
		</a>
		<a href="#t<?php echo $mention['id'] ?>">#</a>
		Par
		<a href="http://twitter.com/<?php echo rawurlencode($mention['pseudo']); ?>">
			<?php echo htmlspecialchars($mention['pseudo']) ?>
		</a> (<?php echo htmlspecialchars($mention['nom']) ?>),

		<?php echo dateformat($mention['creation'], MINUSCULE) ?>
	</p>
	<hr />
	<img src="<?php echo $mention['avatar'] ?>" alt="Avatar" class="avatar" />
	<?php echo $view->get('messages')->parseTwitter($view->get('messages')->parseLiens(htmlspecialchars($mention['texte']))) ?>
</div>

<div class="rmq information" style="margin-bottom: 15px;">
	Ce <em>tweet</em>, en tant que réponse, n'apparaîtra pas sur le site
	mais uniquement sur Twitter.
</div>
<?php $view['vitesse']->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css') ?>
<?php endif ?>

<form action="" method="post" class="form-horizontal">
	<div class="control-group">
		<label for="tweet_texte" class="control-label">Contenu du <em>tweet</em></label>
		<div class="controls">
			<textarea id="tweet_texte" style="height: 50px; width: 98%;" name="tweet"><?php
			if (isset($mention)) echo '@'.htmlspecialchars($mention['pseudo']).' '
			?></textarea>
			<p class="help-block" id="tweet_chars" style="display: none"></p>
		</div>
	</div>

	<div class="control-group">
		<label for="id_for_comptes" class="control-label">Compte où poster ce <em>tweet</em></label>
		<div class="controls">
			<select name="compte" id="id_for_comptes">
				<?php foreach ($accounts as $id => $account): ?>
					<option value="<?php echo $id ?>">
						<?php echo htmlspecialchars($account['nom']) ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	
	<?php if (verifier('twitter_procuration')): ?>
		<div class="control-group">
			<label for="pseudo" class="control-label">Auteur</label>
			<div class="controls">
				<input type="text" name="pseudo" id="pseudo" />
				<p class="help-block">
					Le pseudo du membre à afficher à côté du <em>tweet</em>.
					Laissez vide pour afficher le vôtre.
				</p>
        		<?php $view['javelin']->initBehavior('autocomplete', array(
        		    'id' => 'pseudo', 
        		    'callback' => $view['router']->path('zco_user_api_searchUsername'),
        		)) ?>
			</div>
		</div>
	<?php endif ?>
	
	<div class="form-actions">
		<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"/>
		<input type="submit" class="btn btn-primary" value="Envoyer" id="tweet_submit"/><br/>
	</div>
	
	<?php $view['javelin']->initBehavior('twitter-count-characters', array(
		'textarea_id' => 'tweet_texte',
		'chars_id' => 'tweet_chars',
		'button_id' => 'tweet_submit',
	)) ?>
</form>
