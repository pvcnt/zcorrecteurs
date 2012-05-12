/**
 * Légères améliorations graphiques du module de dictées.
 *
 * @author mwsaz
*/

window.addEvent('domready', function() {
	$$('.collapse').each(function(e) {
		var ch = e.nextSibling.nextSibling;
		ch.fade('hide');

	});
});
