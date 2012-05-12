/**
 * Installe une fonction d'autocomplétion sur une zone de texte.
 *
 * @provides vitesse-behavior-edit-in-place
 * @requires @ZcoCoreBundle/Resources/public/js/libs/EditInPlace.js
 */
Behavior.create('edit-in-place', function(config)
{
    if (!config.options)
    {
        config.options = {};
    }
    if (!config.options.postVar)
    {
        config.options.postVar = 'data';
    }
    
    new EditInPlace(config.id, config.callback, config.options);
});