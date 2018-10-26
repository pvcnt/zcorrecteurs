/**
 * Fichier devant contenir tous les outils Javascript succeptibles d'être
 * appelés depuis toutes les pages du site.
 *
 * @author	 dworkin, vincent1870, mwsaz
 * @requires jquery-no-conflict mootools mootools-more
 */
jQuery(function() {
	// Initialize sliding blocks.
    jQuery('.UI_rollbox .hidden').hide();
    jQuery('.UI_rollbox .title').click(function(e) {
        e.stopPropagation();
        jQuery(this).siblings('.hidden').slideToggle(400);
    });

	var sortImg = {
		'up':   '/bundles/zcocore/img/generator/arrow-up.gif',
		'down': '/bundles/zcocore/img/generator/arrow-down.gif',
		'none': '/bundles/zcocore/img/generator/arrow-updown.gif'
	};

	var accents = [
		'ß','à','á','â','ã','ä','å','æ','ç',
		'è','é','ê','ë','ì','í','î','ï','ð',
		'ñ', 'ò','ó','ô','õ','ö','ø',
		'ù','ú','û','ü','ý','ý','þ','ÿ'
	];
	var replaceWith = [
		's', 'a','a','a','a','a','a','a','c',
		'e','e','e','e','i','i','i','i','d',
		'n', 'o','o','o','o','o','o',
		'u','u','u','u','y','y','b','y'
	];

	var removeAccents = function(str) {
		for (var i = 0; i < accents.length; i++)
			while (str.indexOf(accents[i]) >= 0)
				str = str.replace(accents[i], replaceWith[i]);
		return str;
	};

	var sortTable = function(table, index, dir) {
		var dom_lignes = table.getElementsByTagName('tr');
		var lignes = [];

		for (var i = 0; i < dom_lignes.length; i++)
			lignes[lignes.length] = dom_lignes[i];

		var order = [];
		for (var i = 0; i < lignes.length; i++) {
			if (lignes[i].getElementsByTagName('th').length)
				continue;

			var cell = lignes[i].getElementsByTagName('td');
			if (index >= cell.length)
				continue;
			cell = cell[index];

			order.push([i, removeAccents(cell.get('text').toLowerCase())]);
		}
		order.sort(function(a, b) {
			return (a[1] < b[1] ? -1 : (a[1] == b[1] ? 0 : 1))
				* (dir == 'up' ? 1 : -1);
		});

		for (var i = 0; i < order.length; i++)
			table.appendChild(lignes[order[i][0]]);
	};


	$$('table.tab_user').each(function(tableau) {
		tableau.sorted = null;

		tableau.getElements('th').each(function(header) {
			var img = new Element('img', {
				'src':   sortImg['none'],
				'class': 'tabsort',
				'style': 'padding-right: 5px; opacity: 0.3'
			}).inject(header, 'top');

			header.addEvent('mouseenter', function() {
				img.setStyle('opacity', '1');
			});
			header.addEvent('mouseleave', function() {
				if (!tableau.sorted || tableau.sorted[0] !== header)
					img.setStyle('opacity', '0.3');
			});
			header.setStyle('cursor', 'pointer');

			header.addEvent('click', function() {
				var headers = header.parentNode.getElementsByTagName('th');
				var index = 0;
				for (; index < headers.length && headers[index] !== header; index++);

				if (tableau.sorted) {
					if (tableau.sorted[0] == header) {
						var dir = tableau.sorted[1] == 'up' ? 'down' : 'up';
						tableau.sorted[1] = dir;
						tableau.sorted[0].getElement('img.tabsort').set('src', sortImg[dir]);
						sortTable(tableau, index, dir);
						return;
					}
					else {
						tableau.sorted[0].getElements('img.tabsort')
							.set('src', sortImg['none'])
							.setStyle('opacity', '0.3');
						tableau.sorted = null;
					}
				}

				tableau.sorted = [header, 'up'];
				sortTable(tableau, index, 'up');
				tableau.sorted[0].getElement('img.tabsort').set('src', sortImg['up']);
			});
		});
	});
});

/**
 * Gestion des spoilers (nécessaires pour le zCode).
 */
function switch_spoiler(a)
{
	if(a.nodeName == 'DIV' && a.className == 'spoiler2')
	{
		var b = a.getElementsByTagName('div')[0];

		if (b.style.visibility == 'visible')
			b.style.visibility = 'hidden';
		else
			b.style.visibility = 'visible';
		return false;
	}
	switch_spoiler(a.parentNode.nextSibling);
	return false;
}

function switch_spoiler_hidden(a)
{
	var b = a.parentNode.nextSibling.getElementsByTagName('div');
	b = b[0];
	if (b.style.display == 'block')
		b.style.display = 'none';
	else
		b.style.display = 'block';
	return false;
}