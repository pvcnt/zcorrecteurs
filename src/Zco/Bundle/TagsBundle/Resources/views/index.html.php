<?php $view->extend('::layouts/default.html.php') ?>

<h1>Liste des mots clés</h1>
<p>
	Les mots clés sont des mots brefs pouvant être associés à diverses composantes
	du site (billets sur le blog, sujet sur le forum, etc.). Vous visualisez ici
	la liste de tous les mots clés utilisés sur le site. Pour en créer un nouveau,
	il suffit de l'attibuer à un billet ou un sujet par exemple, et il sera
	automatiquement répertorié ici.
</p>

<p>
	<?php foreach($Tags as $tag){ ?>
	<a href="tag-<?php echo $tag['id']; ?>-<?php echo rewrite($tag['nom']); ?>.html">
		<?php echo htmlspecialchars($tag['nom']); ?>
	</a>&nbsp;&nbsp;
	<?php } ?>
</p>