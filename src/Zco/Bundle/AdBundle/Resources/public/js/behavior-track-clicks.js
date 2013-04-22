/**
 * @provides vitesse-behavior-ads-track-clicks
 * @requires mootools
 *           vitesse-behavior
 *           @ZcoCoreBundle/Resources/public/js/vendor/zMessage.js
 */

/**
 * Gestion des clics sur les liens publicitaires. Le comptage des clics
 * se fait en Ajax. La redirection est faite une fois le clic enregistré.
 *
 * Paramètres :
 *   - category_id : l'identifiant de la catégorie dans laquelle on se trouve
 */
Behavior.create('ads-track-clicks', function(config) {
    $$('.bloc_partenaires a').each(function(elem, i) {
        var id = elem.id.replace('pub-', '').trim();
        if (elem.href[0] != '/' && id) {
            elem.addEvent('click', function(e) {
                e.preventDefault();
                zMessage.info('Chargement…', {duration: -1});
                xhr = new Request({
                    method: 'post',
                    url: Routing.generate('zco_ad_api_saveClick', {'id': id}),
                    onComplete: function(text) {
                        zMessage.cacher()
                        window.location = elem.href;
                    }
                });
                xhr.send('cat=' + config.category_id);
            });
        }
    });
});