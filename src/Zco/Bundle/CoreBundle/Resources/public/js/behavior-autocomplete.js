/**
 * Installe une fonction d'autocomplétion sur une zone de texte.
 *
 * @provides vitesse-behavior-autocomplete
 * @requires vitesse-behavior
 *           @ZcoCoreBundle/Resources/public/js/libs/Autocompleter.Request.js
 *           @ZcoCoreBundle/Resources/public/css/libs/autocomplete.css
 */
Behavior.create('autocomplete', function(config, statics)
{
    if (!config.options)
    {
        config.options = {};
    }
    if (!config.options.postVar)
	{
		config.options.postVar = config.id;
	}
	if (!config.options.minLength)
	{
		config.options.minLength = 2;
	}
    
    new Autocompleter.Request.JSON(config.id, config.callback, config.options);
});