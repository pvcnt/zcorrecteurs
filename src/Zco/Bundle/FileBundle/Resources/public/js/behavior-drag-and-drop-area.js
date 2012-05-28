/**
 * @provides vitesse-behavior-zco-files-drag-and-drop-area
 * @requires mootools
 *		   @ZcoFileBundle/Resources/public/js/FormUpload/Form.Upload.js
 *		   @ZcoFileBundle/Resources/public/js/FormUpload/Form.MultipleFileInput.js
 *		   @ZcoFileBundle/Resources/public/js/FormUpload/Request.File.js
 *		   @ZcoCoreBundle/Resources/public/js/libs/zMessage.js
 */
Behavior.create('zco-files-drag-and-drop-area', function(config)
{
	/*<div class="droppable" id="droppable">
        <p>Déposez les fichiers depuis votre ordinateur dans cette zone.</p>
    </div>*/
	
	var drop  = document.id('droppable');
	var files = document.id('files');
	var file = document.id('file');
	var form  = document.id('uploadForm');
	
	Locale.use('fr-FR');
	file.setStyle('display', 'none');
	var inputFiles = new Form.MultipleFileInput('file', 'files', 'droppable', {
		onDragenter: drop.addClass.pass('hover', drop),
		onDragleave: drop.removeClass.pass('hover', drop),
		onDrop: function()
		{
			drop.removeClass.pass('hover', drop);
		}
	});
	
	form.addEvent('submit', function(event)
    {
        event.preventDefault();
		
		var request = new Request.File({
			url: form.get('action'), 
			onSuccess: function(text)
			{
				response = JSON.decode(text);
				if (response.status == 'OK')
				{
					if (config.redirect_url)
					{
						document.location = config.redirect_url;
					}
				}
				else
				{
					var append = [];
					var i;
					for (i = 0; i < response.failed.length; i++)
					{
						append.push(response.failed[i].name + (response.failed[i].message ? ' (' + response.failed[i].message + ')' : ''));
					}
					zMessage.error(
						'Il y a eu une erreur lors de l’import des fichiers '
						+ 'suivants : ' + append.join(', ') + '.'
					);
				}
			}, onFailure: function(text)
			{
				zMessage.error('Il y a eu une erreur lors de l’import des fichiers.');
			}
		
			/*,
			onProgress: function(text)
			{
				alert(text);
			}*/
		});
	
		inputFiles.getFiles().each(function(file)
		{
			request.append('file[]' , file);
		});
		request.send();
	});
});
