<script type="text/javascript">
$('emplacement').addEvent('change', function(){
	emplacement = $('emplacement').value;
	if (emplacement == 'menu' || emplacement == 'pied')
	{
		$('row_image').setStyle('display', 'none');
		$('row_contenu').setStyle('display', 'block');
	}
	else if (emplacement == 'header')
	{
		$('row_image').setStyle('display', 'block');
		$('row_contenu').setStyle('display', 'block');
	}
	update_pub();
});
$('contenu_js').addEvent('click', function(){
	if ($('contenu_js').checked)
	{
		$('contenu').value = get_pub_html();
		update_pub();
	}
	else
	{
		update_pub();
	}
});
$('titre').addEvent('keyup', update_pub);
$('contenu').addEvent('keyup', update_pub);
$('url_cible').addEvent('change', update_pub);

function update_pub()
{
	emplacement = $('emplacement').value;

	<?php if (verifier('publicite_js')){ ?>
	if ($('contenu_js').checked)
	{
		pub = $('contenu').value;
	}
	else <?php } ?>pub = get_pub_html();

	if (emplacement == 'menu')
	{
		pub = '<div class="sidebar"><div class="bloc partenaires"><h4>Partenaires</h4><ul>'+
			pub+'</ul></div></div>';
	}
	else if (emplacement == 'pied')
	{
		pub = '<div class="footer center centre"><p class="links blanc" style="margin-top: 20px;">'+
			'Partenaires : '+pub+
			'</p></div>';
	}
	else
	{
		pub = '<p class="italique centre">Aucun aperçu de disponible pour ce positionnement. Nous contacter en cas de besoin spécifique.</p>';
	}

	$('preview_pub').set('html', pub);
}

function get_pub_html()
{
	emplacement = $('emplacement').value;

	if (emplacement == 'menu')
	{
		pub = '<li><a href="'+$('url_cible').value+'" title="'+$('titre').value+'" rel="'+$('contenu').value+'">'+$('titre').value+'</a></li>';
	}
	else if (emplacement == 'pied')
	{
		pub = '<a href="'+$('url_cible').value+'" title="'+$('contenu').value+'">'+$('titre').value+'</a>';
	}
	else
	{
		pub = '';
	}
	return pub;
}
</script>