/**
 * Interactivité des formulaires générés par la classe Form.
 *
 * @author vincent1870, mwsaz
 */

window.addEvent('domready', function() {
	var helpText = null;
	$$('.help_text').each(function(element) {
		element.parentNode.addEvent('mouseover', function() {
			if(helpText && element.uid != helpText.uid)
				helpText.fade('out');
			element.fade('in');
			helpText = element;
		})
		element.fade('hide');
	});

	$$('.collapse').each(function(elem, i) {
		elem.getElementsByTagName('div')[0].slide('hide');
	});
	$$('.collapse h2').each(function(elem, i) {
		elem.set('html', elem.get('html')
			+ ' (<a href="#" onclick="this.parentNode.parentNode.getElementsByTagName'
			+ '(\'div\')[0].slide(\'show\');return false;">Afficher</a>)'
		);
	});
});
