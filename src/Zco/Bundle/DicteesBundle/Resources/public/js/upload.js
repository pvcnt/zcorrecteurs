/**
 * Suivi de la progression des uploads.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */

window.addEvent('domready', function() {
	var activerSuiviUpload = false; // Suivi impossible quand PHP est en CGI

	var	barreProgression = null, calque = null, g = null, iframe = null,
		delaiRafraichissement = 1000,
		idSuiviUpload = parseInt(Math.random() * 100000),
		avgSpeed = 0, oldSpeed = 0, numSpeeds = 0,
		oldRate = 0;

	function getSpeed(prev, current) {
		var speed = (((current - prev) / delaiRafraichissement) * 1000);
		if(!speed) return oldSpeed;

		avgSpeed = (avgSpeed * numSpeeds + speed) / ++numSpeeds;
		return oldSpeed = formatSize(speed);
	}

	function formatSize(sp) {
		if(sp < 1024)		return Math.round(sp * 100) / 100 + ' o';
		if((sp /= 1024) < 1024)	return Math.round(sp * 100) / 100 + ' ko';
		if((sp /= 1024) < 1024)	return Math.round(sp * 100) / 100 + ' Mo';
		return sp + 'Go';
	}

	function formatTemps(s) {
		s = Math.abs(s);
 	        m = parseInt(s / 60);
 	        h = parseInt(m / 60);

 	        h = h % 24;
 	        m = m % 60;
 	        s = s % 60;

		var out = s + ' s';
		if(m > 0) out = m + ' m ' + out;
		if(h > 0) out = h + ' h ' + out;
		return out;
	}

	function tempsRestant(vitesse, actuel, total) {
		var restant = total - actuel;
		var secondes = restant / vitesse;

		return formatTemps(secondes);
	}

	function uploadTermine() {
		spinner.dispose();

		setTimeout(function() {
			new Fx.Slide(calque, {'mode':'vertical'})
			.slideOut().chain(function() {
				calque.dispose();
				var json = JSON.decode(
					iframe.contentWindow.document
					.documentElement.getElementsByTagName('body')[0]
					.innerHTML);
				zMessage.display(json.msg, json.type, {'duration':2000});
				setTimeout('window.location = "' + json.url + '"', 2500);
			});
		}, 1000);
	};
	function suivreUpload() {
		var xhr = new Request.JSON({
			method		: 'post',
			url		: '/xhr_light.php',
			onFailure	: function() { setTimeout(suivreUpload, delaiRafraichissement * 2); },
			onSuccess	: function(data) {
				if(data)
				{
					$('upload_vitesse').set('text', getSpeed(data.rate, oldRate) + '/s');
					$('upload_vitesse_moyenne').set('text', formatSize(avgSpeed) + '/s');
					oldRate = data.rate;
					var pourcentage = parseInt(data.current * 10000 / data.total) / 100;
					barreProgression.setStyle('width', pourcentage + '%');
					$('upload_progression').set('text', pourcentage + ' %');
					$('upload_temps_ecoule').set('text',
						formatTemps(data.current_time - data.start_time));
					$('upload_temps_restant').set('text',
						tempsRestant(data.rate, data.current, data.total));
					if(data.done == 1)
						uploadTermine();
					else
						setTimeout(suivreUpload, delaiRafraichissement);
				}
				else
					setTimeout(suivreUpload, delaiRafraichissement);
			}
		});
		xhr.send('act=suivi_upload&key=' + idSuiviUpload);
	};
	function envoyer() {
		// Création du calque
		var taille = {
			'x'	: 400,
			'y'	: 200
		};
		calque = new Element('div').setStyles({
			'top'		: '0px',
			'left'		: '0px',
			'z-index'	: '20',
			'width'		: '100%',
			'height'	: '100%',
			'opacity'	: '0.8',
			'position'	: 'fixed',
			'background'	: 'black'})
			.inject($('body'))
			.slide('hide')
			.slide('in');
		var div = new Element('div').setStyles({
			'top'		: '50%',
			'left'		: '50%',
			'z-index'	: '21',
			'padding'	: '10px',
			'background'	: 'white',
			'position'	: 'relative',
			'text-align'	: 'center',
			'width'		: taille.x + 'px',
			'height'	: taille.y + 'px',
			'margin-top'	: '-' + parseInt(taille.y / 2) + 'px',
			'margin-left'	: '-' + parseInt(taille.x / 2) + 'px'})
			.inject(calque);
		new Element('h1')
			.set('text', 'Envoi en cours, merci de patienter…')
			.setStyle('padding-left', '0px')
			.inject(div);

		spinner = new Element('img', {
			'src'	: '/img/ajax-loader.gif',
			'alt'	: 'Chargement'})
			.inject(div);

		if(!activerSuiviUpload)
			spinner.setStyle('margin-top', '60px');
		else {
			barreProgression = new Element('p',
				{'class' : 'upload_barre_progression'})
				.inject(div);
			new Element('p', {'id' : 'upload_progression'})
				.set('text', '0 %')
				.setStyle('margin-bottom', '30px')
				.inject(div);

			var tableau = new Element('table').setStyle('width', '100%').setStyle('text-align', 'left').inject(div);

			var ligne1 = new Element('tr').inject(tableau);
			new Element('th').set('text', 'Temps :').inject(ligne1).setStyle('width', '25%');
			new Element('td', {'id':'upload_temps_ecoule'}).inject(ligne1).setStyle('width', '25%');
			new Element('th').set('text', 'Vitesse :').inject(ligne1).setStyle('width', '25%');
			new Element('td', {'id':'upload_vitesse'}).inject(ligne1).setStyle('width', '25%');

			var ligne2 = new Element('tr').inject(tableau);
			new Element('th').set('text', 'Restant :').inject(ligne2).setStyle('width', '25%');
			new Element('td', {'id':'upload_temps_restant'}).inject(ligne2).setStyle('width', '25%');
			new Element('th').set('text', 'Moyenne :').inject(ligne2).setStyle('width', '25%');
			new Element('td', {'id':'upload_vitesse_moyenne'}).inject(ligne2).setStyle('width', '25%');

			setTimeout(suivreUpload, delaiRafraichissement);
		}
	};

	var tmp = $$('input'), inputs = [];
	for(var i = 0; i < tmp.length; i++)
		if(tmp[i].type == 'file')
			inputs.push(tmp[i]);
	if(inputs.length > 0) {
		// Préchargement de l'image de chargement
		spinner = new Element('img', {
			'src'	: '/img/ajax-loader.gif',
			'alt'	: 'Chargement'})
			.setStyle('display', 'none')
			.inject($('body'));
		// Cet iframe recevra les résultats des envois des formulaires
		iframe = new Element('iframe', {
			'name'	: 'suivi_upload'})
			.setStyle('display', 'none')
			.inject($('body'));

		var formulaires = {};
		for(var i = 0; i < inputs.length; i++) {
			var form = inputs[i];
			while(form && form.nodeName != 'FORM')
				form = form.parentNode;
			if(!form)
				continue;

			var uid = $(form).uid;
			if(formulaires[uid])
				continue;
			formulaires[uid] = 1;

			if(activerSuiviUpload) {
				new Element('input', {
					'type'	: 'hidden',
					'name'	: 'APC_UPLOAD_PROGRESS',
					'value'	: idSuiviUpload})
					.inject(form);
				new Element('input', {
					'type'	: 'hidden',
					'name'	: 'ajax',
					'value'	: '1'})
					.inject(form);
				// Redirection du formulaire vers un iframe caché
				form.set('target', 'suivi_upload');
			}

			// Modification du comportement lors du clic sur 'Envoyer'
			form.onsubmit = envoyer;
		}
	}
});
