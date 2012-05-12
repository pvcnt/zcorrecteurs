/**
 * @provides vitesse-behavior-popover
 * @requires vitesse-behavior
 *           mootools
 *           bootstrap-js
 */
Behavior.create('popover', function(config)
{
    if (!config.options)
    {
        config.options = {};
    }
    jQuery(config.selector).popover(config.options);
});