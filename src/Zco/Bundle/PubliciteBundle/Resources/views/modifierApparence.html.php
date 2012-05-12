<?php $view->extend('::layouts/default.html.php') ?>

<?php echo $view->render('ZcoPubliciteBundle::_onglets.html.php') ?>
<h1>Modifier l'apparence : <?php echo htmlspecialchars($publicite['titre']) ?></h1>

<form method="post" action="">
	<fieldset>
		<legend>Conception de votre publicité</legend>

		<table class="wrapper">
			<tr>
				<td>
					<label for="url_cible">Adresse de redirection :</label>
					<input type="text" name="url_cible" id="url_cible" size="40" value="<?php echo htmlspecialchars($publicite['url_cible']) ?>" /><br />

					<label for="titre">Nom de la publicité :</label>
					<input type="text" name="titre" id="titre" size="40" value="<?php echo htmlspecialchars($publicite['titre']) ?>" /><br />

					<div id="row_contenu">
						<label for="contenu">Contenu :</label>
						<textarea name="contenu" id="contenu" style="width: 400px; height: 70px;"><?php echo htmlspecialchars($publicite['contenu']) ?></textarea>

						<?php if (verifier('publicite_js')){ ?><br />
						<input type="checkbox" name="contenu_js" id="contenu_js" style="margin-left: 200px;"<?php if ($publicite['contenu_js']) echo ' checked="checked"' ?> />
						<label for="contenu_js" class="nofloat">Décrire le contenu en HTML + Javascript</label>
						<?php } ?>
					</div>

					<?php /*
					<div id="row_image" style="display: none;">
						<label for="image">Image :</label>
						<input type="text" name="image" id="image" size="40" value="<?php echo htmlspecialchars($publicite['image']) ?>" /><br />
					</div>*/ ?>
				</td>
				<td style="vertical-align: top; padding-left: 20px; margin: auto;">
					<strong>Prévisualisation de la publicité.</strong> <span class="gris">Le rendu final peut quelque peu différer.</span>
					<div id="preview_pub"><em>Complétez les champs pour avoir un aperçu.</em></div>
				</td>
			</tr>
		</table>
	</fieldset>

	<div class="send">
		<input type="submit" name="send" value="Sauvegarder" accesskey="s" />
	</div>
</form>

<script type="text/javascript">
$('titre').addEvent('keyup', update_pub);
$('contenu').addEvent('keyup', update_pub);
$('url_cible').addEvent('change', update_pub);
<?php if (verifier('publicite_js')){ ?>
$('contenu_js').addEvent('click', function(){
	if ($('contenu_js').checked)
	{
		$('contenu').value = get_pub_html();
		update_pub();
	}
	else
	{
		update_pub();
	}
});
<?php } ?>
document.addEvent('domready', update_pub);

function update_pub()
{
	emplacement = '<?php echo $publicite['emplacement'] ?>';

	<?php if (verifier('publicite_js')){ ?>
	if ($('contenu_js').checked)
	{
		pub = $('contenu').value;
	}
	else <?php } ?>pub = get_pub_html();

	if (emplacement == 'menu')
	{
		pub = '<div class="sidebar"><div class="bloc partenaires"><h4>Partenaires</h4><ul>'+
			pub+'</ul></div></div>';
	}
	else if (emplacement == 'pied')
	{
		pub = '<div class="footer center centre"><p class="links blanc" style="margin-top: 20px;">'+
			'Partenaires : '+pub+
			'</p></div>';
	}
	$('preview_pub').set('html', pub);
}

function get_pub_html()
{
	emplacement = '<?php echo $publicite['emplacement'] ?>';

	if (emplacement == 'menu')
	{
		pub = '<li><a href="'+$('url_cible').value+'" title="'+$('titre').value+'" rel="'+$('contenu').value+'">'+$('titre').value+'</a></li>';
	}
	else if (emplacement == 'pied')
	{
		pub = '<a href="'+$('url_cible').value+'" title="'+$('contenu').value+'">'+$('titre').value+'</a>';
	}
	return pub;
}
</script>