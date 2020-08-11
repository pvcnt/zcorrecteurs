<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\ParserBundle\Feature;

use Highlight\Highlighter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\ParserBundle\ParserEvents;
use Zco\Bundle\ParserBundle\Event\FilterContentEvent;
use Zco\Bundle\ParserBundle\Event\FilterDomEvent;

/**
 * Composant principal du parseur de zCode, voir zCode.xsl pour la présentation.
 *
 * @author    mwsaz <mwsaz@zcorrecteurs.fr>
 * @copyright mwsaz <mwksaz@gmail.com> 2010-2012
 */
class CoreFeature implements EventSubscriberInterface
{
	/**
	 * Protocoles acceptés pour les urls (liens, images).
	 *
	 * @var array
	 */
	private static $protocoles = array(
		'apt', 'ftp', 'http', 'https',
	);

	/**
	 * Liste des balises du zCode (utilisé pour échapper les autres balises,
	 * supposées malicieuses).
	 *
	 * @var array
	 */
	private static $balises = array(
		'barre', 'gras', 'italique', 'souligne',
		'attention', 'erreur', 'information', 'question',
		'titre1', 'titre2',
		'exposant', 'indice', 'acronyme', 'faute',
		'lien',	'email',
		'liste', 'puce',
		'image', 'math',
		'couleur', 'police', 'taille',
		'position', 'flottant',
		'citation', 'secret', 'code', 'minicode', 'touche',
		'tableau', 'legende', 'ligne', 'entete', 'cellule',
	);

	/**
	 * Liste des différents types de codes pour le colorateur syntaxique.
	 * Le type de code est placé en clé et en valeur un tableau contenant
	 * le nom du colorateur à utiliser le nom lisible du langage. Le type
	 * a été entré directement par l'utilisateur.
	 *
	 * @var array
	 */
	private static $langages = array(
		'actionscript' => array('actionscript', 'Actionscript'),
		'apache' => array('apache', 'Apache'),
		'bash' => array('bash', 'Bash'),
		'bat' => array('bat', 'Batch'),
		'bbcode' => array('bbcode', 'BBcode'),
		'befunge' => array('befunge', 'Befunge'),
		'boo' => array('boo', 'Boo'),
		'brainfuck' => array('brainfuck', 'BrainFuck'),
		'c' => array('c', 'C'),
		'c#' => array('csharp', 'C#'),
		'c++' => array('cpp', 'C++'),
		'cfg' => array('cfg', 'cfg'),
		'c++-objdumb' => array('c++-objdumb', 'C++ Objdump'),
		'c-objdump' => array('c-objdump', 'C Objdump'),
		'common-lisp' => array('common-lisp', 'Common Lisp'),
		'console' => array('console', 'Console'),
		'control' => array('control', 'Debian control'),
		'cpp' => array('cpp', 'C++'),
		'cpp-objdump' => array('cpp-objdump', 'C++ Objdump'),
		'csharp' => array('csharp', 'C#'),
		'css' => array('css', 'CSS'),
		'd' => array('d', 'D'),
		'delphi' => array('delphi', 'Delphi'),
		'diff' => array('diff', 'Diff'),
		'django' => array('django', 'Django'),
		'd-objdump' => array('d-objdump', 'D Objdump'),
		'dylan' => array('dylan', 'Dylan'),
		'erb' => array('erb', 'ERB'),
		'erlang' => array('erlang', 'Erlang'),
		'gas' => array('gas', 'Gas'),
		'genshi' => array('genshi', 'Genshi'),
		'genshitext' => array('genshitext', 'Genshi text'),
		'groff' => array('groff', 'Groff'),
		'haskell' => array('haskell', 'Haskell'),
		'html' => array('html', 'HTML'),
		'ini' => array('ini', 'Ini'),
		'io' => array('io', 'Io'),
		'irc' => array('irc', 'IRC'),
		'java' => array('java', 'Java'),
		'javascript' => array('javascript', 'JavaScript'),
		'js' => array('js', 'JavaScript'),
		'kid' => array('kid', '<<<<>>>>'),
		'latex' => array('latex', 'LaTeX'),
		'lhs' => array('lhs', 'Literate Haskell'),
		'llvm' => array('llvm', 'LLVM'),
		'lua' => array('lua', 'Lua'),
		'make' => array('make', 'Make (extension)'),
		'makefile' => array('makefile', 'Makefile'),
		'mako' => array('mako', 'Mako'),
		'minid' => array('minid', 'MiniD'),
		'moocode' => array('moocode', 'MOOCode'),
		'mupad' => array('mupad', 'MuPAD'),
		'myghty' => array('myghty', 'Myghty'),
		'mysql' => array('mysql', 'MySQL'),
		'nasm' => array('nasm', 'Netwide Assembler'),
		'obj-c' => array('objective-c', 'Objective-C'),
		'objc' => array('objective-c', 'Objective-C'),
		'objdump' => array('objdump', 'Objdump'),
		'objective-c' => array('objective-c', 'Objective-C'),
		'objectivec' => array('objective-c', 'Objective-C'),
		'ocaml' => array('ocaml', 'OCaml'),
		'pascal' => array('pascal', 'Pascal'),
		'perl' => array('perl', 'Perl'),
		'php' => array('html+php', 'PHP'),
		'pl' => array('perl', 'Perl'),
		'po' => array('po', 'PO'),
		'pot' => array('pot', 'Gettext'),
		'py' => array('python', 'Python'),
		'pycon' => array('pycon', 'Python Console'),
		'pytb' => array('pytb', 'Python TraceBack'),
		'python' => array('python', 'Python'),
		'rb' => array('ruby', 'Ruby'),
		'rbcon' => array('rbcon', 'Ruby Console'),
		'redcode' => array('redcode', 'Redcode'),
		'rhtml' => array('rhtml', 'rHTML'),
		'rst' => array('rst', 'ReStructured Text'),
		'ruby' => array('ruby', 'Ruby'),
		'scheme' => array('scheme', 'Scheme'),
		'sh' => array('sh', 'Bash'),
		'smarty' => array('smarty', 'Smarty'),
		'sources.list' => array('sources.list', 'Sources.list'),
		'sourceslist' => array('sources.list', 'Sources.list'),
		'sql' => array('sql', 'SQL'),
		'squid' => array('squid', 'SQUID'),
		'tcl' => array('tcl', 'Tcl'),
		'tex' => array('tex', 'TeX'),
		'text' => array('text', 'Texte'),
		'trac-wiki' => array('trac-wiki', 'MoinMoin / Trac Wiki'),
		'vb.net' => array('vb.net', 'VB.NET'),
		'vbnet' => array('vb.net', 'VB.NET'),
		'vim' => array('vim', 'VimL script'),
		'xml' => array('xml', 'XML'),
		'zcode' => array('xml', 'zCode')
	);

	private static $codesParses;
	private static $prefixeAncre;
	private static $ancres;
	private static $dom;

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
    {
        return array(
            ParserEvents::PRE_PROCESS_TEXT  => array('preProcessText', -128),
            ParserEvents::PREPARE_XML       => 'prepareXML',
            ParserEvents::PROCESS_DOM       => 'process',
            ParserEvents::POST_PROCESS_TEXT => 'postProcessText',
        );
    }

    /**
     * Remplace les liens automatiques (non-entourés de balises).
     *
     * @param FilterContentEvent $event
     */
    public function preProcessText(FilterContentEvent $event)
    {
		static $protocoles = array();
		if (!$protocoles)
		{
			foreach (self::$protocoles as $protocole)
			{
				$protocoles[] = preg_quote($protocole, '`');
			}
			$protocoles = implode('|', $protocoles);
		}
		
		$event->setContent(preg_replace(
			'`(\s|^|>)'
			.'((?:'.$protocoles.')://[.0-9a-z/~;:@?&=#%_-]+)'
			.'(\s|$|<)(?![^><]*"[^>]*>)`i',
			'$1<lien>$2</lien>$3',
			$event->getContent()
		));
    }
    
    /**
     * Remplace les sauts de ligne par la balise HTML appropriée.
     * Remplace les codes parsés par leur valeur.
     *
     * @param FilterContentEvent $event
     */
    public function postProcessText(FilterContentEvent $event)
    {
        $text = nl2br(trim($event->getContent()));
		$text = preg_replace(
		    '`(</li>|<ul [a-z]+="[a-zA-Z0-9_]*">)<br />`', 
		    '$1', 
		    $text
		);
        
		if (self::$codesParses) // Balises code
		{
		    $codesParses = self::$codesParses;
			$text = preg_replace_callback(
				'`<zcode-code>([0-9]+)</zcode-code>`',
				function($m) use ($codesParses) {
				    return isset($codesParses[$m[1]]) ? $codesParses[$m[1]] : '';
				}, $text
			);
		}
		
		$event->setContent($text);
    }
    
	/**
	 * Convertit le zCode en HTML grâce à une feuille de style XSLT.
	 *
	 * @param FilterDomEvent $event
	 */
	public function process(FilterDomEvent $event)
	{
		$dom = $event->getDom();
	    self::$dom = $dom;
		self::$codesParses = array(); // Nettoyer les anciens parsages
		self::$prefixeAncre = $event->getOption('core.anchor_prefix', false);
		if (self::$prefixeAncre !== false)
		{
			self::$prefixeAncre = (ctype_digit(self::$prefixeAncre) ? 'zc' : '').self::$prefixeAncre.'-';
		}
		self::$ancres = array();
        
		static $xsl = false;  // Le chargement de la feuille de style
		static $xslt = false; // est ce qui prend le plus de temps...
		if ($xsl === false || $xslt === false)
		{
			$xsl = new \DomDocument;
			$xsl->load(__DIR__.'/zCode.xsl');

			$xslt = new \XSLTProcessor;
			$xslt->importStylesheet($xsl);
			$xslt->registerPhpFunctions();
		}
		
		$html = $xslt->transformToXML($dom);
		
		//On insère le HTML dans un nouvel arbre DOM rien que pour lui.
		$html = trim(substr($html, strlen('<?xml version="1.0" encoding="utf-8"?>')));
		$newDom = new \DomDocument;
		$newDom->loadXML('<zcode>'.$html.'</zcode>');
		
		$event->setDom($newDom);
	}
	
	/**
	 * Récupère les balises du zCode et les repasse en XML. Évite ainsi 
	 * l'insertion d'une balise HTML au milieu du zCode.
	 *
	 * @param FilterContentEvent $event
	 */
	public function prepareXML(FilterContentEvent $event)
	{
	    $texte = htmlspecialchars($event->getContent());
		static $balises = array();
		if(!$balises)
		{
			foreach(self::$balises as &$balise)
				$balises[] = preg_quote($balise, '`');
			$balises = implode('|', $balises);
		}

		$remplacements = 1;
		$balises_c = function($m)
		{
			return '<'.$m[1].str_replace('&quot;', '"', $m[2])
				.'>'.$m[3].'</'.$m[1].'>';
		};

		$pattern = '`&lt;('.$balises.')'
			.'((?:\s+[A-Za-z_-]+=&quot;.*?&quot;)*)'
			.'&gt;(.+?)&lt;/\\1&gt;`s';
		do
		{
			$texte = preg_replace_callback(
				$pattern,
				$balises_c, $texte, -1, $remplacements);
		}
		while($remplacements && preg_match($pattern, $texte));

		$texte = preg_replace_callback(
			'`(<code(?:(?:\s+[A-Za-z_-]+=".*?")*)>)(.+?)(</code>)`s',
            function ($m) {
                return $m[1].htmlspecialchars($m[2]).$m[3];
            }, $texte);

		$event->setContent($texte);
	}

	/**
	 * Vérifie le protocole d'une URL.
	 *
	 * @param  string $lien URL
	 * @return boolean Protocole autorisé ?
	 */
	public static function verifierLien($lien)
	{
		$protocole = substr($lien, 0, strpos($lien, ':'));
		
		return $protocole === '' || in_array($protocole, self::$protocoles);
	}

    /**
     * Corrige les URLs.
     *
     * @param string $url URL brute.
     * @return string
     */
	public static function lien($url)
    {
        if (strpos($url, '/uploads/') === 0) {
            // Historiquement, les chemins commencent généralement par "/uploads/", inquant ainsi qu'on est sur
            // le système de fichiers des "uploads". On retire ce préfixe et utilise le resolver pour transformer
            // ce lien en une URL correcte.
            return \Container::getService('zco.url_resolver')->resolveUrl(substr($url, 9));
        }
        if (strpos($url, 'ftp.') === 0) {
            // Si aucun protocole n'est spécifié et que l'url commence par "ftp.", on suppose que c'est le protocole
            // FTP qui est en jeu.
            return 'ftp://' . $url;
        }
        if (strpos($url, 'www.') === 0) {
            // Si aucun protocole n'est spécifié et que l'url commence par "ftp.", on suppose que c'est le protocole
            // HTTP qui est en jeu.
            return 'http://' . $url;
        }
        return $url;
    }

    /**
     * Crée une ancre automatique pour un contenu donné.
     *
     * @param  string $text Le contenu sur lequel créer une ancre
     * @return string L'identifant de l'ancre
     */
	public static function ancre($text)
	{
		if (self::$prefixeAncre === false)
			return '';

		$t = stripAccents($text);
		$t = preg_replace('`[^A-Za-z0-9]+`', '-', $t);
		$t = trim($t, '-');

		if (!$t)
		{
		    $t = substr(md5($text), 0, 8);
		}

		if (isset(self::$ancres[$t]))
			$t .= ++self::$ancres[$t];
		else
			self::$ancres[$t] = 1;

		return self::$prefixeAncre.$t;
	}

	/**
	 * Colore un code.
	 *
	 * @param DOMElement $code Code à colorer.
	 * @return int ID du code coloré.
	 */
	public static function colorerCode($code)
	{
		// Récupération des attributs
		$langage = $code[0]->getAttribute('type');
		$premiereLigne = (int)$code[0]->getAttribute('debut');
		$surligne = $code[0]->getAttribute('surligne');
		$minicode = $code[0]->nodeName == 'minicode';

		// Puis du code à colorer
		$texte = '';
		foreach($code[0]->childNodes as $enfant)
			$texte .= self::$dom->saveXML($enfant);

		// URLs cliquables
		$texte = str_replace(array('<lien>', '</lien>'), '', $texte);

		$html = function($t) {
			return str_replace(array("&lt;", "&gt;", "&quot;", "&amp;"), array("<", ">", "\"", "&"), $t);
		};
		$texte = $html($html(trim($texte)));

		// Décalage dans la numérotation (commencer à x)
		if($premiereLigne <= 0)
			$premiereLigne = 1;
		$derniereLigne = $premiereLigne + substr_count($texte, "\n");

		// ID du code parsé
		$id = count(self::$codesParses);

		$langage = isset(self::$langages[$langage]) ?
			self::$langages[$langage][0] : false;

		// Le langage "console" est un peu à part, c'est juste un fond noir
		$colorer = $langage && $langage != 'console';
		$reponse = null;
		if($colorer)
		{
			$reponse = self::colorerCode_c($texte, $langage,
				$premiereLigne, $minicode);
		}

		if($colorer && $reponse !== false)
		{
			self::$codesParses[] = $reponse;
		}
		else // Erreur de coloration
		{
			if($minicode)
				$out = htmlspecialchars($texte);
			else
			{
				$out = '<table class="syntaxtable"><tbody><tr><td class="linenos"><pre>';
				if($langage != self::$langages['console'][0])
				{
					for($i = $premiereLigne; $i <= $derniereLigne; $i++)
						$out .= $i."\n";
				}
				$out .= '</pre></td>'
				       .'<td class="code"><div class="syntax"><pre>'
				       .htmlspecialchars($texte)
				      .'</pre></div></td></tr></tbody></table>';
			}
			self::$codesParses[] = $out;
		}

		// Surlignage
		if(strpos($surligne, '-') !== false) // Plage de lignes
		{
			$surligne = explode('-', $surligne);
			if(count($surligne) != 2)
				$surligne = false;
			else
			{
				$surligne[0] = (int)trim($surligne[0]);
				$surligne[1] = $surligne[1] === '' ? // Plages incomplètes e.g. 3-
					$derniereLigne : (int)trim($surligne[1]);

				if($surligne[0] == $surligne[1])
					$surligne[0] = &$surligne[1];
				else if($surligne[0] > $surligne[1]) // Inverser l'ordre
				{
					list($surligne[0], $surligne[1])
						= array($surligne[1], $surligne[0]);
				}

				if($surligne[0] < $premiereLigne)
					$surligne[0] = $premiereLigne;
				if($surligne[1] > $derniereLigne)
					$surligne[1] = $derniereLigne;

				$s = array();
				for($i = $surligne[0]; $i <= $surligne[1]; $i++)
					$s[] = $i;
				$surligne = $s;
			}
		}
		elseif(strpos($surligne, ',') !== false || (int)$surligne) // Lignes précises
		{
			$surligne = explode(',', $surligne);
			foreach($surligne as $k => &$ligne)
			{
				$ligne = (int)$ligne;
				if($ligne < $premiereLigne
				|| $ligne > $derniereLigne)
					unset($surligne[$k]);
			}
			$surligne = array_unique($surligne);
		}
		else
			$surligne = false;

		if(!$minicode && $surligne)
		{
			$texte = self::$codesParses[$id];
			$finNumeros = '</pre></td><td class="code"><div class="syntax"><pre>';

			// Séparation du texte et des balises du tableau
			$debutTableau = substr($texte, 0, strpos($texte, $finNumeros) + strlen($finNumeros));
			$finTableau = substr($texte, strrpos($texte, '</pre>'));

			$texte = substr($texte, strlen($debutTableau), -strlen($finTableau));
			$texte = explode("\n", $texte);

			foreach($surligne as &$ligne)
			{
				$texte[$ligne - $premiereLigne]
					= '<span class="ln-xtra">'
					 .$texte[$ligne - $premiereLigne]
					 .'</span>';
			}
			self::$codesParses[$id] = $debutTableau.implode("\n", $texte).$finTableau;
		}

		return $id;
	}

    /**
     * Résout le chemin complet vers une image.
     *
     * @param string $path
     * @return string
     */
	public static function imageLien($path)
    {
        // Historiquement, les chemins commencent généralement par "uploads/", inquant ainsi qu'on est sur le système
        // de fichiers des "uploads". On retire ce préfixe et laisse le resolver faire son boulot.
        $path = ltrim($path, '/');
        if (strpos($path, 'uploads/') === 0) {
            $path = substr($path, 8);
        }
	    return \Container::getService('zco.url_resolver')->resolveUrl($path);
    }

	/**
	 * Callback pour la coloration d'un code.
	 *
	 * @param  string $code            Code à colorer.
	 * @param  string $langage         Langage.
	 * @param  string $premiereLigne   Nombre à partir duquel commencer la numérotation.
	 * @param  bool   $minicode        Balise minicode ?
	 * @return string Code coloré.
	 */
	private static function colorerCode_c($code, $langage, $premiereLigne, $minicode)
	{
        return (new Highlighter())->highlight($langage, $code)->value;
	}

	/**
	 * Retourne le nom du langage, à afficher à côté de Code :
	 *
	 * @param string $langage Attribut type.
	 * @return string Nom "humain" du langage.
	 */
	public static function nomCode($langage)
	{
		return isset(self::$langages[$langage]) ?
			self::$langages[$langage][1] : 'Autre';
	}

	/**
	 * Retourne l'attribut class associé au langage, pour le span.
	 *
	 * @param string $langage Attribut type.
	 * @return string Class associée.
	 */
	public static function typeCode($langage)
	{
		return isset(self::$langages[$langage]) ?
			' '.$langage : '';
	}

	/**
	 * Renvoie le nom de l'auteur, et un lien vers le message.
	 *
	 * @param int $rid ID du message
	 * @return string sujet:auteur
	 */
	public static function citationRid($rid)
	{
		$dbh = \Doctrine_Manager::connection()->getDbh();

		$stmt = $dbh->prepare('SELECT message_sujet_id, utilisateur_pseudo '
			.'FROM '.\Container::getParameter('database.prefix').'forum_messages '
			.'LEFT JOIN '.\Container::getParameter('database.prefix').'utilisateurs '
			.'ON message_auteur = utilisateur_id '
			.'WHERE message_id = :message');

		$stmt->bindParam('message', $rid);
		$stmt->execute();
		if (!$row = $stmt->fetch(\PDO::FETCH_ASSOC))
		{
			return false;
		}

		return $row['message_sujet_id'].':'.$row['utilisateur_pseudo'];
	}
}
