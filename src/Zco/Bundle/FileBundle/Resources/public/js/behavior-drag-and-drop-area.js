/**
 * @provides vitesse-behavior-zco-files-drag-and-drop-area
 * @requires mootools
 *         jquery-no-conflict
 *         bootstrap-js
 *		   @ZcoFileBundle/Resources/public/js/FormUpload/Form.Upload.js
 *		   @ZcoFileBundle/Resources/public/js/FormUpload/Form.MultipleFileInput.js
 *		   @ZcoFileBundle/Resources/public/js/FormUpload/Request.File.js
 *		   @ZcoCoreBundle/Resources/public/js/libs/zMessage.js
 */
Behavior.create('zco-files-drag-and-drop-area', function(config)
{
	var files = document.id('files');
	var file = document.id('file');
	var form  = document.id('uploadForm');
	var progress = document.id('progress-bar');
	var modal = jQuery('#progress-modal').modal({"keyboard": false, "show": false});
	
	var drop = new Element('div', {
		'class': 'droppable', 
		'id': 'droppable', 
		'html': '<p>Vous pouvez aussi déposer les fichiers directement depuis votre ordinateur dans cette zone.</p>'
	}).inject(file, 'after');
	
	Locale.use('fr-FR');
	var inputFiles = new Form.MultipleFileInput('file', 'files', 'droppable', {
		onDragenter: drop.addClass.pass('hover', drop),
		onDragleave: drop.removeClass.pass('hover', drop),
		onDrop: function() {
			drop.removeClass.pass('hover', drop);
		}
	});
	form.addEvent('submit', function(e)
	{
		e.preventDefault();	
		var request = new Request.File({
			url: form.get('action'), 
			onSuccess: function(text) {
				var json = JSON.decode(text);
				if (json.type == 'error') {
					modal.modal('hide');
					zMessage.error(json.msg);
				} else {
					document.location = json.url;
				}
			},
			onFailure: function() {
				zMessage.error('Il y a eu une erreur lors de l’import des fichiers.');
			},
			onProgress: function(event) {
				var percent = (event.loaded / event.total) * 100;
				progress.getChildren()[0].setStyle('width', percent + '%');
			}
		});
		inputFiles.getFiles().each(function(file) {
			request.append('file[]' , file);
		});
		progress.setStyle('display', 'block');
		progress.getChildren()[0].setStyle('width', '0%');
		modal.modal('show');
		request.send();
	});
});
