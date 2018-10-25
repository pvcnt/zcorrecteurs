function afficher_votants(bouton, id_forum, id_sondage)
{
	bouton.setStyle('display', 'none');
	$('persones_qui_ont_vote').set('html', '<p class="centre"><img src="/img/ajax-loader.gif" alt="" /></p>');

	setTimeout(function(){
		xhr = new Request({method: 'get', url: '/forum/ajax-retour-sondage.html', onSuccess:
			function(text, xml){
				$('persones_qui_ont_vote').set('html', '<h2>Ont voté :</h2><br/>'+text);
			}
		});
		xhr.send('forum='+id_forum+'&sondage='+id_sondage);
	}, 500);
}
