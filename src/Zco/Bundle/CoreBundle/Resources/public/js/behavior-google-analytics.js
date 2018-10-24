/**
 * Initialise l'enregistrement des statistiques Google Analytics.
 *
 * @provides vitesse-behavior-google-analytics
 * @requires vitesse-behavior
 */
Behavior.create('google-analytics', function (config) {
    if (!config.account) {
        return;
    }

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', config.account, 'auto');

    // Les trois commandes suivantes ont pour effet d'améliorer le respect de
    // la vie privée en désactivant les fonctionnalités marketing, obfuscant
    // l'adresse IP et envoyant les données via SSL.
    ga('set', 'allowAdFeatures', false);
    ga('set', 'anonymizeIp', true);
    ga('set', 'forceSSL', true);

    ga('send', 'pageview');
});