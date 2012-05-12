/**
 * Initialisation des blocs déroulants.
 *
 * @provides vitesse-behavior-init-sliding-blocks
 * @requires vitesse-behavior
 *           mootools
 *           mootools-more
 */
Behavior.create('init-sliding-blocks', function(config)
{
	var blocs_titre = $$('.UI_rollbox .title');
	var blocs_deroulant = $$('.UI_rollbox .hidden');
	var time = 400;
	var blocs_sliders = [];

	/* Création des sliders */
	blocs_deroulant.each(function(elem, i)
	{
		blocs_sliders.push(new Fx.Slide(elem, {duration: 200, transition: Fx.Transitions.ExpoEaseIn}));
		blocs_sliders[i].hide();
	});

	blocs_titre.each(function(elem, i)
	{
		elem.addEvent('click', function(e)
		{
			e.stop();
			blocs_sliders[i].toggle();
		});
	});
});