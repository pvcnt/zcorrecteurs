/**
 * Installe une fonction d'autocomplétion sur une zone de texte.
 *
 * @provides vitesse-behavior-twipsy
 * @requires vitesse-behavior
 *           mootools
 *           @ZcoCoreBundle/Resources/public/js/libs/Twipsy.js
 */
Behavior.create('twipsy', function(config)
{
    if (!config.options)
    {
        config.options = {};
    }
    $$(config.selector).twipsy(config.options);
});