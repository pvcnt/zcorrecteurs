<div id="ajax_loader" class="bulle" style=" display: none;">
	<img src="/img/ajax-loader.gif" style="border: none;" title="chargement" />
</div>

<div id="mise_en_forme" style="display: none;">
	<span class="boutons">
		<img src="/bundles/zcocore/img/zcode/gras.png" alt="Gras" title="Gras" onclick="balise('&lt;gras&gt;','&lt;/gras&gt;', 'texte');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/italique.png" alt="Italique" title="Italique" onclick="balise('&lt;italique&gt;','&lt;/italique&gt;', 'texte');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/souligne.png" alt="Souligné" title="Souligné" onclick="balise('&lt;souligne&gt;','&lt;/souligne&gt;', 'texte');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/barre.png" alt="Barré" title="Barré" onclick="balise('&lt;barre&gt;','&lt;/barre&gt;', 'texte');" class="bouton_cliquable" />
	</span>

	<span class="boutons">
		<img src="/bundles/zcocore/img/zcode/liste.png" alt="Liste à puces" title="Liste à puces" onclick="add_liste('texte','prev_texte')" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/citation.png" alt="Citation" title="Citation" onclick="add_bal2('citation','nom','texte','prev_texte')" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/image.png" alt="Image" title="Image" onclick="balise('&lt;image&gt;','&lt;/image&gt;', 'texte');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/lien.png" alt="Lien" title="Lien" onclick="add_bal2('lien','url','texte','prev_texte')" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/mail.png" alt="E-mail" title="E-mail" onclick="add_bal2('email','nom','texte','prev_texte')" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/secret.png" alt="Secret" title="Secret" onclick="balise('&lt;secret&gt;','&lt;/secret&gt;', 'texte');" class="bouton_cliquable" />
	</span>

	<span class="boutons">
		<img src="/bundles/zcocore/img/zcode/exposant.png" alt="Exposant" title="Exposant" onclick="balise('&lt;exposant&gt;','&lt;/exposant&gt;', 'texte');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/indice.png" alt="Indice" title="Indice" onclick="balise('&lt;indice&gt;','&lt;/indice&gt;', 'texte');" class="bouton_cliquable" />
	</span>

	<span class="boutons">
		<img src="/bundles/zcocore/img/zcode/info.png" alt="Information" title="Information" onclick="balise('&lt;information&gt;','&lt;/information&gt;', 'texte');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/attention.png" alt="Attention" title="Attention" onclick="balise('&lt;attention&gt;','&lt;/attention&gt;', 'texte');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/erreur.png" alt="Erreur" title="Erreur" onclick="balise('&lt;erreur&gt;','&lt;/erreur&gt;', 'texte');" class="bouton_cliquable" />
		<img src="/bundles/zcocore/img/zcode/question.png" alt="Question" title="Question" onclick="balise('&lt;question&gt;','&lt;/question&gt;', 'texte');" class="bouton_cliquable" />
	</span>

	<input type="button" name="zform_height_moins" id="zform_height_moins_texte" value=" - " onclick="edit_zform_height('texte', 'prev_texte', 'prev_final_texte', -50)" />
	Hauteur
	<input type="button" name="zform_height_plus" id="zform_height_plus_texte" value=" + " onclick="edit_zform_height('texte', 'prev_texte', 'prev_final_texte', 50)" />
	<br />

	<span class="cleaner">
		<select id="code_texte" onchange="add_bal3('code','type','code_texte','texte')">
			<option class="opt_titre" selected="selected">Code</option>
			<optgroup label="Web">
				<option value="html">(x)HTML</option>
				<option value="css">CSS</option>
				<option value="javascript">JavaScript</option>
				<option value="xml">XML</option>
				<option value="php">PHP</option>
				<option value="sql">SQL</option>
				<option value="asp">ASP</option>
				<option value="smarty">Smarty</option>
				<option value="apache">Apache</option>
				<option value="perl">Perl</option>
				<option value="actionscript">ActionScript</option>
			</optgroup>
			<optgroup label="Prog'">
				<option value="c">C</option>
				<option value="cpp">C++</option>
				<option value="csharp">C#</option>
				<option value="d">D</option>
				<option value="java">Java</option>
				<option value="python">Python</option>
				<option value="pascal">Pascal</option>
				<option value="ruby">Ruby</option>
				<option value="delphi">Delphi</option>
				<option value="vb">VB</option>
				<option value="vbnet">VB .Net</option>
				<option value="qbasic">QBasic</option>
				<option value="asm">Asm</option>
				<option value="darkbasic">DarkBasic</option>
				<option value="ocaml">Ocaml</option>
			</optgroup>
			<optgroup label="Autre">
				<option value="ada">Ada</option>
				<option value="mpasm">Mpasm</option>
				<option value="nsis">Nsis</option>
				<option value="visualfoxpro">Visual FoxPro</option>
				<option value="objc">Objc</option>
				<option value="matlab">MatLab</option>
				<option value="diff">Diff</option>
				<option value="oobas">Oobas</option>
				<option value="oracle8">Oracle8</option>
				<option value="vhdl">Chdl</option>
				<option value="caddcl">Caddcl</option>
				<option value="cadlisp">Cadlisp</option>
				<option value="c_mac">C_mac</option>
				<option value="lisp">Lisp</option>
				<option value="lua">Lua</option>
				<option value="bash">Bash</option>
				<option value="console">Console</option>
				<option value="zcode">zcode</option>
				<option value="">( Autre )</option>
			</optgroup>
		</select>

		<select id="minicode_texte" onchange="add_bal3('minicode','type','minicode_texte','texte')">
			<option class="opt_titre" selected="selected">Minicode</option>
			<optgroup label="Web">
				<option value="html">(x)HTML</option>
				<option value="css">CSS</option>
				<option value="javascript">JavaScript</option>
				<option value="xml">XML</option>
				<option value="php">PHP</option>
				<option value="sql">SQL</option>
				<option value="asp">ASP</option>
				<option value="smarty">Smarty</option>
				<option value="apache">Apache</option>
				<option value="perl">Perl</option>
				<option value="actionscript">ActionScript</option>
			</optgroup>
			<optgroup label="Prog'">
				<option value="c">C</option>
				<option value="cpp">C++</option>
				<option value="csharp">C#</option>
				<option value="d">D</option>
				<option value="java">Java</option>
				<option value="python">Python</option>
				<option value="pascal">Pascal</option>
				<option value="ruby">Ruby</option>
				<option value="delphi">Delphi</option>
				<option value="vb">VB</option>
				<option value="vbnet">VB .Net</option>
				<option value="qbasic">QBasic</option>
				<option value="asm">Asm</option>
				<option value="darkbasic">DarkBasic</option>
				<option value="ocaml">Ocaml</option>
			</optgroup>
			<optgroup label="Autre">
				<option value="ada">Ada</option>
				<option value="mpasm">Mpasm</option>
				<option value="nsis">Nsis</option>
				<option value="visualfoxpro">Visual FoxPro</option>
				<option value="objc">Objc</option>
				<option value="matlab">MatLab</option>
				<option value="diff">Diff</option>
				<option value="oobas">Oobas</option>
				<option value="oracle8">Oracle8</option>
				<option value="vhdl">Chdl</option>
				<option value="caddcl">Caddcl</option>
				<option value="cadlisp">Cadlisp</option>
				<option value="c_mac">C_mac</option>
				<option value="lisp">Lisp</option>
				<option value="lua">Lua</option>
				<option value="bash">Bash</option>
				<option value="console">Console</option>
				<option value="zcode">zcode</option>
				<option value="">( Autre )</option>
			</optgroup>
		</select>

		<select id="position_texte" onchange="add_bal3('position','valeur','position_texte','texte')">
			<option class="opt_titre" selected="selected">Position</option>
			<option value="justifie">Justifié</option>
			<option value="gauche">À gauche</option>
			<option value="centre" class="centre">Centré</option>
			<option value="droite" class="droite">À droite</option>
		</select>

		<select id="flottant_texte" onchange="add_bal3('flottant','valeur','flottant_texte','texte')">
			<option class="opt_titre" selected="selected">Flottant</option>
			<option value="gauche">À gauche</option>
			<option value="droite" class="droite">À droite</option>
		</select>

		<select id="taille_texte" onchange="add_bal3('taille','valeur','taille_texte','texte')">
			<option class="opt_titre" selected="selected">Taille</option>
			<option value="ttpetit">Très très petit</option>
			<option value="tpetit">Très petit</option>
			<option value="petit">Petit</option>
			<option value="gros">Gros</option>
			<option value="tgros">Très gros</option>
			<option value="ttgros">Très très gros</option>
		</select>

		<select id="couleur_texte" onchange="add_bal3('couleur','nom','couleur_texte','texte')">
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

		<select id="police_texte" onchange="add_bal3('police','nom','police_texte','texte')">
			<option class="opt_titre" selected="selected">Police</option>
			<option value="arial" class="arial">arial</option>
			<option value="times" class="times">times</option>
			<option value="courier" class="courier">courier</option>
			<option value="impact" class="impact">impact</option>
			<option value="geneva" class="geneva">geneva</option>
			<option value="optima" class="optima">optima</option>
		</select>

		<select id="semantique_texte" onchange="balise('<titre'+this.value+'>','</titre'+this.value+'>', 'texte'); document.getElementById('semantique_texte').options[0].selected = true;">
			<option class="opt_titre" selected="selected">Sémantique</option>
			<option value="1">Titre 1</option>
			<option value="2">Titre 2</option>
		</select>
	</span>
</div>
