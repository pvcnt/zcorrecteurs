/**
 * Initialisation des effets sur les titres des éléments du menu de gauche.
 *
 * @provides vitesse-behavior-tips
 * @requires vitesse-behavior
 *           mootools
 *           mootools-more
 */
Behavior.create('tips', function(config)
{
    var options = {
		onShow: function(tip)
		{
			tip.fade('in');
		},
		onHide: function(tip)
		{
			tip.fade('out');
		}
    };
    
    if (config.options)
    {
        options = $merge(options, config.options);
    }
    
	var tipper = new Tips(config.selector, options);
	tipper.container.setStyle('zindex', 100);
	tipper.container.set('tween', {duration: 100});
});