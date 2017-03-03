/**
 * Affiche un marqueur sur une carte Google.
 *
 * @provides vitesse-behavior-ips-display-marker-on-map
 * @requires vitesse-behavior
 *           google-maps
 */
Behavior.create('ips-display-marker-on-map', function(config, statics)
{
	if (GBrowserIsCompatible())
	{
		var map = new GMap2(document.getElementById(config.id));
		map.addControl(new GSmallMapControl());
		map.addControl(new GMapTypeControl());
		var point = new GLatLng(config.latitude, config.longitude);
		map.setCenter(point, 4);
		map.addOverlay(new GMarker(point));
		map.setMapType(G_NORMAL_MAP);
	}
	
	window.addEvent('unload', 'GUnload');
});
