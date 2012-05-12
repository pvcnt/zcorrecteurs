/**
 * Script gérant la modification d'un élément directement depuis son 
 * emplacement dans la page.
 *
 * @author   vincent1870 <vincent@zcorrecteurs.fr>
 * @requires mootools
 *           @ZcoCoreBundle/Resources/public/js/libs/zMessage.js
 */
var EditInPlace = new Class({
	Implements: [Options],

	options: {
	    extraData: {},
		postVar: 'data',
		cols: 70,
		rows: 10,
		input: 'text',
		save_text: 'Modifier',
		cancel_text: 'Annuler',
		event: 'dblclick'
	},

	initialize: function(element, url, options)
	{
		this.element = $(element);
		this.setOptions(options);
		options = this.options;

		//Ajout d'une balise title
		if(this.options.event == 'dblclick')
			$(element).set('title', 'Double-cliquez pour modifier cet élément');
		else if(this.options.event == 'click')
			$(element).set('title', 'Cliquez pour modifier cet élément');

		//Au clic, ajouter un <input type="text" />
		$(element).addEvent(this.options.event, function(e)
		{
			e.stop();
			var flag_cancel = false;

			//Si l'objet a la classe _ajax_editing, c'est qu'il est actif.
			if($(element).hasClass('_ajax_editing'))
				return;
			$(element).addClass('_ajax_editing');
			var orig_text = htmlspecialchars_decode($(element).get('html').trim());

			var input = document.createElement('input');
			input.set('type', 'text');
			input.set('size', '40');
			input.set('value', orig_text);

			var cancel = document.createElement('input');
			cancel.set('type', 'button');
			cancel.set('value', options.cancel_text);
			cancel.setStyle('margin-left', '10px');
			cancel.addEvent('click', function(){
				input.set('value', orig_text);
				flag_cancel = true;
				submit.click();
			});

			var submit = document.createElement('input');
			submit.set('type', 'submit');
			submit.set('value', options.save_text);
			submit.setStyle('margin-left', '10px');
			submit.addEvent('click', function()
			{
				//Si on valide l'édition, on envoie une requête AJAX
				xhr = new Request({
					method: 'post',
					url: url,
					onSuccess: function(text, xml) {
						$(element).set('html', htmlspecialchars(text));
						$(element).removeClass('_ajax_editing');
						if(!flag_cancel)
							zMessage.info('Modification réalisée avec succès !');
					}
				});
				var params = options.postVar+'='+encodeURIComponent(input.get('value'));
				for (key in options.extraData)
					params += '&'+key+'='+options.extraData[key];
				params += '&type='+element;
				xhr.send(params);
			});

			$(element).set('html', '');
			input.inject($(element));
			submit.inject($(element));
			cancel.inject($(element));
			input.focus();
		});
	}
});
