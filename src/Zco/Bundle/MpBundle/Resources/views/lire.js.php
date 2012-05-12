window.addEvent('domready', function() {
	var lien = $('ajouter-participant');
	if (!lien)
		return;

	lien.addEvent('click', function(e) {
		e.stop();
		var xhr = new Request({
			method: 'get',
			url:    lien.href,
			onSuccess: function (data) {
				Spinner.hide();
				var cont = lien.parentNode;
				var href = lien.href;
				cont.old = cont.innerHTML;

				cont.innerHTML = data;
				var form = cont.getElementsByTagName('form')[0];
				form.action = href;

				var iframe = new Element('iframe')
					.setStyle('display', 'none')
					.set('name', 'ajouter-participant')
					.inject('body');
				form.set('target', 'ajouter-participant');
				form.getElementsByTagName('input')[0].focus();

				form.addEvent('submit', function(e) {
					Spinner.show();
					var interval = window.setInterval(function() {
						var doc = iframe.contentWindow.document;
						var html = doc.body.innerHTML;

						if (html.length > 0) {
							window.clearInterval(interval);
							if (html.indexOf('<' + 'div id="message_0">') > 0) {
								var msg = doc.getElementsByTagName('span')[0]
									.innerHTML;
								zMessage.error(msg);
							}
							else if (html.indexOf('<' + 'div id="message_1">') > 0 ||
							         html.indexOf('<' + 'div id="message_2">') > 0) {
								var msg = doc.getElementsByTagName('span')[0]
									.innerHTML;
								zMessage.info(msg);
							}
							else if(html.indexOf('<' + 'p class="UI_infobox">') > 0) {
								var msg = doc.getElementById('content')
									.getElementsByTagName('p')[0]
									.innerHTML;
								zMessage.info(msg);
							}
							else if(html.indexOf('<' + 'p class="UI_errorbox">') > 0) {
								var msg = doc.getElementById('content')
									.getElementsByTagName('p')[0]
									.innerHTML;
								zMessage.error(msg);
							}
							else {
								zMessage.error('Erreur inconnue');
							}

							iframe.dispose();
							cont.innerHTML = cont.old;
							ajouterParticipant();
							Spinner.hide();
						}
					}, 500);
				});
			}
		});
		Spinner.show();
		xhr.send('xhr=1');
	});
});