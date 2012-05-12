function recup_revisions(bouton, execute)
{
	if(execute == false)
	{
		$(bouton).setStyle('display', 'none');
		$('revisions').set('html', '<img src=\'/img/ajax-loader.gif\' alt=\'Chargement...\' />');
		setTimeout('recup_revisions(\''+bouton+'\', true);', 500);
	}
	else
	{
		xhr = new Request({method: 'post', url: '/evolution/ajax-liste-revisions.html', onSuccess: function(text, xml){
			$('revisions').set('html', text);
		}});
		xhr.send();
	}
}
