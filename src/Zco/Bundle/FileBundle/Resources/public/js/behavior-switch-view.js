/**
 * @provides vitesse-behavior-zco-files-switch-view
 * @requires mootools
 */
Behavior.create('zco-files-switch-view', function(config)
{
    document.id('thumb_view_link').addEvent('click', function(e)
    {
        e.preventDefault();
        document.id('thumb_view_link').addClass('active');
        document.id('list_view_link').removeClass('active');
        
        $$('table.table').setStyle('display', 'none');
        if ($$('ul.thumbnails li').length > 0)
        {
            $$('ul.thumbnails').setStyle('display', '');
        }
    });
    
    document.id('list_view_link').addEvent('click', function(e)
    {
        e.preventDefault();
        document.id('thumb_view_link').removeClass('active');
        document.id('list_view_link').addClass('active');
        
        $$('ul.thumbnails').setStyle('display', 'none');
        if ($$('table.table tr td').length > 0)
        {
            $$('table.table').setStyle('display', '');
        }
    });
    
    $$('table.table').setStyle('display', 'none');
});