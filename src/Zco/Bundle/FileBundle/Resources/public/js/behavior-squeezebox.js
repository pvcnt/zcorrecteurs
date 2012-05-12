/**
 * @provides vitesse-behavior-squeezebox
 * @requires mootools
 *           @ZcoFileBundle/Resources/public/js/SqueezeBox.js
 */
Behavior.create('squeezebox', function(config, statics)
{
    if (!statics.initialized)
    {
        if (!config.initialize)
        {
            config.initialize = {
                size: { x: 975, y: 500}
            };
        }
        SqueezeBox.initialize(config.initialize);
        statics.initialized = true;
    }
    
    if (!config.options)
    {
        config.options = {};
    }
    
    SqueezeBox.assign($$(config.selector), config.options); 
});
