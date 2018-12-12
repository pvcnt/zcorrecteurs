/**
 * Script servant à sauvegarder le contenu des zForms.
 *
 * @author        vincent1870, dworkin
 */

var shortTime = 10000;
var longTime = 30000;
var saveTxt = '';

function save_zform(textarea) {
    if ($(textarea).value == '') {
        setTimeout('save_zform(\'' + textarea + '\')', shortTime);
        return;
    }
    if ($(textarea).value != saveTxt) {
        saveTxt = $(textarea).value;
        var xhr = new Request({
            method: 'post',
            url: Routing.generate('zco_user_api_saveZform'),
            onSuccess: function (text, xml) {
                $(textarea).highlight('#b3ffb3');
            }
        });
        xhr.send('id=' + textarea + '&texte=' + encodeURIComponent($(textarea).value) + '&url=' + encodeURIComponent(document.location.pathname));
    }
    setTimeout('save_zform(\'' + textarea + '\')', longTime);
}

window.addEvent('domready', function () {
    if ($chk($('texte'))) {
        setTimeout('save_zform(\'texte\')', shortTime);
    }
});
