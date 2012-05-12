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


var ajax;

// Initialisation
function ajaxLoad() {
	ajax = null;
	if (window.XMLHttpRequest) {
		ajax = new XMLHttpRequest();

		// Évite un bug du navigateur Safari :
		if (ajax.overrideMimeType) {
			ajax.overrideMimeType("text/xml");
		}
	}
	else if (window.ActiveXObject) {
		try { ajax = new ActiveXObject("Msxml2.XMLHTTP"); }
		catch(e1) {
			try { ajax = new ActiveXObject("Microsoft.XMLHTTP"); }
			catch(e2) { ajax = null; /* XML Http  Request non pris en charge --> */ }
		}
	}
}

var en_edition;
var editing  = false;
var last_id = null;
var type = null;
var tag = null;

if (document.getElementById && document.createElement) {
	var butt = document.createElement('BUTTON');
	var buttext = document.createTextNode(' Modifier ! ');
	var mef = document.createElement('DIV');
	mef.id = 'mef';
	mef.className = 'boutons_zform';
	butt.appendChild(buttext);
	butt.className = 'edit';
	butt.onclick = saveEdit;
}

function catchIt(e) {
	/* Création de l'évènement */
	if (!document.getElementById || !document.createElement) return;
	if (!e) var obj = window.event.srcElement;
	else var obj = e.target;

	var texte = new RegExp("texte-.*");
	var titre = new RegExp("titre-.*");

	while (obj.nodeType != 1) {
		obj = obj.parentNode;
	}
	/* Lien ou textarea ou zone d'édition du texte => On s'arrête ! */
	if (obj.tagName == 'A' || obj.tagName == 'TEXTAREA' || obj.id == 'texte') return;
	if (obj.parentNode.className == _boutonsLettres.className) return;


	/* On remonte dans la hiérarchie du DOM */
	while ( !obj.id.match(titre) &&
	!obj.id.match(texte) &&
	obj.id != 'texte' &&
	obj.nodeName != 'HTML' &&
	obj.id != 'mef' &&
	obj.id != 'boxHandle'
	) {
		test = obj.previousSibling;
		if (test && test.nodeType == 1 && test.id.match(texte)) { obj = test; }
		else { obj = obj.parentNode; }
	}

	if (obj.className == 'noedit' || obj.id == 'boxHandle') { return; }
	if (editing && (obj.id != 'mef') || (obj.nodeName == 'HTML')) { saveEdit(); }

	if (obj.id.match(titre))
	{
		tag = "TITRE";
		last_id = obj.id;
		type = obj.nodeName;

		//var x = obj.innerHTML;

		var x;
		ajax.open('post', '/zcorrection/ajax-unparse-titre.html', false);
		ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		ajax.send('id='+encodeURIComponent(obj.id));

		if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				x = ajax.responseText;
			} else {
				x = "Erreur "+ajax.status+" : "+ajax.statusText;
			}
		}

		var y = document.createElement('input');
		y.id = 'texte';
		y.style.width = '700px';
		var z = obj.parentNode;
		z.insertBefore(y,obj);
		z.insertBefore(butt,obj);
		z.removeChild(obj);
		y.value = x;
		y.focus();
		en_edition = y;
	}
	else if (obj.id.match(texte))
	{
		tag = "TEXTE";
		last_id = obj.id;

		ShowBulle();
		var x;
		ajax.open('post', '/zcorrection/ajax-unparse-texte.html', false);
		ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

		ajax.send('id='+encodeURIComponent(obj.id));

		if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				x = ajax.responseText;
			} else {
				x = "Erreur "+ajax.status+" : "+ajax.statusText;
			}
		}

		var y = document.createElement('textarea');
		y.id = 'texte';

		y.style.width = '99%';
		y.style.display = 'block';
		y.rows = '12';
		var z = obj.parentNode;
		z.insertBefore(_boutonsLettres,obj);
		z.insertBefore(y,obj);
		z.insertBefore(mef,obj);
		z.insertBefore(butt,obj);
		z.removeChild(obj);
		y.value = x;
		y.focus();
		en_edition = y;

		HideBulle();
	}
	else
	{
		return;
	}
	editing = true;
}

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

function switchDiv(id) {
   if (document.all)
   {
      if (document.all[id].style.display == 'none')
	  {
		document.all[id].style.display = 'block';
	  }
	  else
	  {
		document.all[id].style.display = 'none';
	  }
   }
   else if(document.getElementById)
   {
      if (document.getElementById(id).style.display == 'none')
	  {
		document.getElementById(id).style.display = 'block';
	  }
	  else
	  {
		document.getElementById(id).style.display = 'none';
	  }
   }
   return false;
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

 ///////////////////////////////////////////
//   Récupère la position de la souris   //

function getMousePos(e)
{
   if (document.all)
   {
      posX=event.x+document.body.scrollLeft;
      posY=event.y+document.body.scrollTop;
   }
   else
   {
      posX=e.pageX;
      posY=e.pageY;
   }

   // Correction des positions négatives (l'info-bulle ne s'affiche pas au bout du pointeur, elle est un peu décallée)
   var finalPosX = posX-xOffset;
   if (finalPosX < 0) finalPosX=0;

   // On place l'info-bulle au bon endroit
   if (document.layers)
   {
      document.layers["bulle"].top = posY+yOffset;
      document.layers["bulle"].left = finalPosX;
   }
   if (document.all)
   {
      document.all['bulle'].style.top = posY+yOffset;
      document.all['bulle'].style.left = finalPosX;
   }
   else if (document.getElementById)
   {
      document.getElementById('bulle').style.top = posY+yOffset;
      document.getElementById('bulle').style.left = finalPosX;
   }
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
	mef.set('html', $('mise_en_forme').get('html'));
	ajaxLoad();
	zcoMarksInit();
});
