/**
 * Configuration un formulaire pour fonctionner en mode asynchrone.
 *
 * @provides vitesse-behavior-ajax-form
 * @requires vitesse-behavior 
 *           mootools
 *           @ZcoCoreBundle/Resources/public/js/zMessage.js
 */
Behavior.create('ajax-form', function(config, statics)
{
    var elem = $(config.id);
    if (!elem)
    {
        return;
    }
    
	elem.addEvent('submit', function(e)
	{
	    e.stop();
        var action = elem.action;
        xhr = new Request({method: elem.method, url: action,
        	onSuccess: function(text)
        	{
        		json = JSON.decode(text);
        		zMessage.display(json.msg, json.type);
        		/*if ($type(json.data) == 'string' && $type(json.exec) == 'string')
        		{
        			data = JSON.decode(json.data);
        			$exec(json.exec);
        		}*/
        	}
        });
        xhr.send(elem.toQueryString());
	});
});