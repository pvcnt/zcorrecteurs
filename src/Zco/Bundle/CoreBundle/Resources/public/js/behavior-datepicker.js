/**
 * @provides vitesse-behavior-datepicker
 * @requires vitesse-behavior
 *           mootools
 *           @ZcoCoreBundle/Resources/public/js/libs/DatePicker.js
 *           @ZcoCoreBundle/Resources/public/css/datepicker_vista.css
 */
Behavior.create('datepicker', function(config)
{
	new DatePicker('#' + config.id, Object.merge({
		pickerClass: 'datepicker_vista',
		days: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
		months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
	}, config.options));
});