<?php $view->extend('::layouts/default.html.php') ?>

<h1>Accueil du centre d'aide</h1>

<p>
	En cas de question concernant le site, vous pourrez trouver ici toutes les
	ressources que nous mettons à votre disposition. Elles concernent
	principalement le bon usage de toutes les fonctionnalités du site
	et détaillent de façon exhaustive les services que nous vous proposons.
	Si vous êtes un professionnel, vous trouverez aussi de plus amples
	informations sur ce que nous pouvons faire pour vous.
</p>

<p>
	Si vous souhaitez voir un nouveau sujet apparaître ou ne trouvez pas ce
	que vous cherchez, n'hésitez pas à <a href="<?php echo $view['router']->generate('zco_about_contact', array('objet' => 'Question')) ?>">nous contacter</a>.
</p>

<div style="clear: right;"></div>

<?php foreach ($categories as $cat){ ?>
<?php if (count($cat->Aide()) > 0){ ?>
<div>
	<div class="UI_underlined"><?php echo htmlspecialchars($cat['nom']) ?></div>

	<table class="UI_wrapper">
		<tr>
			<?php foreach ($cat->Aide() as $i => $sujet){ ?>
			<?php if ($i % ceil(count($cat->Aide())/3) == 0) echo ($i > 0 ? '</ul></td>' : '').'<td style="width: 33%;"><ul style="list-style-type: none;">' ?>
			<li style="margin-top: 5px;"><a href="page-<?php echo $sujet['id'] ?>-<?php echo rewrite($sujet['titre']) ?>.html">
				<?php if (!empty($sujet['icone'])){ ?>
				<img src="<?php echo htmlspecialchars($sujet['icone']) ?>" alt="" />
				<?php } ?>
				<?php echo htmlspecialchars($sujet['titre']) ?>
			</a></li>
			<?php } ?>
			</ul></td>
		</tr>
	</table>
</div>
<?php } ?>
<?php } ?>

<?php if (verifier('aide_ajouter') || verifier('aide_modifier') || verifier('aide_supprimer')){ ?>
<div class="UI_box gras centre" style="margin-top: 50px;">
	<?php if (verifier('aide_ajouter')){ ?>
	<a href="ajouter.html">Ajouter un nouveau sujet d'aide</a><br />
	<?php } ?>
	<a href="gestion.html">Gestion des sujets du centre d'aide</a>
</div>
<?php } ?>
