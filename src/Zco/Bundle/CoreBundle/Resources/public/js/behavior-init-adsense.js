/**
 * Initialisation des actions Ajax.
 *
 * @provides vitesse-behavior-init-adsense
 * @requires vitesse-behavior mootools
 */
Behavior.create('init-adsense', function(config)
{
	var pub = $('adsense_postchargement');
	if (!pub)
	{
	    return;
    }
    
	pub.inject($('adsense'));
	pub.setStyle('display', 'block');
});