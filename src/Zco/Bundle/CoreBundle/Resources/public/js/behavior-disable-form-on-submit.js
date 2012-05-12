/**
 * Initialise le blocage des éléments du formulaire lors de sa soumission, 
 * pour éviter les doubles insertions.
 *
 * @provides vitesse-behavior-disable-form-on-submit
 * @requires vitesse-behavior
 *           mootools
 */
Behavior.create('disable-form-on-submit', function(config)
{
	function returnFalse() { return false; }
	
	$$('form').each(function(form)
	{
		form.addEvent('submit', function()
		{
			if (this.sumbitted)
				return false;
			this.sumbitted = true;
			this.getElements('input, select, checkbox, textarea')
			.each(function(el)
			{
				el.setStyle('opacity', '0.3');
				el._onclick = el.onclick;
				el.onclick = returnFalse;
				el._onkeypress = el.onkeypress;
				el.onkeypress = returnFalse;
			});

			setTimeout(function()
			{
				this.getElements('input, select, checkbox, textarea')
				.each(function(el)
				{
					el.setStyle('opacity', '1');
					el.onclick = el._onclick;
					el.onkeypress = el._onkeypress;
				});
				this.sumbitted = false;
			}.bind(this), 5000);
		});
	});
});