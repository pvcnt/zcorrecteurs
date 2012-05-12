/**
 * @provides vitesse-behavior-zco-files-aviary
 * @requires mootools
 *           @ZcoCoreBundle/Resources/public/js/libs/zMessage.js
 */
Behavior.create('zco-files-aviary', function(config)
{
    var featherEditor = new Aviary.Feather({
        apiKey: config.apiKey,
        apiVersion: 2,
        language: 'fr',
        tools: 'resize,orientation,crop,enhance,text,draw,effects,contrast,brightness',
        appendTo: '',
		postUrl: config.base_url + '/' + Routing.generate('zco_file_api_save', {id: config.file_id}),
        onSave: function(imageID, newURL)
		{
			document.id(imageID).set('src', newURL);
			return false;
        }
    });
    
    $(config.link_id).addEvent('click', function(e)
    {
        e.stop();
        featherEditor.launch(config.options);
    });
});
