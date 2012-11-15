/**
 * Compte les caractères restants lors de la rédaction d'un tweet.
 * Paramètres :
 *   - textarea_id : ID de la zone de rédaction du tweet ;
 *   - chars_id : ID de la zone d'affichage des caractères restants ;
 *   - button_id : ID du bouton d'envoi du tweet.
 * 
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 * @provides vitesse-behavior-twitter-count-characters
 * @requires vitesse-behavior
 *           jquery-no-conflict
 *           @ZcoTwitterBundle/Resources/public/js/twitter-text.js
 */
(function($) {
	Behavior.create('twitter-count-characters', function(config)
	{
		var txt = $('#' + config.textarea_id);
		var chr = $('#' + config.chars_id);
		var btn = $('#' + config.button_id);
	
		chr.show();
		chr.text(140);
		
		var over = false;
		
		txt.keyup(function()
		{
			var nb = 140 - twttr.txt.getTweetLength(txt.val());
			chr.text(nb);
	
			if (nb < 0 && !over) {
				over = true;
				chr.css('color', 'red');
				chr.css('font-weight', 'bold');
				btn.attr('disabled', true);
			}
			if (nb >= 0 && over) {
				over = false;
				chr.css('color', '');
				chr.css('font-weight', '');
				btn.attr('disabled', false);
			}
		});
	});
})(jQuery);