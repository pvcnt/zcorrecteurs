/**
 * Retient le dernier onglet visité par l'utilisateur et le recharge à sa prochaine visite.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 * @provides vitesse-behavior-admin-homepage
 * @requires vitesse-behavior @ZcoCoreBundle/Resources/public/js/bootstrap.js
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