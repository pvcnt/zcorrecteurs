/**
 * Configuration un formulaire pour fonctionner en mode asynchrone.
 *
 * @provides vitesse-behavior-ajax-link
 * @requires vitesse-behavior
 *           mootools
 *           @ZcoCoreBundle/Resources/public/js/libs/zMessage.js
 */
Behavior.create('ajax-link', function(config, statics)
{
    var elem = $(config.id);
    if (!elem)
    {
        return;
    }
    
	elem.addEvent('click', function(e)
	{
		e.stop();
		xhr = new Request({method: 'get', url: elem.href,
			onSuccess: function(text)
			{
				json = JSON.decode(text);
				zMessage.display(json.msg, json.type);
				/*if ($type(json.data) == 'string' && $type(json.exec) == 'string') {
					data = JSON.decode(json.data);
					$exec(json.exec);
				}*/
			}
		});
		xhr.send();
	});
});