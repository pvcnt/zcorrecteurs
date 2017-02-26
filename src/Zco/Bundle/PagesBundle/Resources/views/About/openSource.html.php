<?php $view->extend('::layouts/bootstrap.html.php') ?>

<?php echo $view->render('ZcoPagesBundle:About:_tabs.html.php', array('currentTab' => 'opensource')) ?>

<h1>Ce site est un logiciel libre. <a href="http://developpeurs.zcorrecteurs.fr">En savoir plus &rarr;</a></h1>

<p class="good">
	L’équipe de zCorrecteurs.fr a la particularité de mélanger en son sein 
	des personnes au profil technique à des personnes au profil littéraire. 
	Cela permet notamment de garantir au code faisant tourner le site 
	une bonne qualité grâce à un développement intégralement réalisé en 
	interne et un logiciel parfaitement adapté à nos besoins.
</p>

<p class="good">
	Les développeurs du site ont tous la culture du logiciel libre, de 
	l’<em>open source</em>. Nous utilisons de nombreux logiciels libres 
	pour bâtir le site et le faire tourner, mais nous publions aussi 
	des morceaux de notre code source sous licence libre, afin de permettre 
	à d’autres développeurs de réutiliser ce que nous avons construit.
</p>

<h2>Code disponible sous licence libre</h2>

<p class="good">
	Le code source de ce site est disponible sous licence AGPL, ce qui signifie 
	que vous pouvez librement le consulter, l’analyser et y contribuer. Il est 
	publié <a href="https://github.com/zcorrecteurs/zcorrecteurs.fr">sur notre dépôt GitHub</a>.
	Toute personne souhaitant aider au développement de ce site peut dorénavant 
	le faire, que ce soit en nous aidant à corriger des dysfontionnements ou en 
	développant de nouvelles fonctionnalités demandées par la communauté.
</p>

<p class="well center" style="font-size: 1.1em; padding: 10px; line-height: 1.5em;">
	Toutes les ressources pour les développeurs sont disponibles sur notre portail dédié<br />
	<a href="http://developpeurs.zcorrecteurs.fr" class="bold">developpeurs.zcorrecteurs.fr</a>
</p>

<h2>Logiciels libres utilisés</h2>

<p class="good">
	Le site s’appuie sur de nombreux logiciels libres. En nous appuyant sur des 
	outils de qualité et performants, nous pouvons nous concentrer sur l’essentiel.
	Nous remercions tous ceux qui ont créé ces différents logiciels et ceux qui les 
	font vivre aujourd’hui. De façon non exhaustive, nous utilisons les logiciels suivants :
</p>

<ul>
	<li><a href="http://www.apache.org/">Apache</a></li>
	<li><a href="http://twitter.github.com/bootstrap/">Bootstrap</a></li>
	<li><a href="http://www.debian.org/index.fr.html">Debian</a></li>
	<li><a href="http://doctrine-project.org/">Doctrine</a></li>
	<li><a href="http://www.drupal.org/">Drupal</a></li>
	<li><a href="http://git-scm.com/">Git</a></li>
	<li><a href="http://jquery.com">jQuery</a></li>
	<li><a href="http://mootools.net/">Mootools</a></li>
	<li><a href="http://www.mysql.fr/">MySQL</a></li>
	<li><a href="http://fr.php.net/">PHP</a></li>
	<li><a href="http://symfony.com/">Symfony</a></li>
	<li><a href="http://swiftmailer.org/">Swiftmailer</a></li>
</ul>

<p class="good">
	Certaines icônes du site proviennent du site <a href="http://www.famfamfam.com/lab/icons/silk/">FamFamFam</a>, 
	que nous remercions au passage.
</p>