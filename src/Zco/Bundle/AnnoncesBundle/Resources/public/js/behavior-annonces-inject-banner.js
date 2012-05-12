/**
 * Injecte une annonce globale au site de façon asynchrone dans le champ 
 * spécialement défini à cet effet.
 *
 * @provides vitesse-behavior-annonces-inject-banner
 * @requires vitesse-behavior mootools
 */
Behavior.create('annonces-inject-banner', function(config)
{
	var annonce = $('postloading-area');
	if (!annonce)
	{
	    return;
    }
	
	xhr = new Request({method: 'get', url: '/annonces/afficher.html',
		onSuccess: function(text, xml)
		{
			annonce.set('html', text);
		}
	});
	
	data = 'categorie='+config.categorie+'&_page='+encodeURI(config.page);
	if (config.annonce)
	{
	    data += '&annonce='+config.annonce;
	}
	if (config.pays)
	{
	    data += '&pays='+config.pays;
	}
	if (config.groupe)
	{
	    data += '&groupe='+config.groupe;
	}
	xhr.send(data);
});