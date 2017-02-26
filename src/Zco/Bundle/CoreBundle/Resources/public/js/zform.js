/*
Auteur : karamilo
Derniere version : 30 juin 2005
Mail : pierre@bleu-provence.com
Si vous trouvez des bugs/commentaires, merci de me le signaler !
*/

var antiflood = false;
var last = 0;

var smilies = new Array(':magicien:',':colere:',':diable:',':ange:',':ninja:',
			'&gt;_&lt;',':pirate:',':zorro:',':honte:',':soleil:',':\'\\(',':waw:',
			':\\)',':D',';\\)',':p',':lol:',':euh:',':\\(',':o',':colere2:','o_O','\\^\\^',':\\-°');
var smilies_url = new Array('magicien.png','angry.gif','diable.png','ange.png',
			'ninja.png','pinch.png','pirate.png','zorro.png','rouge.png','soleil.png',
			'pleure.png','waw.png','smile.png','heureux.png','clin.png','langue.png',
			'rire.gif','unsure.gif','triste.png','huh.png','mechant.png',
			'blink.gif','hihi.png','siffle.png');
var height_avant = 200;
var height_avant_final = 500;

function edit_zform_height(id_textarea, id_prev, id_prev_final, diff)
{
	var champ = document.getElementById(id_textarea);
	var div_prev = document.getElementById(id_prev);
	var div_prev_final = document.getElementById(id_prev_final);

	height_avant = height_avant + Number(diff);
	if (height_avant < 200)
		height_avant = 200;
	if (height_avant > 2000)
		height_avant = 2000;

	height_avant_final = height_avant_final + Number(diff);
	if (height_avant_final < 200)
		height_avant_final = 200;
	if (height_avant_final > 2000)
		height_avant_final = 2000;

	change = height_avant+"px";
	change_final = height_avant_final+"px";

	champ.style.height = change;
	div_prev.style.height = change;
	div_prev_final.style.maxHeight = change_final;

	return false;
}

function storeCaret(id_textarea)
{
	champ = document.getElementById(id_textarea);
	if (champ.createTextRange)
		champ.curseur = document.selection.createRange().duplicate();
}

function balise(balise_debut, balise_fin, id_textarea, noSpaces)
{
	var champ = document.getElementById(id_textarea);
	var scroll = champ.scrollTop;
	balise_debut = remplace(balise_debut, '<br />', "\n");

	if(!noSpaces && balise_fin == '')
		balise_debut = ' ' + balise_debut + ' ';

	if (champ.curseur)
	{
		champ.curseur.text = balise_debut + champ.curseur.text + balise_fin;
	}
	else if (champ.selectionStart >= 0 && champ.selectionEnd >= 0)
	{
		var debut = champ.value.substring(0, champ.selectionStart);
		var entre = champ.value.substring(champ.selectionStart, champ.selectionEnd);
		var fin = champ.value.substring(champ.selectionEnd);
		champ.value = debut + balise_debut + entre + balise_fin + fin;
		champ.focus();
		champ.setSelectionRange(debut.length + balise_debut.length, champ.value.length - fin.length - balise_fin.length);
	}
	else
	{
		champ.value  += balise_debut + balise_fin;
		champ.focus();
	}
	champ.scrollTop = scroll;
}

function remplace(data, search, replace)
{
	var temp = data;
	var longueur = search.length;
	while (temp.indexOf(search) > -1)
	{
		pos = temp.indexOf(search);
		temp = (temp.substring(0, pos) + replace + temp.substring((pos + longueur), temp.length));
	}
	return temp;
}

function add_bal(nom, val, id_liste, id_textarea, id_prev)
{
	bal = document.getElementById(id_liste).value;
	if (bal != '')
		balise('<'+nom+' '+val+'="'+bal+'">','</'+nom+'>', id_textarea);
	else
		balise('<'+nom+'>','</'+nom+'>', id_textarea);
	if (document.getElementById(id_liste))
		document.getElementById(id_liste).options[0].selected = true;
}

function add_bal3(nom, val, id_liste, id_textarea)
{
	bal = document.getElementById(id_liste).value;
	if (bal != '')
		balise('<'+nom+' '+val+'="'+bal+'">','</'+nom+'>', id_textarea);
	else
		balise('<'+nom+'>','</'+nom+'>', id_textarea);
	if (document.getElementById(id_liste))
		document.getElementById(id_liste).options[0].selected = true;
}

function add_bal2(nom, val, id_textarea, id_prev)
{
	var champ = document.getElementById(id_textarea);
	var texte = '';
	if (nom == 'citation')
	{
		texte = 'Veuillez renseigner l\'auteur de la citation';
		bal = prompt(texte);
		if (!bal && nom == 'citation')
		{
			bal = 'Pas de titre';
			balise_debut = '<'+nom+'>';
		}
		else
			balise_debut = '<'+nom+' '+val+'="'+bal+'">';
		balise_fin = '</'+nom+'>';
	}
	else if (nom == 'lien')
	{
		if (champ.curseur)
			txt_selectionne = champ.curseur.text;
		else if (champ.selectionStart >= 0 && champ.selectionEnd >= 0)
			txt_selectionne = champ.value.substring(champ.selectionStart, champ.selectionEnd);
		else
			txt_selectionne = '';

		if (txt_selectionne.indexOf('http://') == 0
		|| txt_selectionne.indexOf('https://') == 0
		|| txt_selectionne.indexOf('ftp://') == 0
		|| txt_selectionne.indexOf('apt://') == 0)
		{
			texte = 'Veuillez indiquer le texte du lien';
			bal2 = prompt(texte);
			balise_debut = '<'+nom+' '+val+'="';
			balise_fin = '">'+bal2+'</'+nom+'>';
		}
		else if (txt_selectionne == '')
		{
			texte = 'Veuillez indiquer le lien';
			bal = prompt(texte);
			bal2 = prompt('Veuillez indiquer le texte du lien');
			balise_debut = '<'+nom+' '+val+'="'+bal+'">'+bal2;
			balise_fin = '</'+nom+'>';
		}
		else
		{
			texte = 'Veuillez indiquer le lien';
			bal = prompt(texte);
			balise_debut = '<'+nom+' '+val+'="'+bal+'">';
			balise_fin = '</'+nom+'>';
		}
	}
	else if (nom == 'email')
	{
		texte = 'Veuillez indiquer l\'email';
		bal = prompt(texte);
		balise_debut = '<'+nom+' '+val+'="'+bal+'">';
		balise_fin = '</'+nom+'>';
	}

	balise(balise_debut,balise_fin, id_textarea);

	if (document.getElementById(nom))
		document.getElementById(nom).options[0].selected = true;
}

function add_liste(id_textarea, id_prev)
{
	var texte = '';
	while (tmp = prompt('Saisir le contenu d\'une puce (si vous voulez arrêter ici, cliquez sur annuler)'))
		texte += '<puce>'+tmp+'</puce>'+"\n";
	balise('<liste>'+"\n"+texte,'</liste>', id_textarea);
}

function ouvrir_page(page,nom,x,y)
{
	window.open(page,nom,'toolbar=yes,personalbar=yes,titlebar=yes,location=yes,directories=yes,width='+x+',height='+y+',scrollbars=yes,resizable=yes');
}

function switch_activ(textarea,prev)
{
	div = document.getElementById(prev);
	if (document.getElementById('activ_'+textarea).checked == true)
	{
		div.style.display = 'block';
		storeCaret(textarea);
	}
	else
		div.style.display = 'none';
}

function full_preview(id_textarea, id_prev_final)
{
	var button = $('lancer_apercu_' + id_textarea);
	button.disabled = true;

	xhr = new Request({method: 'post', url: Routing.generate('zco_parser_api_parse'), onSuccess: function(text, xml){
		$(id_prev_final).set('html', text);
	}});
	xhr.send('texte='+encodeURIComponent($(id_textarea).value));

	button.disabled = false;
}

function switch_smilies(id_textarea)
{
	id1 = 'smilies1_' + id_textarea;
	id2 = 'smilies2_' + id_textarea;

	if($(id1).getStyle('display') == 'none')
	{
		$(id1).setStyle('display', 'block');
		$(id2).setStyle('display', 'none');
	}
	else
	{
		$(id1).setStyle('display', 'none');
		$(id2).setStyle('display', 'block');
	}
}

function add_image_parent(valeur, id_textarea)
{
	var textarea = opener.document.getElementById(id_textarea);
	textarea.focus();
	if (window.ActiveXObject) { // Il s'agit d'internet explorer
		var range = opener.document.selection.createRange();
		var selection = range.text;

		range.text = selection + valeur;
		range.moveStart("character", -valeur.length - selection.length);
		range.moveEnd("character", -valeur.length);
		range.select();
	} else { // Pour les autres
		var startSelection   = textarea.value.substring(0, textarea.selectionStart);
		var selection = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
		var endSelection     = textarea.value.substring(textarea.selectionEnd);

		textarea.value = startSelection + selection + valeur + endSelection;
		textarea.focus();
		textarea.setSelectionRange(startSelection.length, startSelection.length + selection.length);
	}
}

var _boutonsLettres = undefined;
window.addEvent('domready', function() {
	var boutons = ['À', 'Ç', 'É', 'È', '«', '»', '−', '—', '…', 'œ'];
	var ajouterCaractere = function(e) {
		var textarea = this.parentNode.parentNode.getElementsByTagName('textarea')[0];
		if(textarea)
			balise(this.value, '', textarea.id, true);
	};

	if(!_boutonsLettres) {
		_boutonsLettres = new Element('div', { 'class': 'zform_caracteres' });
		boutons.each(function(b) {
			var e = new Element('input', {
				'type': 'button',
				'value': b,
				'class': 'zform_bouton_caractere btn'
			});
			e.addEvent('click', ajouterCaractere);
			e.inject(_boutonsLettres);
		});
	}

	// Mini zForm pour les réponses rapides
	var repsRapides = $$('.zcode_rep_rapide');
	if(repsRapides.length)
	{
		var balises = ['gras', 'italique', 'souligne', 'lien'];
		var conteneur = new Element('span');
		var ajouterBalise = function(e) {
			var textarea = this.parentNode.parentNode.parentNode.getElementsByTagName('textarea')[0];
			if(textarea)
			{
				if (this.alt == 'lien')
					add_bal2('lien', 'url', textarea.id)
				else
					balise('<'+this.alt+'>', '</'+this.alt+'>', textarea.id, true);
			}
		};

		balises.each(function(b) {
			new Element('img', {
				'src': '/bundles/zcocore/img/zcode/'+b+'.png',
				'alt': b
			}).addEvent('click', ajouterBalise)
			.inject(conteneur);
		});
		var o = '';
		for (var i = 0; i < 15; i++)
			o += '&nbsp;';
		new Element('span').set('html', o).inject(conteneur);
		repsRapides.addEvent('focus', function(e) {
			if(this.focused)
				return;
			_boutonsLettres.inject(this, 'after');
			conteneur.inject(_boutonsLettres, 'top');
			this.focused = true;
		});
	}
	$$('.zform textarea').each(function(e) {
		_boutonsLettres.clone().inject(e, 'before');
	});
	$$('.zform_bouton_caractere').each(function(e) {
		e.addEvent('click', ajouterCaractere);
	});


});
