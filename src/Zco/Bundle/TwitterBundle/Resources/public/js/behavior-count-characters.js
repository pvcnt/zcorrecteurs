/**
 * @provides vitesse-behavior-twitter-count-characters
 * @requires vitesse-behavior
 *           mootools
 */

/**
 * Compte les caractères restants lors de la rédaction d'un tweet.
 * Paramètres :
 *   - textarea_id : ID de la zone de rédaction du tweet ;
 *   - chars_id : ID de la zone d'affichage des caractères restants ;
 *   - button_id : ID du bouton d'envoi du tweet.
 * 
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
Behavior.create('twitter-count-characters', function(config)
{
	var txt = document.id(config.textarea_id);
	var chr = document.id(config.chars_id);
	var btn = document.id(config.button_id);

	chr.setStyle('display', '');
	chr.set('text', 140);

	var over = false;
	txt.addEvent('keyup', function() {
		var nb = 140 - txt.value.length;
		chr.set('text', nb);

		if(nb < 0 && !over) {
			over = true;
			chr.setStyles({
				'color': 'red',
				'font-weight': 'bold'
			});
			btn.disabled = true;
			btn.fade('out');
		}
		if(nb >= 0 && over) {
			over = false;
			chr.setStyles({
				'color': '',
				'font-weight': ''
			});
			btn.disabled = false;
			btn.fade('in');
		}
	});
});