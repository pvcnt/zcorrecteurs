<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPagesBundle:Static:aboutTabs.html.php', array('currentTab' => 'team')) ?>

<h1>Ceux sans qui rien ne serait possible.</h1>

<p class="intro-text">
	zCorrecteurs.fr est un projet unique et ambitieux s’appuyant sur une équipe
	tout aussi unique. Tous ces bénévoles effectuent chaque jour un travail
	indispensable, en corrigeant vos documents, faisant évoluer le site ou encore 
	en enrichissant le contenu du site.
</p>

<ul class="thumbnails">
	<?php foreach ($equipe as $i => $user): ?>
		<li class="span2">
			<div class="thumbnail center" style="height: 165px;">
				<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>" class="avatar-link">
                    <?php echo $view['messages']->afficherAvatar($user) ?>
				</a>
				<div class="caption center">
					<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>">
						<?php echo htmlspecialchars($user->getUsername()) ?>
					</a>
				</div>
			</div>
		</li>
	<?php endforeach ?>
</ul>

<p class="good">
	La liste ne serait pas complète sans citer tous ceux qui ont travaillé 
	avec nous par le passé. Voici la liste de ces membres qui ont tous apporté 
	leur pierre à l'édifice :
	<?php foreach ($anciens as $i => $user): ?>
		<a href="<?php echo $view['router']->path('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))) ?>"><?php echo htmlspecialchars($user->getUsername()) ?></a><?php echo $i === count($anciens) - 1 ? '.' : ',' ?>
	<?php endforeach; ?>
</p>