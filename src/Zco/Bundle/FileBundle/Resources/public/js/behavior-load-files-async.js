/**
 * @provides vitesse-behavior-zco-files-load-files-async
 * @requires vitesse-behavior
 *		   mootools
 *		   bootstrap-js
 *		   javelin-metadata
 *		   @ZcoCoreBundle/Resources/public/js/zMessage.js
 *		   @ZcoFileBundle/Resources/public/js/SqueezeBox.js
 */
Behavior.create('zco-files-load-files-async', function(config, statics)
{
	if (!statics.count)
	{
		statics.count = 0;
	}
	
	var input = document.id('search');
	var spinner = document.id('thumbnails-loader');
	var area = document.id('thumbnails');
	var table = document.id('thumbnails-table')
	var apiUrl = Routing.generate('zco_file_api_search', Object.merge(
		{"folder": config.folder}, 
		config.contentFolder ? {"entities": config.contentFolder} : {}, 
		config.urlExtraData
	));
	var button = document.id('delete_button');
	var timer;
		
	var noData = new Element('p')
		.set('html', 'Aucun fichier correspondant n’a été trouvé.<br />'
		+ '<a href="' + Routing.generate('zco_file_index') + '">Je veux en envoyer maintenant !</a>');
	
	input.addEvent('keyup', function()
	{
		noData.dispose();
		$$('#thumbnails li').destroy();
		$$('#thumbnails-table td').destroy();
		spinner.setStyle('display', '');
		timer = setTimeout(function(){ loadFiles(input.get('value')) }, 200);
	});
	input.addEvent('keydown', function()
	{
		clearTimeout(timer);
	});
	
	if (button)
	{
		//On commence par masquer le bouton en début de script.
		button.setStyle('display', 'none');
		
		//Lors d'un clic sur le bouton, on supprime tous les éléments avec une 
		//confirmation préalable.
		button.addEvent('click', function()
		{
			document.id('batch-delete-confirmation-modal-cancel').addEvent('click', function()
			{
				jQuery('#batch-delete-confirmation-modal').modal('hide');
			});
			document.id('batch-delete-confirmation-modal-confirm')
			.removeEvents('click')
			.addEvent('click', function()
			{
				jQuery('#batch-delete-confirmation-modal').css('display', '').modal('hide');
				button.setStyle('display', 'none');
				
				$$('.thumbnail input[type=checkbox]').each(function(elem)
				{
					if (elem.get('checked'))
					{
						deleteFile(elem.getParent().getParent().getParent(), false);
					}
				});
			});
			jQuery('#batch-delete-confirmation-modal').css('display', '').modal();
		});
	
		//Quand on quitte la page, on décoche toutes les cases pour éviter des 
		//effets de cache indésirables.
		window.addEvent('unload', function()
		{
			$$('.thumbnail input[type=checkbox]').each(function(elem)
			{
				elem.set('checked', false);
			});
		});
	}
	
	function deleteFile(element, confirmation)
	{
		var fileId = Metadata.getData(element).fileId;
		if (!fileId)
		{
			throw new Error('No file metadata.');
		}
		
		if (confirmation)
		{
			document.id('delete-confirmation-modal-cancel')
			.removeEvents('click')
			.addEvent('click', function()
			{
				jQuery('#delete-confirmation-modal').modal('hide');
			});
			document.id('delete-confirmation-modal-confirm')
			.removeEvents('click')
			.addEvent('click', function()
			{
				jQuery('#delete-confirmation-modal').css('display', '').modal('hide');
				deleteFile(element, false);
			});
			jQuery('#delete-confirmation-modal').css('display', '').modal();
		}
		else
		{
			var xhr = new Request({
				method: 'post', 
				url: Routing.generate('zco_file_api_delete', {id: fileId}),
				onSuccess: function(text)
				{
					var parts = element.get('id').split('-');
					document.id(parts[0] + '-thumbnail-' + parts[2]).destroy();
					document.id(parts[0] + '-row-' + parts[2]).destroy();
					
					var xhr2 = new Request({
						method: 'post', 
						url: Routing.generate('zco_file_api_usage'),
						onSuccess: function(text)
						{
							var response = JSON.decode(text);
							var progress = document.id('folder-usage-progress');
							progress.getElement('div').setStyle('width', (response.ratio > 5 ? response.ratio : 5) + '%').set('text', response.ratio + ' %');
							progress
							.removeClass('progress-success')
							.removeClass('progress-warning')
							.removeClass('progress-danger')
							.addClass('progress-' + response.usageClass);
						}
					});
					xhr2.send();
				}
			});
			xhr.send();
		}
	}
	
	function loadFiles(search)
	{
		if (statics.loading)
		{
			return;
		}
		
		statics.loading = true;
		xhr = new Request({method: 'post', url: apiUrl,
			onSuccess: function(text)
			{
				var files = JSON.decode(text);
				displayFiles(files);
				spinner.setStyle('display', 'none');
				statics.loading = false;
			}
		});
		xhr.send('search=' + search);
	}
	
	function displayFiles(files)
	{
		if (!files.length)
		{
			noData.inject(area, 'before');
		}
		else
		{
			for (i = 0, c = files.length; i < c; i++)
			{
				var file = files[i];
				var li = new Element('li');
				Metadata.addData(li, {fileId: file.id});
								
				li
				.set('id', 'file-thumbnail-' + i)
				.addClass('span3')
				.grab(
					new Element('div')
					.addClass('thumbnail')
					.grab(
						new Element('h5')
						.grab(
							new Element('input')
							.set('type', 'checkbox')
							.addEvent('change', function(e)
							{
								if (this.get('checked') == true)
								{
									statics.count++;
								}
								else
								{
									statics.count--;
								}
								if (statics.count > 0 && button.getStyle('display') == 'none')
								{
									button.setStyle('display', '');
								}
								else if (statics.count == 0 && button.getStyle('display') != 'none')
								{
									button.setStyle('display', 'none');
								}
							})
						)
						.grab(
							new Element('span').set('text', ' ' + file.name)
						)
					).grab(
						new Element('a')
						.set('href', Routing.generate('zco_file_file', Object.merge({id: file.id}, config.urlExtraData)))
						.set('html', '<img src="' + file.thumbnail_path + '" alt="Image" />')
					).grab(
						new Element('p')
						.addClass('thumbnail-actions')
						.setStyle('display', 'none')
						.grab(
							new Element('a')
							.set('href', '#')
							.setStyle('float', 'right')
							.set('html', '<img src="/img/supprimer.png" />')
							.addEvent('click', function(e)
							{
								e.preventDefault();
								deleteFile(e.target.getParent('li'), true);
							})
						)
						.grab(
							new Element('a')
							.set('href', file.path)
							.set('html', '<img src="/img/misc/zoom.png" />')
						)
					)
				)
				.addEvent('mouseenter', function(e)
				{
					var li = (e.target.tagName.toLowerCase() == 'li') ? e.target : e.target.getParent('li');
					li.getElement('.thumbnail-actions').setStyle('display', '');
				})
				.addEvent('mouseleave', function(e)
				{
					var li = (e.target.tagName.toLowerCase() == 'li') ? e.target : e.target.getParent('li');
					li.getElement('.thumbnail-actions').setStyle('display', 'none');
				})
				.inject(area);
				
				var tr = new Element('tr');
				tr.set('id', 'file-row-' + i);
				Metadata.addData(tr, {fileId: file.id});
				
				tr.grab(
					new Element('td')
					.addClass('bold')
					.grab(
						new Element('input')
						.set('type', 'checkbox')
						.addEvent('change', function(e)
						{
							if (this.get('checked') == true)
							{
								statics.count++;
							}
							else
							{
								statics.count--;
							}
							if (statics.count > 0 && button.getStyle('display') == 'none')
							{
								button.setStyle('display', '');
							}
							else if (statics.count == 0 && button.getStyle('display') != 'none')
							{
								button.setStyle('display', 'none');
							}
						})
					)
					.grab(
						new Element('a')
						.set('href', Routing.generate('zco_file_file', Object.merge({id: file.id}, config.urlExtraData)))
						.setStyle('margin-left', '5px')
						.set('text', file.name)
					)
				).grab(
					new Element('td')
					.set('html', file.size)
				).grab(
					new Element('td')
					.set('html', file.date)
				).grab(
					new Element('td')
					.grab(
						new Element('a')
						.set('href', file.path)
						.set('html', '<img src="/img/misc/zoom.png" />')
					)
					.grab(
						new Element('a')
						.set('href', '#')
						.set('html', '<img src="/img/supprimer.png" />')
						.addEvent('click', function(e)
						{
							e.preventDefault();
							deleteFile(e.target.getParent('tr'), true);
						})
					)
				).inject(table.getChildren('tbody')[0]);
			}
		}
	}
	
	//Récupération des données en fin de script.
	loadFiles('');
	document.id('thumbnails').setStyle('display', '');
	spinner.setStyle('display', 'none');
});
