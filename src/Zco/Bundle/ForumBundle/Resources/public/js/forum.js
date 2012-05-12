/**
 * Permet de changer l'odre des catégories pour chaque utilisateur.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
*/

window.addEvent('domready', function() {
	var addSlide = function() {
		// Enregistrer / Restaurer la structure
		var disposeCats = function(parent) {
			var cats = [];
			var cat = parent;
			while(true) {
				cat = cat.nextSibling;
				if(!cat)
					break;
				if(cat.nodeName == 'TR') {
					if(cat.className != 'sous_cat')
						break;
					// Enregistrer une copie et marquer l'élément pour suppression
					c = cat.clone();
					c.toDelete = cat;
					cats.push(c);
				}
			}
			parent.cats = cats.reverse();

			// Supprimer les catégories marquées
			cats.each(function(e) {
				if(e.toDelete) {
					e.toDelete.dispose();
					e.toDelete = undefined;
				}
			});
		}
		var injectCats = function(cat) {
			var pos = 0;
			if(cat.cats)
				cat.cats.each(function(e) {
					e.inject(cat, 'after');
				});
			cat.cats = undefined;
		}

		// Changer l'ordre
		var list = null;
		var makeSortable = function() {
			list = new Sortables('.liste_cat tbody', {
				constrain: true,
			});
			$$('.liste_cat tr.grosse_cat').each(function(el) {
				el.style.cursor = 'ns-resize';
			});
			$$('.liste_cat tr.grosse_cat a').each(function(el) {
				el.href2 = el.href;
				el.removeAttribute('href');
			});
		}
		var removeSortable = function() {
			list.detach();
			$$('.liste_cat tr.grosse_cat').each(function(el) {
				el.style.cursor = '';
			});
			$$('.liste_cat tr.grosse_cat a').each(function(el) {
				el.href = el.href2;
			});
		}


		// Liste des ids des éléments, dans l'ordre
		var getItemsIds = function() {
			var it = list.serialize().toString().split(',');
			var items = [];
			var j = 0;
			for(var i = 0; i < it.length; i++)
				if(it[i] != '')
					items[j++] = it[i];
			return items;
		}

		// Enregistrement de l'ordre
		var saveOrder = function() {
			var data = getItemsIds().join(',');
			var xhr = new Request({
				method: 'post',
				url: '/forum/ajax-ordre.html'
			});
			xhr.send('ordre='+data);
		}
		var restoreOrder = function(data) {
			if(!data)
				return;
			var order = data.split(',');
			$$('.liste_cat tr.grosse_cat').each(disposeCats);
			var cats = [];
			var j = -1;
			var parent = undefined;
			$$('.liste_cat tr.grosse_cat').each(function(e) {
				if(!parent)
					parent = e.parentNode;
				cats[++j] = e.clone(true, true);
				cats[j].cats = e.cats;
				e.dispose();
			});

			for(var i = 0; i < order.length; i++)
				for(var k = 0; k < cats.length; k++) {
					if(cats[k].id == order[i]) {
						cats[k].inject(parent);
						cats.splice(k, 1);
						k = cats.length;
					}
				}
			// Catégories n'étant pas dans la liste ordonnée
			for(var i = 0; i < cats.length; i++)
				cats[i].inject(parent);

			$$('.liste_cat tr.grosse_cat').each(injectCats);
		}

		// Clic sur le lien
		var hideCats = function(e) {
			e.stop();
			$$('.liste_cat tr.grosse_cat').each(disposeCats);

			var link = $('make_sortable');
			link.style.color = 'red';
			link.style.fontWeight = 'bold';
			link.firstChild.nodeValue = 'Enregistrer l\'ordre';
			link.removeEvent('click', hideCats);
			link.addEvent('click', showCats);

			makeSortable();
		}
		var showCats = function(e) {
			e.stop();
			$$('.liste_cat tr.grosse_cat').each(injectCats);

			var link = $('make_sortable');
			link.style.color = '';
			link.style.fontWeight = '';
			link.firstChild.nodeValue = 'Modifier l\'ordre';
			link.removeEvent('click', showCats);
			link.addEvent('click', hideCats);

			removeSortable();
			saveOrder();
		}

		// Ajout du lien
		var link = new Element('a', {
			'id':	'make_sortable',
			'href':	'#',
			'text': 'Modifier l\'ordre'
		}).inject(new Element('li').inject(
			$$('.options_forum ul')[0]
		));
		link.addEvent('click', hideCats);

		// Restauration de l'ordre enregistré
		if(_forums_ordre)
			restoreOrder(_forums_ordre);
	}
	addSlide();
});
