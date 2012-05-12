/**
 * Affiche une série de marqueurs sur une carte Google Maps.
 *
 * @provides vitesse-behavior-user-markers-on-map
 * @requires vitesse-behavior
 *           google-maps
 */
Behavior.create('user-markers-on-map', function(config)
{
	if (GBrowserIsCompatible())
	{
		var carte = new GMap2(document.getElementById(config.id));
		carte.setCenter(new GLatLng(47.2000, 1.7500), 6);
		carte.setMapType(G_PHYSICAL_MAP);
		carte.addControl(new GMapTypeControl());
		carte.addControl(new GLargeMapControl()); //GSmallMapControl()

		//Fonction pour afficher les points à partir des coordonnées directement.
		function AfficherPoint(latitude, longitude, icone, pseudo, avatar, liens)
		{
			//On définit l'icône affichée
			var MonIcon = new GIcon(G_DEFAULT_ICON);
			MonIcon.image = icone;
			markerOptions = { icon:MonIcon };

			//On définit le marqueur (latitude, longitude) en lui ajoutant l'icône définie précédemment à l'aide de markerOptions
			var point = new GLatLng(latitude,longitude);
			var marqueur = new GMarker(point, markerOptions);

			//On définit ce qui se passe lors de l'événement click (lors d'un clic sur un marqueur).
			GEvent.addListener(marqueur,"click", function() {
			var html = '<strong>' + pseudo + '</strong><br />'
				+ '<img src="' + avatar + '"/><br />' 
				+ '<h4>Liens</h4>' + liens;
			//L'action à effectuer en cas de clic est donc l'affichage d'une info-bulle. On l'affiche.
			carte.openInfoWindowHtml(point, html);
			});

			//Tout est prêt, on peut afficher le marqueur.
			carte.addOverlay(marqueur);
		}
		
		for (i in config.markers)
		{
		    var marker = config.markers[i];
		    AfficherPoint(marker.latitude, marker.longitude, marker.img, marker.pseudo, marker.avatar, marker.url);
		}
	}
});

window.addEvent('unload', 'GUnload');