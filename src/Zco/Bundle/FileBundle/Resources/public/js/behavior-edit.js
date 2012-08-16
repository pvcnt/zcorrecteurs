/**
 * @provides vitesse-behavior-zco-files-edit
 * @requires vitesse-behavior
 *           mootools
 *           @ZcoCoreBundle/Resources/public/js/vendor/zMessage.js
 */
 
/**
 * Paramètres de configuration :
 *   - file_id: identifiant du fichier
 *   - form_id : identifiant du bouton de modification
 *   - pseudo : pseudonyme de l'utilisateur
 *   - name_selector : sélecteurs où modifier le nouveau nom (facultatif)
 */
Behavior.create('zco-files-edit', function(config)
{
    var form = document.id(config.form_id);
    var name = form.getChildren('input[type=text]')[0];
    var license = form.getChildren('select')[0];
    
    form.addEvent('submit', function(e)
    {
        e.preventDefault();
        xhr = new Request({method: form.method, url: form.action,
        	onSuccess: function(text)
        	{
        	    response = JSON.decode(text);
        	    if (response.status == 'OK')
        	    {
        	        if (config.name_selector)
        	        {
        	            $$(config.name_selector).each(function(elem, i)
            	        {
            	            elem.set('text', name.get('value'));
            	        });
        	        }
        	        zMessage.info('Les propriétés du fichier ont bien été modifiées.');
    	        }
    	        else
    	        {
    	            zMessage.error('Une erreur interne s’est produite.');
    	        }
        	}
        });
        xhr.send('license=' + license.get('value') + '&name=' + name.get('value') + '&pseudo=' + config.pseudo);
    });
});