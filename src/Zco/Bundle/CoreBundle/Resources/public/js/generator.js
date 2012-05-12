/**
 * Améliorations graphiques / ergonomie des pages générées par Generator.
 *
 * @author mwsaz
 */

window.addEvent('domready', function() {
	var filtres = $('generator-filters');
	if(filtres) {
		filtres.slide('hide');
		var filtrer = new Element('input', {
			'type': 'button',
			'value': 'Afficher les filtres'
		}).inject('generator-rechercher', 'after');
		filtrer.setStyle('float', 'right');
		filtrer.addEvent('click', function() {
			filtrer.fade('out');
			filtres.slide('toggle');
		});
	}
});
