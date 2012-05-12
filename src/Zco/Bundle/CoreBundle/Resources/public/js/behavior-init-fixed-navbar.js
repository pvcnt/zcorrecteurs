var win = jQuery(window);
var body = jQuery('body');
var nav = jQuery('div.navbar');
var navTop = jQuery('.navbar-inner').length && jQuery('.navbar-inner').offset().top;
var isFixed = 0;

//processScroll();
//win.on('scroll', processScroll);

function processScroll()
{
	var scrollTop = win.scrollTop();
	if (scrollTop >= navTop && !isFixed)
	{
		isFixed = 1;
		nav.removeClass('navbar-static');
		nav.addClass('navbar-fixed');
		nav.css('margin-bottom', 18);
		body.css('margin-top', 40);
	}
	else if (scrollTop <= navTop && isFixed)
	{
		isFixed = 0;
		nav.removeClass('navbar-fixed');
		nav.addClass('navbar-static');
		body.css('margin-top', 0);
	}
}