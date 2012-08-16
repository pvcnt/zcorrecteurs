/**
 * @provides vitesse-behavior-admin-homepage
 * @requires @ZcoCoreBundle/Resources/public/js/vendor/bootstrap.js
 */

/**
 * Retient le dernier onglet visité par l'utilisateur et le recharche 
 * à sa prochaine visite.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
Behavior.create('admin-homepage', function()
{
	var tabs = jQuery('.admin-wrapper a[data-toggle="tab"]');
	var tab = parseInt(Cookie.read('admin_tab'));
	if (tab > 0 && tab < tabs.size())
	{
		tabs.eq(tab).tab('show');
	}
	else
	{
		tabs.first().tab('show');
	}
	
	tabs.on('shown', function (e)
	{
		Cookie.write('admin_tab', tabs.index(e.target));
	})
});