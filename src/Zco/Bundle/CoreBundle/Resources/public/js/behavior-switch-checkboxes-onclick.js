/*
 * @provides vitesse-behavior-switch-checkboxes-onclick
 * @requires vitesse-behavior mootools-js
 */

/**
 * Inverse l'état de toutes les cases à cocher (et colore les lignes du 
 * tableau le cas échéant).
 */
Behavior.create('switch-checkboxes-onclick', function(config, statics)
{
    if (!$chk(statics.to))
    {
        statics.to = true;
    }

	$$('#' + config.id + ' input[type=checkbox]').each(function(el)
	{
		el.checked = to;
		
		//Change la classe de la ligne si la case est contenue dans un tableau.
		if (el.getParent('tr'))
		{
		    if (el.checked)
		    {
			    el.getParent('tr').addClass('sous_cat_selected');
		    }
		    else
		    {
			    el.getParent('tr').removeClass('sous_cat_selected');
		    }
	    }
	});
	statics.to = !statics.to;
});