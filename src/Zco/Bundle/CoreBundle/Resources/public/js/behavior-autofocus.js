/**
 * Met le focus dans un champ automatiquement.
 *
 * @provides vitesse-behavior-autofocus
 * @requires vitesse-behavior mootools
 */
Behavior.create('autofocus', function(config)
{
    $(config.id).focus();
});