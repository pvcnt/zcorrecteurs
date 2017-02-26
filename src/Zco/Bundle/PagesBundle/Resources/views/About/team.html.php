<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPagesBundle:About:_tabs.html.php', array('currentTab' => 'team')) ?>

<h1>Ceux sans qui rien ne serait possible. <a href="/recrutement/">Rejoignez-nous &rarr;</a></h1>

<p class="intro-text">
	zCorrecteurs.fr est un projet unique et ambitieux s’appuyant sur une équipe
	tout aussi unique. Tous ces bénévoles effectuent chaque jour un travail
	indispensable, en corrigeant vos documents, faisant évoluer le site ou encore 
	en enrichissant le contenu du site.
	<?php if (verifier('voir_adresse')): ?><br />
	En tant que membre ou ancien membre de cette équipe, vous pouvez 
	<a href="<?php echo $view['router']->path('zco_user_localisation') ?>">consulter la localisation de ses membres</a>.
	<?php endif ?>
</p>

<ul class="thumbnails">
	<?php foreach ($equipe as $i => $user): ?>
		<li class="span2">
			<div class="thumbnail center" style="height: 165px;">
				<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>" class="avatar-link" title="Groupe : <?php echo htmlspecialchars($user->getGroup()) ?>">
					<?php if ($user->hasAvatar()): ?>
						<img src="<?php echo htmlspecialchars($user->getAvatar()) ?>" alt="Avatar de <?php echo htmlspecialchars($user->getUsername()) ?>" style="vertical-align: middle;" />
					<?php else: ?>
						<img src="/bundles/zcocore/img/anonyme.png" alt="Aucun avatar" style="vertical-align: middle;" />
					<?php endif ?>
				</a>
				<div class="caption" style="text-align: center;">
					<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>" title="Groupe : <?php echo htmlspecialchars($user->getGroup()) ?>">
						<span style="color: <?php echo $user['Groupe']['class'] ?>;"><?php echo htmlspecialchars($user->getUsername()) ?></span>
					</a>
				</div>
			</div>
		</li>
	<?php endforeach; ?></tr>
</ul>

<p class="good">
	La liste ne serait pas complète sans citer tous ceux qui ont travaillé 
	avec nous par le passé. Voici la liste de ces membres qui ont tous apporté 
	leur pierre à l'édifice :
	<?php foreach ($anciens as $i => $user): ?>
		<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>"><?php echo htmlspecialchars($user->getUsername()) ?></a><?php echo $i === count($anciens) - 1 ? '.' : ',' ?>
	<?php endforeach; ?>
</p>

<?php $view['javelin']->initBehavior('twipsy', array('selector' => '.avatar-link')) ?>