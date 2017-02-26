/**
 * Affiche un bloc et masque tous les autres au clic sur un lien.
 *
 * @provides vitesse-behavior-toggle-blocks
 * @requires vitesse-behavior
 *           mootools
 */
Behavior.create('toggle-blocks', function(config)
{
	var href_id = function(href)
	{
		return href.substring(href.indexOf("#") + 1);
	};
	var showBlock = function(name)
	{
		$(name).slide("show");
		$(name).fade("in");
	};
	var hideBlocks = function(except)
	{
		$$("#toggle_links a").each(function(e) {
			if(except != "" && href_id(e.href) != except) {
				$(href_id(e.href)).fade("hide");
				$(href_id(e.href)).slide("hide");
			}
		});
	};
	
	if (config.current_block)
	{
	    hideBlocks("bloc_" + config.current_block);
    }
    
	$$("#toggle_links a").each(function(e) {
		e.addEvent("click", function(ev) {
			ev.stop();
			hideBlocks();
			showBlock(href_id(this.href));
		});
	});
	$("toggle_links").setStyle("display", "block");
});