function drop_zco_mark(id, url)
{
	url = _ajax_data['page_courante']+'#'+url;
	request = new Request({'url': '/zcorrection/ajax-drop-mark.html', 'method': 'post',
		'onSuccess': function(text, xml){
			zMessage.info('La marque de correction a bien été déposée.');
		}});
	request.send('cid='+id+'&url='+url);
	return false;
}

function zcoMarksInit()
{
	url = _ajax_data['zco_mark'];
	$$('div').each(function(elem, i){
		if($chk(elem.id) && _ajax_data['zco_mark'] == _ajax_data['page_courante']+'#'+elem.id)
		{
			elem.addClass('zco_marque');
		}
	});
}

$$('div[id^=titre-]').each(function(titre) {
	titre.addEvent('click', function(e) {
		var request = new Request({
			'url'    : '/zcorrection/ajax-unparse-titre.html',
			'async'  : false,
			noCache: true,
			onSuccess: function(responseText, responseXML) {
				var input = new Element('input', {
					'id' : 'texte',
					'styles' : {
						'width': '700px'
					},
					'value' : responseText
				});
				var valider = new Element('button',{'class':'edit','value':'Modifier !'});
				valider.addEvent('click', function(e) {
					// saveEdit()
				});
				input.replaces(titre);
				valider.inject(input, 'after');
				input.focus();
			}
			onFailure: function(xhr) {
				alert("Erreur "+xhr.status+" : "+xhr.statusText);
			}
		});
		request.send({id: titre.get('id')});
	});
});

$$('div[id^=texte-]').each(function(texte) {
	texte.addEvent('click', function(e) {
		ShowBulle();
		var request = new Request({
			url:     '/zcorrection/ajax-unparse-texte.html',
			async:   false,
			noCache: true,
			onSuccess: function(responseText, responseXML) {
				var textarea = new Element('textarea', {
					'id' :'texte',
					'styles': {
						width: '99%',
						display:'block',
					},
					'rows' => 12,
					'value' => responseText
				});
				var valider = new Element('button',{'class':'edit','value':'Modifier !'});
				var mef = new Element('div',{'id':'mef','class':'boutons_zform','html':$('mise_en_forme').get('html')});
				valider.addEvent('click', function(e) {
					// saveEdit()
				});
				textarea.replaces($(obj));
				$(_boutonsLettres).inject(y, 'before');
				$(valider).inject(y, 'after');
				$(mef).inject(y, 'after');
				y.focus();
			}
			onFailure: function(xhr) {
				alert("Erreur "+xhr.status+" : "+xhr.statusText);
			},
			onComplete: function() {
				HideBulle();
			}
		});
		request.send({id: texte.get('id')});
	});
});

function saveEdit()
{
	var area = $('texte');

	if (tag == "TITRE")
	{
		ShowBulle();
		var x;
		ajax.open('post', '/zcorrection/ajax-parse-titre.html', false);
		ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

		ajax.send('id='+last_id+'&texte='+encodeURIComponent(area.value).replace(/\+/g,'%2B'));

		if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				x = ajax.responseText;
			} else {
				x = "Erreur "+ajax.status+" : "+ajax.statusText;
			}
		}

		var y = document.createElement(type);
		y.id = last_id;
		var z = area.parentNode;
		y.innerHTML = htmlspecialchars(x, 'ENT_COMPAT');
		z.insertBefore(y,area);
		z.removeChild(area);
		z.removeChild(document.getElementsByTagName('button')[0]);

		HideBulle();
	}
	else if (tag == "TEXTE")
	{
		ShowBulle();

		var x;
		ajax.open('post', '/zcorrection/ajax-parse-texte.html', false);
		ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

		ajax.send('id='+last_id+'&texte='+encodeURIComponent(area.value).replace(/\+/g,'%2B'));

		if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				x = ajax.responseText;
			} else {
				x = "Erreur "+ajax.status+" : "+ajax.statusText;
			}
		}

		var y = document.createElement('div');
		var z = area.parentNode;
		y.innerHTML = x;
		y.id = last_id;
		y.className = 'p';
		y.addEvent('mouseover', function(){ y.addClass('hover'); });
		y.addEvent('mouseout', function(){ y.removeClass('hover'); });
		z.insertBefore(y,area);
		z.removeChild(area);
		z.removeChild(document.getElementsByTagName('button')[0]);
		z.removeChild(document.getElementById('mef'));
		$$('.' + _boutonsLettres.className).each(function(e) { e.dispose(); });

		HideBulle();
	}
	editing = false;
	tag = null;
}

 //////////////////////////////////////
//   Initialisation des variables   //

var xOffset = -12; yOffset = 8;

 //////////////////////////////
//   Affiche l'info-bulle   //

function ShowBulle()
{
	$('ajax_loader').setStyle('display', 'block');
}

 /////////////////////////////
//   Masque l'info-bulle   //

function HideBulle()
{
	$('ajax_loader').setStyle('display', 'none');
}


function htmlspecialchars(string, quote_style) {
   // Convert special characters to HTML entities
   //
   // version: 810.114
   // discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_htmlspecialchars

   // +   original by: Mirek Slugen
   // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
   // +   bugfixed by: Nathan
   // +   bugfixed by: Arno
   // *     example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
   // *     returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'

   string = string.toString();

   // Always encode
   string = string.replace(/&/g, '&amp;');
   string = string.replace(/</g, '&lt;');
   string = string.replace(/>/g, '&gt;');

   // Encode depending on quote_style
   if (quote_style == 'ENT_QUOTES') {
	   string = string.replace(/"/g, '&quot;');
	   string = string.replace(/'/g, '&#039;');
   } else if (quote_style != 'ENT_NOQUOTES') {
	   // All other cases (ENT_COMPAT, default, but not ENT_NOQUOTES)
	   string = string.replace(/"/g, '&quot;');
   }

   return string;
}

window.addEvent('domready', function(){
	document.onclick = catchIt;
	ajaxLoad();
	zcoMarksInit();
});
