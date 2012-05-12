/**
 * @provides vitesse-behavior-twitter-bitly
 * @requires vitesse-behavior
 *           mootools
 */

/**
 * Permet de raccourcir des URL via bit.ly.
 * Paramètres :
 *   - textarea_id : ID de la zone de rédaction du tweet ;
 *   - button_id : ID du bouton d'envoi du tweet.
 * 
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
Behavior.create('twitter-bitly', function(config)
{
	var txt = document.id(config.textarea_id);
	var btn = document.id(config.button_id);
	
	var parag = new Element('div', {'class': 'control-group'})
	.grab(new Element('label', {
		'for':  'id_for_url',
		'text':	'Entrez une URL à raccourcir',
		'class': 'control-label'
	}));
	var controls = new Element('div', {'class': 'controls'});
	var url = new Element('input', {
		'id':    'id_for_url',
		'type':  'text'
	});
	
	var spinner = new Element('div', {
		html: '<img src="/img/ajax-loader.gif" alt="Chargement…" /> <em>Chargement en cours…</em>'
	});
	
	controls
	.grab(url)
	.grab(new Element('p', {
		'class': 'help-block',
		'text': 'Appuyez sur la touche Entrée pour insérer l\'URL.',
	}));
	parag.grab(controls);
	
	url.addEvent('keypress', function(e)
	{
		if (e.key == 'enter')
		{
			e.stop();
			spinner.inject(url, 'before');
			url.setStyle('display', 'none');
			var xhr = new Request({
				method:     'post',
				url:        Routing.generate('zco_twitter_api_bitly'),
				onComplete: function(text)
				{
					json = JSON.decode(text);
					if (json.status == 'OK')
					{
						txt.value += ' ' + json.url + ' ';
						url.value = '';
						txt.fireEvent('keyup');
						txt.focus();
					}
					
					spinner.dispose();
					url.setStyle('display', '');
				}
			});
			xhr.send('url='+url.value);
		}
	});
	
	parag.inject(btn.parentNode, 'before');
});