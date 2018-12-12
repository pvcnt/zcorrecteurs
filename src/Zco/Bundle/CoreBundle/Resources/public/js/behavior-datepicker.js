/**
 * @provides vitesse-behavior-datepicker
 * @requires vitesse-behavior
 *           mootools
 *           @ZcoCoreBundle/Resources/public/js/DatePicker.js
 *           @ZcoCoreBundle/Resources/public/css/datepicker_vista.css
 */
Behavior.create('datepicker', function(config)
{
	new DatePicker('#' + config.id, {
		pickerClass: 'datepicker_vista',
		days: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
		months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
		timePicker: true,
		format: 'd/m/Y à H:i',
		inputOutputFormat: 'Y-m-d H:i:s',
		toggleElements: false,
		allowEmpty: false
	});
});