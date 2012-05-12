/**
 * Sauvegarde des commentaires de zCorrection.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */

/* Dernières valeurs sauvegardées */
var savedComm;
var savedComm2;

/* Effets sur les formulaires */
var formComm;
var formComm2;

/* Initialisation */
function initSave()
{
	/* Commentaire à l'auteur */
	savedComm = $('comm').value;
	formComm = new Fx.Style(
		$('comm'),
		'background-color' ,
		{duration:2000}
	);

	/* Commentaire aux zCorrecteurs */
	savedComm2 = $('comm2').value;
	formComm2 = new Fx.Style(
		$('comm2'),
		'background-color' ,
		{duration:2000}
	);

}

/* Sauvegarde du commentaire à l'auteur */
function saveComm(id)
{
	/* On ne sauvegarde qu'en cas de changement */
	if($('comm').value != savedComm)
	{
		xhr = new Request({method: 'post', url: '/zcorrection/ajax-save-commentaires.html',
			onSuccess: function(){
				formComm.stop();
				if(texte != '') {
					$('comm').hightlight('#ffaeae');
				} else {
					$('comm').hightlight('#b3ffb3');
				}
		}});
		xhr.send('texte='+encodeURIComponent($('comm').value)+'&id='+encodeURIComponent(id));
		savedComm = $('comm').value;
	}
}

/* Sauvegarde du commentaire aux zCorrecteurs */
function saveComm2(id)
{
	/* On ne sauvegarde qu'en cas de changement */
	if($('comm2').value != savedComm2)
	{
		xhr = new Request({method: 'post', url: '/zcorrection/ajax-save-commentaires2.html',
			onSuccess: function(){
				formComm2.stop();
				if(texte != '') {
					$('comm2').hightlight('#ffaeae');
				} else {
					$('comm2').hightlight('#b3ffb3');
				}
		}});
		xhr.send('texte='+encodeURIComponent($('comm2').value)+'&id='+encodeURIComponent(id));
		savedComm2 = $('comm2').value;
	}
}

/* Sauvegarde de la confidentialité */
function saveConfidentialite(id)
{
	/* Définition de la valeur et du message*/
	if($('confidentialite').checked)
	{
		value = 1;
		var mess = 'Votre pseudo est maintenant bien caché !';
	}
	else
	{
		value = 0;
		var mess = 'Votre pseudo n\'est maintenant plus caché !';
	}

	/* Enregistrement et affichage du message */
	xhr = new Request({method: 'post', url: '/zcorrection/ajax-save-confidentialite.html',
		onSuccess: function(){
			message(mess, '#6c6');
	}});
	xhr.send('value='+encodeURIComponent(value)+'&id='+encodeURIComponent(id));
}

/* On initialise les variables le temps que tout se charge correctement */
window.addEvent('domready', initSave);
//setTimeout('initSave()', 500);
