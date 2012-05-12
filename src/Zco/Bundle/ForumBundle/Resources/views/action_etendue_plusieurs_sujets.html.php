<?php
if(verifier('deplacer_sujets', $_GET['id']))
{?>
<script type="text/javascript">
	i = 0;
	function en_route()
	{
		xhr = new Request({method: 'get', url: '/forum/ajax-deplacement-massif.html', onSuccess: afficher_deplacer_sujet});
		xhr.send('f='+encodeURIComponent('<?php echo $_GET['id']; ?>'), '');
	}
	function afficher_deplacer_sujet(texte)
	{
		$('deplacer_sujets').set('html', unescape(texte));
	}
	function changer_select(valeur)
	{
		if(valeur == 'deplacer' && i != 1)
		{
			$('deplacer_sujets').setStyle('display', 'inline');
			$('deplacer_sujets').set('html', '<img src="/img/ajax-loader.gif" alt="" />');
			setTimeout("en_route();", 500);
			i = 1;
		}
		else if(valeur == 'deplacer' && i == 1)
		{
			$('deplacer_sujets').setStyle('display', 'inline');
		}
		else
		{
			$('deplacer_sujets').setStyle('display', 'none');
		}
	}
</script>
<?php
}
?>
	<div class="btn_supprimer_msg">
		<input type="button" name="select_all" id="select_all" onclick="switch_checkbox()" value="Tout sélectionner" />
		<select id="action" name="action" onchange="changer_select(this.value);">
		<optgroup label="Action étendue à plusieurs sujets">
			<?php
			if(verifier('epingler_sujets', $_GET['id']))
			{
				echo '<option value="annonce">Épingler</option>'
				    .'<option value="plus_annonce">Ne plus épingler</option>';
			}
			if(verifier('mettre_sujet_favori', $_GET['id']))
			{
				echo '<option value="favori">Mettre en favoris</option>'
				    .'<option value="nonfavori">Enlever des favoris</option>';
			}
			if(verifier('resolu_sujets', $_GET['id']))
			{
				echo '<option value="resolu">Marquer comme résolus</option>'
				    .'<option value="nonresolu">Marquer comme non-résolus</option>';
			}
			if(verifier('fermer_sujets', $_GET['id']))
			{
				echo '<option value="fermer">Fermer</option>'
				    .'<option value="ouvrir">Ouvrir</option>';
			}
			if(verifier('deplacer_sujets', $_GET['id']))
			{
				echo '<option value="deplacer">Déplacer</option>';
			}
			if(verifier('corbeille_sujets', $_GET['id']))
			{
				if(!empty($_GET['trash']))
				{
					echo '<option value="restaurer">Restaurer</option>';
				}
				else
				{
					echo '<option value="corbeille">Mettre à la corbeille</option>';
				}
			}
			if(verifier('suppr_sujets', $_GET['id']))
			{
				echo '<option value="supprimer">Supprimer</option>';
			}
			if(verifier('connecte'))
			{
				echo '<option value="lu">Marquer comme lu</option>'
				    .'<option value="nonlu">Marquer comme non lu</option>';
			}
			?>
		</optgroup>
		</select>
		<div id="deplacer_sujets" style="display:none;">
		</div>
		<input type="submit" name="send" value="Envoyer" accesskey="s" />
	</div>
