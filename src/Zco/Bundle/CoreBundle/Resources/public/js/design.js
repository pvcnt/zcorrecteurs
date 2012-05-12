/**
 * @requires jquery-no-conflict
 *           @ZcoCoreBundle/Resources/public/js/libs/bootstrap.js
 */

var win = jQuery(window);
var body = jQuery('body');
var nav = jQuery('div.navbar');
var navTop = jQuery('.navbar-inner').offset().top;
var isFixed = 0;

processScroll();
win.on('scroll', processScroll);

function processScroll()
{
	var scrollTop = win.scrollTop();
	if (scrollTop >= navTop && !isFixed)
	{
		isFixed = 1;
		nav.addClass('navbar-fixed-top');
		body.css('margin-top', 40);
	}
	else if (scrollTop <= navTop && isFixed)
	{
		isFixed = 0;
		nav.removeClass('navbar-fixed-top');
		body.css('margin-top', 0);
	}
}