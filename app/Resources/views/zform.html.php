<?php
//Définition de l'id
if(!isset($id))
	$id = 'texte';

if (!isset($texte) && isset($_POST[$id]))
	$texte = $_POST[$id];
//elseif(!isset($texte) && isset($InfosMessage['message_texte']))
//	$texte = htmlspecialchars_decode($InfosMessage['message_texte']);
elseif (!isset($texte))
	$texte = '';

if (!function_exists('zform_inclues'))
{
	function zform_inclues($nb = 0)
	{
		static $nb_zform_inclue = 0;
		if (!$nb)
		{
			return $nb_zform_inclue;
		}
		
		$nb_zform_inclue += $nb;
	}
}

zform_inclues(1);
$view['vitesse']->requireResources(array(
    '@ZcoCoreBundle/Resources/public/css/zcode.css',
    '@ZcoCoreBundle/Resources/public/css/zform.css',
    '@ZcoCoreBundle/Resources/public/js/save.js',
    '@ZcoCoreBundle/Resources/public/js/zform.js',
));

//Ajout de la sauvegarde de zForm si son id n'est pas « texte ».
if($id != 'texte'){
?>
<script type="text/javascript">
	window.addEvent('domready', function(){
		if($chk($('<?php echo $id; ?>')))
			setTimeout('save_zform(\'<?php echo $id; ?>\')', shortTime);
	});
</script>
<?php } ?>

<div class="boutons_zform">
	<span class="boutons">
		<img src="/bundles/zcocore/img/zcode/gras.png" alt="Gras" title="Gras" onclick="balise('&lt;gras&gt;','&lt;/gras&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/italique.png" alt="Italique" title="Italique" onclick="balise('&lt;italique&gt;','&lt;/italique&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/souligne.png" alt="Souligné" title="Souligné" onclick="balise('&lt;souligne&gt;','&lt;/souligne&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/barre.png" alt="Barré" title="Barré" onclick="balise('&lt;barre&gt;','&lt;/barre&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
	</span>

	<span class="boutons">
		<img src="/bundles/zcocore/img/zcode/liste.png" alt="Liste à puces" title="Liste à puces" onclick="add_liste('<?php echo $id; ?>','prev_<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/citation.png" alt="Citation" title="Citation" onclick="add_bal2('citation','nom','<?php echo $id; ?>','prev_<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/image.png" alt="Image" title="Image" onclick="balise('&lt;image&gt;','&lt;/image&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/lien.png" alt="Lien" title="Lien" onclick="add_bal2('lien','url','<?php echo $id; ?>','prev_<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/secret.png" alt="Secret" title="Secret" onclick="balise('&lt;secret&gt;','&lt;/secret&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
	</span>

	<span class="boutons">
		<img src="/bundles/zcocore/img/zcode/exposant.png" alt="Exposant" title="Exposant" onclick="balise('&lt;exposant&gt;','&lt;/exposant&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/indice.png" alt="Indice" title="Indice" onclick="balise('&lt;indice&gt;','&lt;/indice&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
	</span>

	<span class="boutons">
		<img src="/bundles/zcocore/img/zcode/info.png" alt="Information" title="Information" onclick="balise('&lt;information&gt;','&lt;/information&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/attention.png" alt="Attention" title="Attention" onclick="balise('&lt;attention&gt;','&lt;/attention&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/erreur.png" alt="Erreur" title="Erreur" onclick="balise('&lt;erreur&gt;','&lt;/erreur&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/question.png" alt="Question" title="Question" onclick="balise('&lt;question&gt;','&lt;/question&gt;', '<?php echo $id; ?>');" class="bouton_cliquable" />
	</span>
	<br />
	<span class="cleaner">
		<select id="position_<?php echo $id; ?>" onchange="add_bal('position','valeur','position_<?php echo $id; ?>','<?php echo $id; ?>','prev_<?php echo $id; ?>')">
			<option class="opt_titre" selected="selected">Position</option>
			<option value="justifie">Justifié</option>
			<option value="gauche">À gauche</option>
			<option value="centre" class="centre">Centré</option>
			<option value="droite" class="droite">À droite</option>
		</select>

		<select id="flottant_<?php echo $id; ?>" onchange="add_bal('flottant','valeur','flottant_<?php echo $id; ?>','<?php echo $id; ?>','prev_<?php echo $id; ?>')">
			<option class="opt_titre" selected="selected">Flottant</option>
			<option value="gauche">À gauche</option>
			<option value="droite" class="droite">À droite</option>
		</select>

		<select id="taille_<?php echo $id; ?>" onchange="add_bal('taille','valeur','taille_<?php echo $id; ?>','<?php echo $id; ?>','prev_<?php echo $id; ?>')">
			<option class="opt_titre" selected="selected">Taille</option>
			<option value="ttpetit">Très très petit</option>
			<option value="tpetit">Très petit</option>
			<option value="petit">Petit</option>
			<option value="gros">Gros</option>
			<option value="tgros">Très gros</option>
			<option value="ttgros">Très très gros</option>
		</select>

		<select id="couleur_<?php echo $id; ?>" onchange="add_bal('couleur','nom','couleur_<?php echo $id; ?>','<?php echo $id; ?>','prev_<?php echo $id; ?>')">
			<option class="opt_titre" selected="selected">Couleur</option>
			<option value="rose" class="rose">rose</option>
			<option value="rouge" class="rouge">rouge</option>
			<option value="orange" class="orange">orange</option>
			<option value="jaune" class="jaune">jaune</option>
			<option value="vertc" class="vertc">vertc</option>
			<option value="vertf" class="vertf">vertf</option>
			<option value="olive" class="olive">olive</option>
			<option value="turquoise" class="turquoise">turquoise</option>
			<option value="bleugris" class="bleugris">bleugris</option>
			<option value="bleu" class="bleu">bleu</option>
			<option value="marine" class="marine">marine</option>
			<option value="violet" class="violet">violet</option>
			<option value="marron" class="marron">marron</option>
			<option value="noir" class="noir">noir</option>
			<option value="gris" class="gris">gris</option>
			<option value="argent" class="argent">argent</option>
			<option value="blanc" class="blanc">blanc</option>
		</select>

		<select id="police_<?php echo $id; ?>" onchange="add_bal('police','nom','police_<?php echo $id; ?>','<?php echo $id; ?>','prev_<?php echo $id; ?>')">
			<option class="opt_titre" selected="selected">Police</option>
			<option value="arial" class="arial">arial</option>
			<option value="times" class="times">times</option>
			<option value="courier" class="courier">courier</option>
			<option value="impact" class="impact">impact</option>
			<option value="geneva" class="geneva">geneva</option>
			<option value="optima" class="optima">optima</option>
		</select>

		<select id="semantique_<?php echo $id; ?>" onchange="balise('&lt;titre'+this.value+'&gt;','&lt;/titre'+this.value+'&gt;','<?php echo $id; ?>');this.options[0].selected = true;">
			<option class="opt_titre" selected="selected">Sémantique</option>
			<option value="1">Titre 1</option>
			<option value="2">Titre 2</option>
		</select>
	</span>
	<br />
	
	<span class="flot_droite">
	    <a href="/options/sauvegardes-zcode.html?id=<?php echo $id ?>&xhr=1" id="zform<?php echo $id ?>-autobackups-link">
	        <img src="/img/popup.png" alt="Ouvre une nouvelle fenêtre" />
	        Sauvegardes automatiques de zCode
	    </a>
	    <?php $view['javelin']->initBehavior('squeezebox', array(
    	    'selector' => '#zform'.$id.'-autobackups-link', 
    	    'options' => array('handler' => 'iframe'),
    	)) ?>
	</span>
</div>

<div class="smilies_zform">
	<div id="smilies1_<?php echo $id; ?>">
		<a href="#" onclick="switch_smilies('<?php echo $id; ?>'); return false;">Autres smilies &rarr;</a><br />
		<img src="/bundles/zcocore/img/zcode/smilies/smile.png" class="smiley_cliquable" alt=":)" onclick="balise(':)','','<?php echo $id; ?>');" />
		<img src="/bundles/zcocore/img/zcode/smilies/heureux.png" class="smiley_cliquable" alt=":D" onclick="balise(':D','','<?php echo $id; ?>');" />
		<img src="/bundles/zcocore/img/zcode/smilies/clin.png" class="smiley_cliquable" alt=";)" onclick="balise(';)','','<?php echo $id; ?>');" />
		<img src="/bundles/zcocore/img/zcode/smilies/langue.png" class="smiley_cliquable" alt=":p" onclick="balise(':p','','<?php echo $id; ?>');" />
		<br />
		<img src="/bundles/zcocore/img/zcode/smilies/rire.gif" class="smiley_cliquable" alt=":lol:" onclick="balise(':lol:','','<?php echo $id; ?>');" />
		<img src="/bundles/zcocore/img/zcode/smilies/unsure.gif" class="smiley_cliquable" alt=":euh:" onclick="balise(':euh:','','<?php echo $id; ?>');" />
		<img src="/bundles/zcocore/img/zcode/smilies/triste.png" class="smiley_cliquable" alt=":(" onclick="balise(':(','','<?php echo $id; ?>');" />
		<img src="/bundles/zcocore/img/zcode/smilies/huh.png" class="smiley_cliquable" alt=":o" onclick="balise(':o','','<?php echo $id; ?>');" />
		<br />
		<img src="/bundles/zcocore/img/zcode/smilies/mechant.png" class="smiley_cliquable" alt=":colere2:" onclick="balise(':colere2:','','<?php echo $id; ?>');" />
		<img src="/bundles/zcocore/img/zcode/smilies/blink.gif" class="smiley_cliquable" alt="o_O" onclick="balise('o_O','','<?php echo $id; ?>');" />
		<img src="/bundles/zcocore/img/zcode/smilies/hihi.png" class="smiley_cliquable" alt="^^" onclick="balise('^^','','<?php echo $id; ?>');" />
		<img src="/bundles/zcocore/img/zcode/smilies/siffle.png" class="smiley_cliquable" alt=":-°" onclick="balise(':-°','','<?php echo $id; ?>');" />
	</div>
	<div id="smilies2_<?php echo $id; ?>" style="display: none;">
		<a href="#" onclick="switch_smilies('<?php echo $id; ?>'); return false;">&larr; Autres smilies</a><br />
		<img src="/bundles/zcocore/img/zcode/smilies/pirate.png" class="smiley_cliquable" alt=":pirate:" onclick="balise(':pirate:','','<?php echo $id; ?>');" />&nbsp;&nbsp;&nbsp;
		<img src="/bundles/zcocore/img/zcode/smilies/zorro.png" class="smiley_cliquable" alt=":zorro:" onclick="balise(':zorro:','','<?php echo $id; ?>');" />&nbsp;&nbsp;&nbsp;
		<img src="/bundles/zcocore/img/zcode/smilies/rouge.png" class="smiley_cliquable" alt=":honte:" onclick="balise(':honte:','','<?php echo $id; ?>');" />
		<br />
		<img src="/bundles/zcocore/img/zcode/smilies/soleil.png" class="smiley_cliquable" alt=":soleil:" onclick="balise(':soleil:','','<?php echo $id; ?>');" />&nbsp;&nbsp;&nbsp;
		<img src="/bundles/zcocore/img/zcode/smilies/magicien.png" class="smiley_cliquable" alt=":magicien:" onclick="balise(':magicien:','','<?php echo $id; ?>');" />&nbsp;
		<img src="/bundles/zcocore/img/zcode/smilies/angry.gif" class="smiley_cliquable" alt=":colere:" onclick="balise(':colere:','','<?php echo $id; ?>');" />
		<br />
		<img src="/bundles/zcocore/img/zcode/smilies/diable.png" class="smiley_cliquable" alt=":diable:" onclick="balise(':diable:','','<?php echo $id; ?>');" />&nbsp;&nbsp;
		<img src="/bundles/zcocore/img/zcode/smilies/ange.png" class="smiley_cliquable" alt=":ange:" onclick="balise(':ange:','','<?php echo $id; ?>');" />&nbsp;&nbsp;
		<img src="/bundles/zcocore/img/zcode/smilies/ninja.png" class="smiley_cliquable" alt=":ninja:" onclick="balise(':ninja:','','<?php echo $id; ?>');" />
		<br />
		<img src="/bundles/zcocore/img/zcode/smilies/pinch.png" class="smiley_cliquable" alt="&gt;_&lt;" onclick="balise('&gt;_&lt;','','<?php echo $id; ?>');" />&nbsp;&nbsp;&nbsp;&nbsp;
		<img src="/bundles/zcocore/img/zcode/smilies/pleure.png" class="smiley_cliquable" alt=":'(" onclick="balise(':\'(','','<?php echo $id; ?>');" />&nbsp;&nbsp;&nbsp;&nbsp;
		<img src="/bundles/zcocore/img/zcode/smilies/waw.png" class="smiley_cliquable" alt=":waw:" onclick="balise(':waw:','','<?php echo $id; ?>');" />
	</div>
	<br /><br /><br />

	<a id="zform<?php echo $id ?>-manual-link" href="http://www.siteduzero.com/tutoriel-3-37291-bien-utiliser-le-zcode.html">
		<img src="/img/popup.png" alt="Ouvre une nouvelle fenêtre" />
		Mode d'emploi
	</a><br />
	<?php $view['javelin']->initBehavior('squeezebox', array(
	    'selector' => '#zform'.$id.'-manual-link', 
	    'options' => array('handler' => 'iframe'),
	)) ?>
	
	<a id="zform<?php echo $id ?>-files-link" href="/fichiers/?textarea=<?php echo $id ?>&xhr=1">
		<img src="/img/popup.png" alt="Ouvre une nouvelle fenêtre" />
		Envoi de fichiers
	</a>
	<?php $view['javelin']->initBehavior('squeezebox', array(
	    'selector' => '#zform'.$id.'-files-link', 
	    'options' => array('handler' => 'iframe'),
	)) ?>

	<br /><br />
	<input type="button" name="zform_height_moins" id="zform_height_moins_texte" value="-" onclick="edit_zform_height('<?php echo $id; ?>', 'prev_<?php echo $id; ?>', 'prev_final_texte', -50)" />
	Hauteur
	<input type="button" name="zform_height_plus" id="zform_height_plus_texte" value="+" onclick="edit_zform_height('<?php echo $id; ?>', 'prev_<?php echo $id; ?>', 'prev_final_texte', 50)" />
</div>


<div class="zform">
	<textarea onselect="storeCaret('<?php echo $id; ?>')" onclick="storeCaret('<?php echo $id; ?>');" onkeyup="storeCaret('<?php echo $id; ?>');" name="<?php echo $id; ?>" id="<?php echo $id; ?>" cols="40" rows="15" <?php if(isset($tabindex)){ ?>tabindex="<?php echo $tabindex; ?>"<?php } ?>><?php echo htmlspecialchars($texte); ?></textarea>

	<div class="cadre_previsualisation_finale">
		<p class="bouton_prev_finale">
			<input type="submit" name="xhr" id="lancer_apercu_<?php echo $id; ?>" value="Aperçu final" onclick="full_preview('<?php echo $id; ?>', 'prev_final_<?php echo $id; ?>'); return false;" />
		</p>
		<div id="prev_final_<?php echo $id; ?>" class="code_parse_final">
			<?php
			if($app->getRequest()->attributes->get('_module') == 'zcorrection')
				echo $view['messages']->parseSdz($texte);
			else
				echo $view['messages']->parse($texte);
			?>
		</div>
	</div>

</div>

<br/><br/>
