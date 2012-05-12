/**
 * @provides vitesse-behavior-validate-value
 * @requires vitesse-behavior
 *           mootools
 */
Behavior.create('validate-value', function(config, statics)
{
	var input = document.id(config.id);
	if (!input)
	{
		return;
	}
	if (!config.postVar)
	{
		config.postVar = 'data';
	}
	
	if (!statics[config.id])
	{
		statics[config.id] = {};
	}
	
	if (!config.result_id)
	{
		statics[config.id].result = new Element('span', {
			'class': 'center',
			'style': 'margin-left: 10px;'
		});
		statics[config.id].result.inject(input, 'after');
	}
	else
	{
		statics[config.id].result = document.id(config.result_id);
	}
	
	input.addEvent('change', function()
	{
		xhr = new Request({method: 'post', url: config.callback, onSuccess: function(text)
		{
			var json = JSON.decode(text);
			if (json.status == 'OK')
			{
				var retval;
				if (json.result == 'OK')
				{
					retval = '<span style="color: green;">'
						+ '<img src="/pix.gif" alt="" class="fff tick" /> '
						+ json.message
						+ '</span>';
				}
				else
				{
					retval = '<span style="color: red;">'
						+ '<img src="/pix.gif" alt="" class="fff cross" /> '
						+ (json.message ? json.message : 'La valeur est invalide.')
						+ '</span>';
				}
				
				statics[config.id].result.set('html', retval);
			}
		}});
		xhr.send(config.postVar + '=' + input.get('value'));
	});
});