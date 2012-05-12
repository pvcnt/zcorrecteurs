/**
 * Script servant à sauvegarder le contenu des zForms.
 *
 * @provides vitesse-behavior-table-with-checkboxes
 * @requires vitesse-behavior mootools-js
 */
Behavior.create('table-with-checkboxes', function(config, statics)
{
    $$('#' + config.id + ' tr').each(function(el)
    {
        if (el.hasClass('espace_postit') || el.hasClass('sous_cat_vide'))
        {
            return;
        }
        
        el.addEvent('mouseover', function()
        {
            el.addClass('sous_cat_over');
        });
        el.addEvent('mouseout', function()
        {
            el.removeClass('sous_cat_over');
        });
        el.addEvent('click', function(event, target)
        {
            if (target.nodeName == 'a')
            {
                return;
            }
            
            var checkbox = el.getElement('input[type=checkbox]');
            checkbox.set('checked', !checkbox.get('checked'));
            if (checkbox.get('checked'))
            {
                el.addClass('sous_cat_selected');
            }
            else
            {
                el.removeClass('sous_cat_selected');
            }
        });
    });
});
