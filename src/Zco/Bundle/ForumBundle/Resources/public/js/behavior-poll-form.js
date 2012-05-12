/**
 * Gère un formulaire dynamique pour l'ajout d'un sondage à un sujet.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 * @provides vitesse-behavior-forum-poll-form
 * @requires vitesse-behavior
 *           mootools
 */
Behavior.create('forum-poll-form', function(config)
{
    if (config.inject_button)
    {
        new Element("input")
    	    .set("value", "Sondage")
        	.set("type", "button")
        	.addEvent("click", function()
        	{
        		this.fade("out");
        		$("postage_sondage").setStyle("display", "block");
        	}).inject(new Element("p").inject($(config.inject_button), "before"));
	
    	$(config.inject_button).setStyle("display", "none");
	}
	if (config.inject_link)
	{
		var li = new Element("li").inject($(config.inject_link), "before");
		new Element('img')
		    .set('src', '/pix.gif')
		    .addClass('fff')
		    .addClass('chart_bar')
		    .inject(li);
	    new Element("a")
    	    .set("href", "#" + config.inject_link)
    	    .set('html', ' Ajouter un sondage')
        	.addEvent("click", function()
        	{
        		$(config.inject_link).setStyle("display", "block");
        	}).inject(li);
	
    	$(config.inject_link).setStyle("display", "none");
    	
		//$("postage_sondage").setStyle("display", "none");
		//$("lien_ajouter_sondage").setStyle("display", "list-item");
	}
	
	var reponses = $$('#sondage_reponses div');
	var nb = -1;
    
	var ajouterChamp = function()
	{
		nb++;
		var champ = reponses[0].clone();
		var id = 'sdg_reponse' + nb;

		champ.getElement('label')
			.set('for', id)
			.set('text', 'Réponse ' + (nb - 200) + ' :');

		champ.getElement('input')
			.set('id', id)
			.set('tabindex', nb)
			.set('value', '')
			.addEvent('keyup', verifierChamps);
		champ.inject($('sondage_reponses'));

		$('sdg_reponse' + (nb - 1)).removeEvent('keyup', verifierChamps);
	}

	var verifierChamps = function()
	{
		var input = $('sdg_reponse' + nb);
		if(input.value != '')
			ajouterChamp();
	}

	// Suppression des champs vides présents pour les clients sans js
	for(var i = reponses.length - 1, total = reponses.length; i >= 0; i--)
	{
		var e = reponses[i];
		var nb2 = 0;
		var input = e.getElement('input');
		if(total > 1 && input.value == '') { // Garder une réponse minimum
			nb2 = parseInt(input.id.substring('sdg_reponse'.length));
			if(nb < 0 || nb2 < nb)
				nb = nb2;
			e.dispose();
			total--;
		}
	}
	nb--;
	ajouterChamp();
});