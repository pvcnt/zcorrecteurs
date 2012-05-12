/**
 * Initialise l'enregistrement des statistiques Google Analytics.
 *
 * @provides vitesse-behavior-google-analytics
 * @requires vitesse-behavior
 */
Behavior.create('google-analytics', function(config)
{
	if (!config.account || !config.domain)
	{
		return;
	}
	
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', config.account]);
    _gaq.push(['_setDomainName', config.domain]);
    _gaq.push(['_trackPageview']);

    (function() {
    	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
});