/**
 * Script servant à sauvegarder le contenu des zForms.
 *
 * @provides vitesse-behavior-save-zform
 * @requires vitesse-behavior
 *           mootools
 */
Behavior.create('save-zform', function (config, statics) {
    var shortTime = 10000;
    var longTime = 30000;

    var saveCallback = function (config, statics) {
        var textarea = $(config.textarea);
        if (textarea.value == '') {
            resetCallback(shortTime, textarea, [config, statics]);
            return;
        }
        if (textarea.value != statics['text-' + config.textarea]) {
            statics['text-' + config.textarea] = textarea.value;
            var xhr = new Request({
                method: 'post', url: Routing.generate('zco_user_api_saveZform'), onSuccess: function (text, xml) {
                    textarea.highlight('#b3ffb3');
                }
            });
            xhr.send('texte=' + encodeURIComponent($(config.textarea).value) +
                '&url=' + encodeURIComponent(document.location.pathname));
        }
        resetCallback(longTime, textarea, [config, statics]);
    };

    var resetCallback = function (interval, obj, args) {
        saveCallback.delay(interval, obj, args);
    };
});