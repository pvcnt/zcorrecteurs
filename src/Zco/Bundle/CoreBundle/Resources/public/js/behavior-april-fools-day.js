/**
 * Initialise le blocage des éléments du formulaire lors de sa soumission, 
 * pour éviter les doubles insertions.
 *
 * @provides vitesse-behavior-april-fools-day
 * @requires vitesse-behavior
 *           mootools
 *           mootools-more
 */
Behavior.create('april-fools-day', function(config)
{
    var dt = new Date();
    if (dt.getMonth() != 3 || dt.getDate() != 1)
    {
    	return;
	}

    function createCookie(name,value,days)
    {
    	if (days)
    	{
    		var date = new Date();
    		date.setTime(date.getTime()+(days*24*60*60*1000));
    		var expires = "; expires="+date.toGMTString();
    	}
    	else var expires = "";
    	document.cookie = name+"="+value+expires+"; path=/";
    }

    function readCookie(name)
    {
    	var nameEQ = name + "=";
    	var ca = document.cookie.split(';');
    	for (var i=0;i < ca.length;i++)
    	{
    		var c = ca[i];
    		while (c.charAt(0)==' ') c = c.substring(1,c.length);
    		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    	}
    	return null;
    }

    function eraseCookie(name)
    {
    	createCookie(name,"",-1);
    }

    // 10 secondes de fun toutes les 4 pages env
    if ((parseInt(Math.random() * 10) % 4) == 0)
    {
    	var rumbaTimer = null;
    	var cible = 'input, a';

    	//Seulement 5 fois au maximum.
    	var times = readCookie('poisson');
    	if (!times)
    	{
    		times = 0;
    	}
    	createCookie('poisson', parseInt(times) + 1, 1);
    	if (parseInt(times) >= 5)
    	{
    		return;
    	}

    	function rumba(ev)
    	{
    		var el = this;
    		if(!rumbaTimer)
    		{
    			rumbaTimer = window.setTimeout(function()
    			{
    				function remettreLiens() {
    					$$(cible).each(function(el) {
    						el.removeEvent('mouseover', rumba);
    						if(!el.oldCoordinates)
    							return;
    						var move = new Fx.Move(el, {
    							'offset': {
    								'x': el.oldCoordinates.x,
    								'y': el.oldCoordinates.y
    							},
    							'position': 'upperLeft',
    							'edge': 'upperLeft',
    							'transition': Fx.Transitions.Elastic.easeIn,
    							'duration': 1000
    						}).chain(function() {
    							el.setStyle('position', 'relative');
    							el.setPosition({x: 0, y: 0});
    						});
    						move.start();
    					});
    				}
    				var vibrations = 0;
    				function vibrer() {
    					window.resizeTo(screen.width - 20, screen.height);
    					if(vibrations > 50) {
    						window.resizeTo(screen.width, screen.height);
    						return remettreLiens();
    					}
    					var x, y;
    					if(vibrations % 2) {
    						x = Math.round(Math.random() * 100) - 30;
    						y = Math.round(Math.random() * 100) - 30;
    						window.moveBy(x, y);
    					}
    					else
    						window.moveBy(-x, -y);
    					vibrations++;
    					setTimeout(vibrer, 10);
    				}
    				vibrer();
    			}, 1000 * 10);
			}
			
    		var a = ((parseInt(Math.random() * 100) % (60 - 10 + 1)) + 10)
    			* (parseInt(Math.random() * 10) % 2 ? -1 : 1);
    		var b = ((parseInt(Math.random() * 100) % (60 - 10 + 1)) + 10)
    			* (parseInt(Math.random() * 10) % 2 ? -1 : 1);

    		if(!el.oldCoordinates)
    			el.oldCoordinates = {
    				x: el.getPosition().x,
    				y: el.getPosition().y
    			};

    		a += el.getPosition().x;
    		b += el.getPosition().y;

    		el.setStyle('position', 'absolute');

    		var move = new Fx.Move(el, {
    			'offset': {
    				'x': a,
    				'y': b
    			},
    			'position': 'upperLeft',
    			'edge': 'upperLeft',
    			'transition': Fx.Transitions.Bounce.easeOut,
    			'duration': 400
    		});
    		move.start();
    	}

    	$$(cible).each(function(el)
    	{
    		el.addEvent('mouseover', rumba);
    	});
    }
});