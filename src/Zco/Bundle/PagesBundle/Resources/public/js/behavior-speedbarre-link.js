/**
 * Affiche un camembert de répartition des dons
 *
 * @provides vitesse-behavior-dons-speedbarre-link
 * @requires vitesse-behavior
 *           mootools
 *           mootools-more
 */
Behavior.create('dons-speedbarre-link', function(config)
{
    var tipper = new Tips('#' + config.id, {
		showDelay: 400,
		fixed: true,
		offset : {'x': 0, 'y': 40},
		onShow: function(tip) {
			tip.fade('in');
		},
		onHide: function(tip) {
			tip.fade('out');
		}
	});
	tipper.container.style.zIndex = 100;
	tipper.container.set('tween', {duration: 100});
	
	$(config.id).addEvent('mouseover', function()
	{
	    (function()
	    {
	        $$('#' + config.id + ' span').setStyle('display', 'inline');
	        $(config.id).setStyle('background', '#666');
	    }).delay(500);
	});
	
	$(config.id).addEvent('mouseout', function()
	{
	    //$$('#' + config.id + ' span').setStyle('display', 'none');
	    $(config.id).setStyle('background', '');
	});
});