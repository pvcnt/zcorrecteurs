<?php $view->extend('::layouts/default.html.php') ?>

<h1>Accueil du centre d'aide</h1>
<h2>Recherche d'un sujet d'aide</h2>

<div class="UI_box" style="float: right; width: 370px;">
	<form method="get" action="recherche.html">
		<input type="text" name="recherche" size="40" value="<?php echo htmlspecialchars($recherche) ?>" />
		<input type="submit" value="Rechercher dans l'aide" />
	</form>
</div>

<p>
	En cas de question sur le site, vous pourrez trouver ici toutes les
	ressources que nous mettons à votre disposition. Elles concernent
	principalement le bon usage de toutes les fonctionnalités du site,
	et détaillent de façon exhaustive les services que nous vous proposons.
	Si vous êtes un professionnel, vous trouverez aussi de plus amples
	informations sur ce que nous pouvons faire pour vous.
</p>

<p>
	Si vous souhaitez voir un nouveau sujet apparaitre, ou ne trouvez pas ce
	que vous cherchez, n'hésitez pas à <a href="<?php echo $view['router']->generate('zco_about_contact', array('objet' => 'Question')) ?>">nous contacter</a>.
</p>

<div style="clear: right; margin-bottom: 20px;"></div>

<p class="centre"><strong><?php echo $nombre ?> sujet<?php echo pluriel($nombre) ?></strong> correspondant à votre recherche <?php echo pluriel($nombre, 'ont', 'a') ?> été trouvé<?php echo pluriel($nombre) ?>.</p>

<?php foreach ($hits as $hit){ ?>
<div class="UI_box" style="max-width: 800px;">
	<h3><?php echo htmlspecialchars($hit->titre) ?></h3>
	<p><?php echo htmlspecialchars(substr(strip_tags($hit->contenu), 0, 500)).(strlen(strip_tags($hit->contenu) > 500) ? '…' : '') ?></p>
	<p><a href="page-<?php echo $hit->pk ?>-<?php echo rewrite($hit->titre) ?>.html">Lire en entier &rarr;</a></p>
</div>
<?php } ?>