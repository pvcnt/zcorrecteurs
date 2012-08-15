/**
  * @requires jquery-no-conflict
  *           bootstrap-js
  *           @ZcoCoreBundle/Resources/public/js/libs/zMessage.js
  */
jQuery(function() {
	var win = jQuery(window);

	//Rend la barre de navigation fixe en haut de page dès qu'elle devrait 
	//devenir cachée.
	
	var body = jQuery('body');
	var nav = jQuery('div.navbar');
	if (nav.length)
	{
		var navTop = jQuery('.navbar-inner').offset().top;
		var isFixed = 0;

		var processScroll = function()
		{
			var scrollTop = win.scrollTop();
			if (scrollTop >= navTop && !isFixed) {
				isFixed = 1;
				nav.addClass('navbar-fixed-top');
				body.css('margin-top', 40);
			} else if (scrollTop <= navTop && isFixed) {
				isFixed = 0;
				nav.removeClass('navbar-fixed-top');
				body.css('margin-top', 0);
			}
		};

		processScroll();
		win.on('scroll', processScroll);
	}

	//Active le code Konami. Yeah !
    var konami = [];
    win.on('keydown', function(event){
        konami.push(event.keyCode);
        if (konami.toString().indexOf("38,38,40,40,37,39,37,39,66,65") >= 0) {
        	zMessage.info('Bon jeu !');
            var script = document.createElement("script");
			script.setAttribute('type', 'text/javascript');
			script.onreadystatechange = function() {
				if (script.readyState == 'loaded' || script.readyState == 'complete') {
					alert('loaded');
				}
			};
			script.src = "/bundles/zcocore/js/vendor/asteroids.js";
			document.getElementsByTagName('head')[0].appendChild(script);

            konami = [];
        }
	});
});