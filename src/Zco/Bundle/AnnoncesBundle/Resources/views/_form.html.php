<form method="post" action="">
	<fieldset>
		<legend>Propriétés de l'annonce</legend>
		<label for="nom">Nom :</label>
		<input type="text" name="nom" id="nom" size="40" value="<?php if (isset($annonce)) echo htmlspecialchars($annonce['nom']) ?>" /><br />
		
		<label for="date_debut">Date de début d'affichage :</label>
		<?php echo $view->get('widget')->dateTimePicker('date_debut', isset($annonce) ? $annonce['date_debut'] : date('Y-m-d H:i')) ?><br />
		
		<label for="date_fin">Date de fin d'affichage :</label>
		<?php echo $view->get('widget')->dateTimePicker('date_fin', !empty($annonce['date_fin']) ? $annonce['date_fin'] : null, array('allowEmpty' => true)) ?>
		<em>Laissez vide pour jamais.</em><br />
		
		<label for="poids">Poids :</label>
		<select name="poids" id="poids">
			<?php for ($i = 0 ; $i <= 100 ; $i += 5): ?>
			<option value="<?php echo $i ?>"<?php if ((isset($annonce) && $annonce['poids'] == $i) || (empty($annonce) && $i == 50)) echo ' selected="selected"' ?>>
				<?php echo $i ?>
			</option>
			<?php endfor; ?>
		</select>
		
		<?php if (verifier('annonces_publier')): ?><br />
		<label for="actif">Active :</label>
		<input type="checkbox" name="actif" id="actif"<?php if (isset($annonce) && $annonce['actif']) echo ' checked="checked"' ?> />
		<em>Seules les annonces actives sont affichées.</em>
		<?php endif; ?><br />

		<label for="cibler_domaines">Cibler l'annonce par domaine :</label>
		<input type="checkbox" name="cibler_domaines" id="cibler_domaines" onclick="$('row_cibler_domaines').toggle();"<?php if (!empty($attrDomaines)) echo ' checked="checked"' ?> />

		<div id="row_cibler_domaines" style="margin-left: 200px;">
			<select name="domaines[]" id="domaines" multiple="multiple" size="5" style="min-width: 200px;">
				<?php foreach ($domaines as $domaine): ?>
				<option value="<?php echo $domaine ?>"<?php if (!isset($attrDomaines) || in_array($domaine, $attrDomaines)) echo ' selected="selected"' ?>>
					<?php echo $domaine ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		
		<label for="cibler_groupes">Cibler l'annonce par groupe :</label>
		<input type="checkbox" name="cibler_groupes" id="cibler_groupes" onclick="$('row_cibler_groupes').toggle();"<?php if (!empty($attrGroupes)) echo ' checked="checked"' ?> />

		<div id="row_cibler_groupes" style="margin-left: 200px;">
			<select name="groupes[]" id="groupes" multiple="multiple" size="5" style="min-width: 200px;">
				<?php foreach ($groupes as $groupe){ ?>
				<option value="<?php echo $groupe['id'] ?>"<?php if (!isset($attrGroupes) || in_array($groupe['id'], $attrGroupes)) echo ' selected="selected"' ?>>
					<?php echo htmlspecialchars($groupe['nom']) ?>
				</option>
				<?php } ?>
			</select>
		</div>

		<label for="cibler_categories">Cibler l'annonce par section :</label>
		<input type="checkbox" name="cibler_categories" id="cibler_categories" onclick="$('row_cibler_categories').toggle();"<?php if (!empty($attrCategories)) echo ' checked="checked"' ?> />

		<div id="row_cibler_categories" style="margin-left: 200px;">
			<select name="categories[]" id="categories" multiple="multiple" size="5" style="min-width: 200px;">
				<?php foreach ($categories as $categorie){ ?>
				<option value="<?php echo $categorie['id'] ?>"<?php if (!isset($attrCategories) || in_array($categorie['id'], $attrCategories)) echo ' selected="selected"' ?>>
					<?php echo htmlspecialchars($categorie['nom']) ?>
				</option>
				<?php } ?>
			</select>
		</div>

		<label for="cibler_pays">Cibler l'annonce par pays :</label>
		<input type="checkbox" name="cibler_pays" id="cibler_pays" onclick="$('row_cibler_pays').toggle();"<?php if (!empty($attrPays)) echo ' checked="checked"' ?> />

		<div id="row_cibler_pays" style="margin-left: 200px;">
			<select name="pays[]" id="pays" multiple="multiple" size="5" style="min-width: 200px;">
				<?php foreach ($pays as $p){ ?>
				<option value="<?php echo $p['id'] ?>"<?php if (!isset($attrPays) || in_array($p['id'], $attrPays)) echo ' selected="selected"' ?>>
					<?php echo htmlspecialchars($p['nom']) ?>
				</option>
				<?php } ?>
			</select><br />

			<input type="checkbox" name="aff_pays_inconnu" id="aff_pays_inconnu" class="nofloat"<?php if (!isset($annonce) || $annonce['aff_pays_inconnu']) echo ' checked="checked"' ?> />
			<label for="aff_pays_inconnu" class="nofloat">Afficher la publicité quand il est impossible de déterminer le pays de provenance.</label>
		</div>
	</fieldset>
	
	<fieldset>
		<legend>Apparence de l'annonce</legend>
		<input type="button" onclick="previsualiserAnnonce();" value="&larr; Prévisualiser" style="float: right;" />
		<div id="previsualisation" style="margin-right: 100px;"><?php if (isset($annonce)) echo $annonce->genererHTML() ?></div>
		
		<div style="clear: right;">&nbsp;</div>
		<textarea style="margin-top: 10px;" name="contenu" id="contenu"><?php if (isset($annonce)) echo htmlspecialchars($annonce['contenu']) ?></textarea>
		
		<p>
			<strong>Insérer :</strong>
			<a href="#" onclick="$('contenu').insertAroundCursor(contenus['banniere_bleue']); return false;">bannière bleue</a>,
			<a href="#" onclick="$('contenu').insertAroundCursor(contenus['banniere_jaune']); return false;">bannière jaune</a>,
			<a href="#" onclick="$('contenu').insertAroundCursor(contenus['url_destination']); return false;">adresse de redirection</a>,
			<a href="#" onclick="$('contenu').insertAroundCursor(contenus['croix_fermeture']); return false;">croix de fermeture</a>, 
			<a href="#" onclick="$('contenu').insertAroundCursor(contenus['retour_exp']); return false;">retour utilisateur</a>.
		</p>
		
		<p style="margin-left: 50px;">
			Le marqueur <em>%url%</em> représente l'adresse finale de redirection de votre annonce.
			Utilisé en conjonction avec le compteur de clics, cela vous permet de spécifier une ou
			plusieurs adresses ci-dessous. L'une d'entre elles sera sélectionnée au moment de la 
			redirection, chacune avec la même probabilité. Vous pouvez utiliser le marqueur 
			<em>%page%</em> dans les adresses, il sera remplacé par le chemin vers la page où a été 
			cliquée l'annonce.
		</p>
		
		<div style="margin-left: 50px;">
			<label for="url_destination">Adresse(s) de redirection :</label>
			<input type="text" name="url_destination" id="url_destination" size="60" value="<?php if (isset($annonce)) echo htmlspecialchars($annonce['url_destination']) ?>" />
		</div>
	</fieldset>

	<div class="send">
		<input type="submit" value="Envoyer" />
	</div>
</form>

<script type="text/javascript">
var contenus = {
	'banniere_bleue':  {'before': '<div class="annonce" style="border: 1px solid #00aeff; background-color: #efefff; font-size: 1.1em; text-align: center;">', 'after': '</div>', 'defaultMiddle': 'Contenu de l\'annonce'},
	'banniere_jaune':  {'before': '<style type="text/css">'+"\n\t"+'.annonce .lien { color: black; }'+"\n\t"+'.annonce .lien:hover { text-decoration: underline; }'+"\n"+'</style>'+"\n"+'<div class="annonce" style="border: 1px solid #f5d779; background-color: #f6ebc8; font-size: 1.1em; text-align: center;">', 'after': '</div>', 'defaultMiddle': 'Contenu de l\'annonce'},
	'url_destination': {'before': '<a href="%url%">', 'after': '</a>', 'defaultMiddle': ''},
	'croix_fermeture': {'before': '<a href="%fermer%">', 'after': '</a>', 'defaultMiddle': '<img src="/img/supprimer2.png" alt="Fermer l\'annonce" title="Fermer l\'annonce" />'},
	'retour_exp':      {'before': '<a href="/evolution/retour-experience.html" onclick="window.open(this.href, \'retour\', \'width=800, height=500, status=no, location=no, menubar=no, scrollbars=yes\'); return false;">', 'after': '</a>', 'defaultMiddle': ''}
};

function previsualiserAnnonce()
{
	xhr = new Request({method: 'post', url: '/annonces/ajax-previsualiser.html',
		onSuccess: function(text, xml){
			$('previsualisation').set('html', text);
		}
	});
	xhr.send('<?php if (isset($annonce)) echo 'id='.$annonce['id'].'&' ?>contenu='+$('contenu').get('value'));
}

window.addEvent('domready', function(){
	<?php if (!isset($annonce) || empty($attrCategories)){ ?>
	$('row_cibler_categories').toggle('out');
	<?php } if (!isset($annonce) || empty($attrPays)){ ?>
	$('row_cibler_pays').toggle('out');
	<?php } if (!isset($annonce) || empty($attrGroupes)){ ?>
	$('row_cibler_groupes').toggle('out');
	<?php } if (!isset($annonce) || empty($attrDomaines)){ ?>
	$('row_cibler_domaines').toggle('out');
	<?php } ?>
});
</script>