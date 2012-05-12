function chercher_adresse(adresse, id)
{
	var geocoder = new GClientGeocoder();

	geocoder.getLatLng(adresse,
	function(point)
	{
		if (!point)
		{
			alert('Le lieu ' + adresse + ' n\'a pas été trouvé.');
			return false;
		}
		else
		{
			xhr = new Request({method: 'post', url: '/options/ajax-enregistrer-coordonnees.html',
				onSuccess: function(text, xml){
					$('form_profil').coordonnees.value = text;
			}});
			xhr.send('point='+encodeURIComponent(point)+'&id='+encodeURIComponent(id));
			return true;
		}
	}
	);
}
