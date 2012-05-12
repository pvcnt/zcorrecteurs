/**
 * Gère le marquage comme lu des nouveauc commentaires sur le blog.
 *
 * @provides vitesse-behavior-blog-new-comments
 * @requires vitesse-behavior mootools
 */
Behavior.create('blog-new-comments', function(config)
{
	new Element("input", {
		"type"  : "button",
		"value" : "Tout sélectionner"
	}).addEvent("click", function() {
		$$("#nouveauxMessages input").each(function(el) {
			if(el.type == "checkbox")
				el.checked = true;
		});
		$$(".message").each(function(el) {
			el.addClass("bonne_reponse");
		});
	}).inject($("boutons_selection"));

	new Element("input", {
		"type"  : "button",
		"value" : "Tout désélectionner"
	}).addEvent("click", function() {
		$$("#nouveauxMessages input").each(function(el) {
			if(el.type == "checkbox")
				el.checked = false;
		});
		$$(".message.bonne_reponse").each(function(el) {
			el.removeClass("bonne_reponse");
		});
	}).inject($("boutons_selection"));

	function toggleMessage(boite) {
		var checkbox = boite.parentNode.previousSibling.previousSibling
			.getElementsByTagName("input")[0];
		if(!checkbox || checkbox.type != "checkbox")
			return;
		var c = checkbox.checked = !checkbox.checked;
		if(c) $(boite).addClass("bonne_reponse");
		else  $(boite).removeClass("bonne_reponse");
	}

	$$("#nouveauxMessages input").each(function(el) {
		if(el.type == "checkbox")
			el.setStyle("visibility", "hidden");
	});

	$$(".message").each(function(el) {
		el.addEvent("click", function() {
			toggleMessage(this);
		});
		el.setStyle("cursor", "pointer");
	});
});