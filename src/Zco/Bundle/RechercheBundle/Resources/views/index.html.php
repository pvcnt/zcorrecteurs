<?php $view->extend('::layouts/default.html.php') ?>

<h1>Recherche avancée</h1>

<p>
	Cette page vous permet de disposer de toutes les options du moteur de
	recherche intégré au site. Vous pouvez actuellement effectuer une recherche
	parmi les sujets du forum, les billets publiés sur notre blog ou les <em>tweets</em>.
</p>

<p><a href="/aide/page-8-recherche.html">
    <img src="/img/misc/aide.png" alt="" />
    Plus d'informations sur la recherche.
</a></p>

<form method="get" action="index.html">
	<fieldset style="width: 45%; float: right;">
		<legend class="vertf">Champs facultatifs</legend>

		<label for="nb_resultats">Nombre de résultats par page :</label>
		<select name="nb_resultats" id="nb_resultats">
			<?php for($i = 5 ; $i <= 50 ; $i += 5){ ?>
			<option value="<?php echo $i; ?>"
				<?php if($i == $_flags['nb_resultats']) echo ' selected="selected"';
				?>><?php echo $i; ?></option>
			<?php } ?>
		</select><br />

		<div id="options_auteur"
		<?php if(!in_array($_flags['section'], array('twitter', 'forum'))) echo ' style="display: none;"';
		?>>
			<label for="auteur">Auteur :</label>
			<input type="text" name="auteur" id="auteur" value="<?php
			if(isset($_flags['auteur'])) echo htmlspecialchars($_flags['auteur']);
			?>" />
			
    		<?php $view['javelin']->initBehavior('autocomplete', array(
    		    'id' => 'auteur', 
    		    'callback' => $view['router']->generate('zco_user_api_searchUsername'),
    		    'options' => array('postVar' => 'pseudo'),
    		)) ?>
		</div>

		<div id="options_forum"
		<?php if($_flags['section'] != 'forum') echo ' style="display: none;"';
		?>>
			<input type="checkbox" id="resolu" name="resolu"<?php
			if(isset($_flags['resolu']) && $_flags['resolu']) echo ' checked="checked"'; ?> />
			<label for="resolu" class="nofloat">Ne retourner que les sujets résolus</label><br />

			<input type="checkbox" id="ferme" name="ferme"<?php
			if(isset($_flags['ferme']) && $_flags['ferme']) echo ' checked="checked"'; ?> />
			<label for="ferme" class="nofloat">Ne retourner que les sujets fermés</label>
		</div>

		<div id="options_blog"<?php if($_flags['section'] != 'blog') echo ' style="display: none;"'; ?>>
		</div>

		<div id="options_twitter"<?php if($_flags['section'] != 'twitter') echo ' style="display: none;"'; ?>>
		</div>
	</fieldset>

	<fieldset style="width: 45%;">
		<legend class="rouge">Champs obligatoires</legend>

		<label for="recherche">Votre recherche :</label>
		<input type="text" name="recherche" id="recherche" size="45" value="<?php
			if(isset($_GET['recherche'])) echo htmlspecialchars($_GET['recherche'])
			?>" /><br />

		<label for="mode">Mode de recherche :</label>
		<select name="mode" id="mode">
			<option value="tous"<?php
			if(isset($_flags['mode']) && $_flags['mode'] == 'tous') echo ' selected="selected"';
				?>>Tous les mots</option>
			<option value="un"<?php
			if(isset($_flags['mode']) && $_flags['mode'] == 'un') echo ' selected="selected"';
				?>>N'importe quel mot</option>
			<option value="phrase"<?php
			if(isset($_flags['mode']) && $_flags['mode'] == 'phrase') echo ' selected="selected"';
				?>>Phrase exacte</option>
		</select><br />

		<label for="section">Sections du site où effectuer la recherche :</label>
		<select name="section" id="section" onchange="switch_cat(this.value);">
			<option value="forum"<?php
			if($_flags['section'] == 'forum') echo ' selected="selected"';
			?>>
				Dans les sujets du forum
			</option>
			<option value="blog"<?php
			if($_flags['section'] == 'blog') echo ' selected="selected"';
			?>>
				Dans les billets du blog
			</option>
			<option value="twitter"<?php
			if($_flags['section'] == 'twitter') echo ' selected="selected"';
			?>>
				Dans les tweets
			</option>
		</select><br />

		<select name="categories[]" id="cats_forum" multiple="multiple"<?php
		if($_flags['section'] != 'forum') echo ' style="display: none;"';
		?>>
			<?php foreach($CatsForum as $cat){ ?>
			<option value="<?php echo $cat['cat_id']; ?>"<?php
			if(isset($_GET['categories']) && in_array($cat['cat_id'], $_GET['categories']))
			echo ' selected="selected"'; ?>>
				<?php for($i = 2 ; $i <= $cat['cat_niveau'] ; $i++) echo '.....'; ?>
				<?php echo htmlspecialchars($cat['cat_nom']); ?>
			</option>
			<?php } ?>
		</select>

		<select name="categories[]" id="cats_blog" multiple="multiple"<?php
		if($_flags['section'] != 'blog') echo ' style="display: none;"'; ?>>
			<?php foreach($CatsBlog as $cat){ ?>
			<option value="<?php echo $cat['cat_id']; ?>"<?php
			if(isset($_GET['categories']) && in_array($cat['cat_id'], $_GET['categories']))
			echo ' selected="selected"'; ?>>
				<?php for($i = 2 ; $i <= $cat['cat_niveau'] ; $i++) echo '.....'; ?>
				<?php echo htmlspecialchars($cat['cat_nom']); ?>
			</option>
			<?php } ?>
		</select>

		<select name="categories[]" id="cats_twitter" multiple="multiple" style="<?php
		if($_flags['section'] != 'twitter') echo 'display: none;'; ?> width: 200px;">
			<?php foreach($CatsTwitter as $cat){ ?>
			<option value="<?php echo $cat['id']; ?>"<?php
			if(isset($_GET['categories']) && in_array($cat['id'], $_GET['categories']))
			echo ' selected="selected"'; ?>>
				<?php echo htmlspecialchars($cat['nom']); ?>
			</option>
			<?php } ?>
		</select>
		<p><em>Vous pouvez sélectionner plusieurs catégories en maintenant CTRL ou MAJ enfoncée.</em></p>
	</fieldset>

	<div class="centre">
		<input type="submit" name="submit" value="Envoyer" />
	</div>
</form>

<?php if (isset($Resultats)): ?>
	<?php if ($Resultats === array()): ?>
		<p>Aucun résultat n'a été trouvé.</p>
	<?php else: ?>
		<p class="gras centre" id="resultats">
			<?php echo $CompterResultats; ?> résultat<?php echo pluriel($CompterResultats); ?>
			<?php echo pluriel($CompterResultats, 'ont', 'a'); ?>
			été trouvé<?php echo pluriel($CompterResultats); ?>.
		</p>

		<?php
			echo $view->render('ZcoRechercheBundle::_'.$_flags['section'].'.html.php',
				array('Resultats' => $Resultats,
				      'Pages' => $pages
			));
		?>
	<?php endif ?>
<?php endif ?>

<script type="text/javascript">
function switch_cat(cat)
{
	var cfrm = $('cats_forum');
	var cblg = $('cats_blog');
	var ctwt = $('cats_twitter');
	if(cat == 'forum')
	{
		for (var i = cblg.options.length - 1; i >= 0; i--)
			cblg.options[i].selected = false;
		for (var i = ctwt.options.length - 1; i >= 0; i--)
			ctwt.options[i].selected = false;

		cfrm.setStyle('display', 'block');
		cfrm.setStyle('margin-left', '200px');
		cblg.setStyle('display', 'none');
		ctwt.setStyle('display', 'none');
		$('options_forum').setStyle('display', 'block');
		$('options_auteur').setStyle('display', 'block');
		$('options_blog').setStyle('display', 'none');
		$('options_twitter').setStyle('display', 'none');
	}
	if(cat == 'blog')
	{
		for (var i = cfrm.options.length - 1; i >= 0; i--)
			cfrm.options[i].selected = false;
		for (var i = ctwt.options.length - 1; i >= 0; i--)
			ctwt.options[i].selected = false;

		cfrm.setStyle('display', 'none');
		cblg.setStyle('display', 'block');
		cblg.setStyle('margin-left', '200px');
		ctwt.setStyle('display', 'none');
		$('options_forum').setStyle('display', 'none');
		$('options_blog').setStyle('display', 'block');
		$('options_auteur').setStyle('display', 'none');
		$('options_twitter').setStyle('display', 'none');
	}
	if(cat == 'twitter')
	{
		for (var i = cfrm.options.length - 1; i >= 0; i--)
			cfrm.options[i].selected = false;
		for (var i = cblg.options.length - 1; i >= 0; i--)
			cblg.options[i].selected = false;

		cfrm.setStyle('display', 'none');
		cblg.setStyle('display', 'none');
		ctwt.setStyle('display', 'block');
		ctwt.setStyle('margin-left', '200px');
		$('options_forum').setStyle('display', 'none');
		$('options_blog').setStyle('display', 'none');
		$('options_twitter').setStyle('display', 'block');
		$('options_auteur').setStyle('display', 'block');
	}
}
</script>
