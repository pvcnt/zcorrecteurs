window.addEvent('domready', function() {
    $$('.lien_citer').each(function(elem, i){
    	elem.addEvent('click', function() {
    		if(!elem.hasClass('selected'))
    		{
    			elem.addClass('selected');
    			elem.getChildren('img').set('src', '/bundles/zcoforum/img/multi_citer_ajoute.png');
    			xhr = new Request({method: 'post', url: '/forum/ajax-multi-citer.html',
    				onSuccess: function(text)
					{
    					$('texte').value += ($('texte').value ? "\n\n" : '') + text;
    				}
    			});
    			xhr.send('action=ajoute&url='+encodeURIComponent(elem.href));
    			return false;
    		}
    		else
    		{
    			elem.removeClass('selected');
    			elem.getChildren('img')[0].set('src', '/bundles/zcoforum/img/multi_citer.png');
    			xhr = new Request({method: 'post', url: '/forum/ajax-multi-citer.html',
    				onSuccess: function(text)
					{
						$('texte').value = $('texte').value.replace(text, '');
					}
    			});
    			xhr.send('action=supprime&url='+encodeURIComponent(elem.href));
    			return false;
    		}
    	});
    });
});

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
