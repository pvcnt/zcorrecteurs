/**
 * Gère le clic sur des étoiles de choix d'une note.
 *
 * @provides vitesse-behavior-livredor-stars
 * @requires vitesse-behavior
 *           mootools
 */
Behavior.create('livredor-stars', function(config, statics)
{
    if (!config.id || !config.note_id)
    {
        return;
    }
    
    $$('#' + config.id + ' ul.star-rating li a').each(function(elem, i)
    {
        elem.addEvent('click', function(e)
        {
            e.stop();
            var note = parseInt(elem.get('html'));
            $$('#' + config.id + ' ul.star-rating li.current-rating').setStyle('width', note * 30);
        	$(config.note_id).value = note;
        	if (config.textarea_id)
        	{
        	    $(config.textarea_id).focus();
    	    }
        });
    });
    
	statics.err = false;
	$(config.id).addEvent('submit', function(e)
	{
		if ($('note').value == '-1')
		{
			e.stop();
			var sr = $$('.star-rating')[0];
			if (!statics.err)
			{
				var el = new Element('div')
					.set('text', 'Veuillez attribuer une note en cliquant sur les étoiles.')
					.setStyle('color', 'red')
					.setStyle('float', 'right')
					.setStyle('margin-top', '10px')
					.inject(sr, 'before');
				window.setTimeout(function() {el.dispose(); statics.err = false}, 5000);
				statics.err = true;
			}
			sr.highlight('#ff0000');
		}
	});
});