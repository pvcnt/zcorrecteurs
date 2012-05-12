<?php $view->extend('::layouts/default.html.php') ?>

<?php $convertisseurMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'); ?>

<h1>Consultation du flux du blog</h1>

<form method="get" action="" id="config">
	<fieldset>
	<label for="categorie">Flux de la catégorie :</label>
		<select name="categorie" id="categorie" onchange="$('config').submit();">
			<option value="0" <?php if($id_cat ==  GetIDCategorie('blog')) echo ' selected="selected"'; ?>>
				Tout le blog
			</option>
			<?php echo GetListeCategories($id_cat, GetIDCategorie('blog')); ?>
		</select><br />

		<label for="annee">Année :</label>
		<select name="annee" id="annee" onchange="$('config').submit();">
			<?php for($i = 2007 ; $i <= date('Y') ; $i++){ ?>
			<option value="<?php echo $i; ?>"<?php if($i == $annee) echo ' selected="selected"'; ?>>
				<?php echo $i; ?>
			</option>
			<?php } ?>
		</select><br />

		<span>
		<label>Graphique :</label>
			<a href="?annee=<?php echo $annee; ?>&categorie=<?php echo $id_cat; ?>&periode=mois">
				<img src="/img/misc/calendrier.png" alt="Par mois" class="cliquable<?php if($periode == 'mois') echo ' selected'; ?>" />
				Par mois
			</a> |
			<a href="?annee=<?php echo $annee; ?>&categorie=<?php echo $id_cat; ?>&periode=jour">
				<img src="/img/misc/jour.png" alt="Par jour" class="cliquable<?php if($periode == 'jour') echo ' selected'; ?>" />
				Par jour
			</a>
		</span>
		<br />

		<noscript>
			<input type="submit" value="Envoyer" />
		</noscript>
	</fieldset>
</form>

<br />
<hr />

<p>
	Il y a actuellement <?php echo $NbAbonnes; ?> abonné<?php echo pluriel($NbAbonnes); ?>
	au flux sélectionné (ce nombre est basé sur les visites d'hier).
</p>

<p class="centre">
	<img src="/statistiques/graphique-blog-flux.html" alt="Consultation du flux du blog" /><br />
</p>
