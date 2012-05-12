var to = true;

/**
 * Inverse l'état de toutes les cases à cocher (et colore les lignes du tableau).
 */
function switch_checkbox()
{
	$$('#action_etendue input[type=checkbox]').each(function(el) {
		el.checked = to;
		if (el.checked) {
			el.getParent('tr').addClass('sous_cat_selected');
		} else {
			el.getParent('tr').removeClass('sous_cat_selected');
		}
	});
	to = !to;
}

/**
 * Inverse l'état de toutes les cases à cocher (et colore les lignes du tableau).
 */
function generator_switch_checkbox(form_id)
{
	$$('#'+form_id+' input[type=checkbox]').each(function(el) {
		el.checked = to;
		if (el.checked) {
			el.getParent('tr').addClass('sous_cat_selected');
		} else {
			el.getParent('tr').removeClass('sous_cat_selected');
		}
	});
	to = !to;
}

/**
 * Inverse l'état de toutes les cases à cocher.
 * @param integer form_id					L'id de la form.
 */
function switch_checkbox_normal(form_id)
{
	$$('#'+form_id+' input[type=checkbox]').each(function(el) {
		el.checked = to;
	});
	to = !to;
}

var flag = (window.Event)? true : false;
function fixEvent(event)
{
	event = event || window.event;
	if(event.target)
	{
		if(event.target.nodeType == 3) event.target = event.target.parentNode; // defeat Safari bug
	}
	else if (event.srcElement)
	{
		event.target = event.srcElement;
	}
	return event;
}

function InverserEtat(e)
{
	event = fixEvent(e);
	if (flag)
	{
		parentEl = event.target.parentNode;
	}
	else
	{
		parentEl = window.event.srcElement.parentElement;
	}
	if (parentEl.tagName == "TR" && parentEl.className !== 'espace_postit' && parentEl.className !== 'sous_cat vide')
	{
		if(event.type == 'click')
		{
			if(event.target.nodeName !== "A")
			{
				var cat = $(parentEl);
				var checkbox = cat.getElement('input[type=checkbox]');
				checkbox.checked = !checkbox.checked;
				if (checkbox.checked)
				{
					cat.addClass("sous_cat_selected");
				}
				else
				{
					cat.removeClass("sous_cat_selected");
				}
			}
		}
		else if(event.type == 'mouseover')
		{
			$(parentEl).addClass("sous_cat_over");
		}
		else if(event.type == 'mouseout')
		{
			$(parentEl).removeClass("sous_cat_over");
		}
	}
}

function ColorerLigneOncheck(e)
{
	event = fixEvent(e);
	if (flag)
	{
		checkbox = event.target;
	}
	else
	{
		checkbox = window.event.srcElement;
	}
	if ($(checkbox).checked)
	{
		$(checkbox).getParent('tr').addClass('sous_cat_selected');
	}
	else
	{
		$(checkbox).getParent('tr').removeClass('sous_cat_selected');
	}
}

