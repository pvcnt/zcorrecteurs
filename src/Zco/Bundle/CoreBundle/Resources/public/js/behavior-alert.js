/**
 * @provides vitesse-behavior-alert
 * @requires @ZcoCoreBundle/Resources/public/js/lib/noty.js @ZcoCoreBundle/Resources/public/css/lib/noty.css @ZcoCoreBundle/Resources/public/css/lib/noty-theme.css
 */
Behavior.create('alert', function (config) {
    console.log('create alert', config);
    new Noty({
        theme: 'evergreen',
        text: config.text,
        type: config.type || 'info',
        timeout: config.timeout || false
    }).show();
});