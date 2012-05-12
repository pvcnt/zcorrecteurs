/**
 * Script servant à sauvegarder le contenu des zForms.
 *
 * @author		vincent1870, dworkin
 */

var shortTime = 10000;
var longTime = 30000;
var saveTxt = '';
var save_textarea = null;

function save_zform(textarea)
{
	if ($(textarea).value == '')
	{
        setTimeout('save_zform(\''+textarea+'\')', shortTime);
        return;
    }
	if ($(textarea).value != saveTxt)
	{
		saveTxt = $(textarea).value;
		xhr = new Request({method: 'post', url: '/informations/ajax-save-zform.html', onSuccess: function(text, xml){
			$(textarea).highlight('#b3ffb3');
		}});
		xhr.send('texte='+encodeURIComponent($(textarea).value)+'&url='+encodeURIComponent(document.location.pathname));
	}
	setTimeout('save_zform(\''+textarea+'\')', longTime);
}

window.addEvent('domready', function(){
	if ($chk($('texte')))
	{
		setTimeout('save_zform(\'texte\')', shortTime);
	}
});
