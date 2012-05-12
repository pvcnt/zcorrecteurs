/**
 * @provides vitesse-behavior-zco-files-insert-link
 * @requires mootools
 */
Behavior.create('zco-files-insert-link', function(config)
{
    document.id(config.id).addEvent('click', function(e)
    {
        e.stop();
        
        if (config.textarea_id)
        {
            var input = parent.document.id(config.textarea_id);
            input.insertAtCursor(config.value);
        }
        else if (config.input_id)
        {
            var input = parent.document.id(config.input_id);
            input.set('value', config.value);
        }
    });
});