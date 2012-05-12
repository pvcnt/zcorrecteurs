/**
 * @provides vitesse-behavior-resizable-textarea
 * @requires vitesse-behavior
 *		     mootools
 */

/**
 * Comportement permettant de rendre une zone de texte redimensionnable 
 * sur tous les navigateurs (fonctionnement identique à Drupal).
 *
 *   - id : identifiant de la zone sur laquelle appliquer le comportement
 */
Behavior.create('resizable-textarea', function(config)
{
	var textarea = document.id(config.id);
	
	var resizerTextarea = null;
	var resizerStaticOffset = null;
	var resizerLastMousePos = 0;
	
	var performDrag = function(e)
	{
		var thisMousePos = e.page.y;
		var mousePos = resizerStaticOffset + thisMousePos;
		if (resizerLastMousePos >= thisMousePos)
		{
			mousePos -= 5;
		}
		resizerLastMousePos = thisMousePos;
		mousePos = Math.max(32, mousePos);
		resizerTextarea.setStyle('height', mousePos + 'px');
		if (mousePos < 32)
		{
			endDrag(e);
		}
	};
	
	var endDrag = function(e)
	{
		document.removeEvent('mousemove', performDrag);
		document.removeEvent('mouseup', endDrag);
		resizerTextarea.focus();
		resizerTextarea = null;
		resizerStaticOffset = null;
		resizerLastMousePos = 0;
	}
	
	var grippie = new Element('div', {
		'class': 'resizable-textarea',
		'data-target': textarea.get('id'),
	});
	grippie.addEvent('mousedown', function(e)
	{
		if (document.id(this.get('data-target')))
		{
			resizerTextarea = document.id(this.get('data-target'));
			resizerLastMousePos = e.page.y;
			resizerStaticOffset = parseInt(resizerTextarea.getStyle('height')) - resizerLastMousePos;
			document.addEvent('mousemove', performDrag);
			document.addEvent('mouseup', endDrag);
		}
	});
	grippie.inject(textarea, 'after');
});