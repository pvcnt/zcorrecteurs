/**
 * Initialisation de l'effet sur le lien de retour en haut de page.
 *
 * @provides vitesse-behavior-morph-link
 * @requires vitesse-behavior
 *           mootools
 *           mootools-more
 */
Behavior.create('morph-link', function(config)
{
	var topLink = $(config.id);
	if (!topLink)
	{
		return;
	}
	
	var fxTopLink = new Fx.Morph(topLink, {duration:150, transition: Fx.Transitions.Back.easeInOut});
	topLink.addEvent('click', function(event)
	{
		event.stop();
		new Fx.Scroll(window, {duration:1000, transition: Fx.Transitions.Quart.easeOut}).toTop();
	});
});