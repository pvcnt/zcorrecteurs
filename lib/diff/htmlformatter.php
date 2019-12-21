<?php
// diff.php
//
// PhpWiki diff output code.
//
// Copyright (C) 2000, 2001 Geoffrey T. Dairiki <dairiki@dairiki.org>
// You may copy this code freely under the conditions of the GPL.
//
// Contributor : Laurent Jouanneau (sept, 2006)
//      this html formater doesn't use HTML lib...
//      adaptation for PHP5

class _HWLDF_WordAccumulator {
    function __construct () {
        $this->_lines = array();
        $this->_line = false;
        $this->_group = false;
        $this->_tag = '~begin';
    }

    function _flushGroup ($new_tag) {
        if ($this->_group !== false) {
            if (!$this->_line)
                $this->_line = '';

            if($this->_tag)
               $this->_line.= '<'.$this->_tag.'>'.$this->_group.'</'.$this->_tag.'>';
            else $this->_line.= $this->_group;
        }
        $this->_group = '';
        $this->_tag = $new_tag;
    }

    function _flushLine ($new_tag) {
        $this->_flushGroup($new_tag);
        if ($this->_line)
            $this->_lines[] = $this->_line;
        $this->_line = '';
    }

    function addWords ($words, $tag = '') {
        if ($tag != $this->_tag)
            $this->_flushGroup($tag);

        foreach ($words as $word) {
            // new-line should only come as first char of word.
            if ($word === null)
                continue;
            if ($word[0] == "\n") {
                $this->_group .= " ";
                $this->_flushLine($tag);
                $word = substr($word, 1);
            }
            assert(!strstr($word, "\n"));
            $this->_group .= htmlspecialchars($word);
        }
    }

    function getLines() {
        $this->_flushLine('~done');
        return $this->_lines;
    }
}

class WordLevelDiff extends MappedDiff
{
    function __construct ($orig_lines, $final_lines) {
        list ($orig_words, $orig_stripped) = $this->_split($orig_lines);
        list ($final_words, $final_stripped) = $this->_split($final_lines);


        parent::__construct($orig_words, $final_words,
                          $orig_stripped, $final_stripped);
    }

    function _split($lines) {
        // FIXME: fix POSIX char class.
        if (!preg_match_all('/ ( [^\S\n]+ | [[:alnum:]]+ | . ) (?: (?!< \n) [^\S\n])? /xs',
                            implode("\n", $lines),
                            $m)) {
            return array(array(''), array(''));
        }
        return array($m[0], $m[1]);
    }

    function orig () {
        $orig = new _HWLDF_WordAccumulator;

        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy')
                $orig->addWords($edit->orig);
            elseif ($edit->orig)
                $orig->addWords($edit->orig, 'del');
        }
        return $orig->getLines();
    }

    function _final () {
        $final = new _HWLDF_WordAccumulator;

        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy')
                $final->addWords($edit->final);
            elseif ($edit->final)
                $final->addWords($edit->final, 'ins');
        }
        return $final->getLines();
    }
}


/**
 * HTML unified diff formatter.
 *
 * This class formats a diff into a CSS-based
 * unified diff format.
 *
 * Within groups of changed lines, diffs are highlit
 * at the character-diff level.
 */
class HtmlUnifiedDiffFormatter extends UnifiedDiffFormatter
{
    public $result;

    function __construct($context_lines = 4) {
        parent::__construct($context_lines);
    }

    function _start_diff() {
        $this->result = '<div class="diff">';
    }
    function _end_diff() {
        return $this->result.'</div>';
    }

    function _start_block($header) {
        $this->result.='<div class="block">'.$header."<br>";
    }

    function _end_block() {
        $this->result.='</div>';
    }

    /*function _lines($lines, $class, $prefix = '&nbsp;', $elem = false) {

        $div = '<div class="difftext">';
        foreach ($lines as $line) {
            if ($elem)
                $line = '<'.$elem.'>'.$line.'</'.$elem.'>';
            $div.="\n".'<div class="'.$class.'"><tt class="prefix">'.$prefix.'</tt>'.$line.'</div>';

        }
        $this->result.=$div."</div>\n";
    }*/

    function _context($lines) {
        $div = '<div class="difftext">';
        foreach ($lines as $line) {
            $div.="\n".'<div class="context"><tt class="prefix">&nbsp;</tt>'.htmlspecialchars($line).'</div>';

        }
        $this->result.=$div."</div>\n";
    }
    function _deleted($lines) {
        $div = '<div class="difftext">';
        foreach ($lines as $line) {
            $div.="\n".'<div class="deleted"><tt class="prefix">-</tt><del>'.htmlspecialchars($line).'</del></div>';

        }
        $this->result.=$div."</div>\n";
    }

    function _added($lines) {
        $div = '<div class="difftext">';
        foreach ($lines as $line) {
            $div.="\n".'<div class="added"><tt class="prefix">+</tt><ins>'.htmlspecialchars($line).'</ins></div>';

        }
        $this->result.=$div."</div>\n";
    }

    function _changed($orig, $final) {
        $diff = new WordLevelDiff($orig, $final);
        $div = '<div class="difftext">';
        foreach ($diff->orig() as $line) {
            $div.="\n".'<div class="original"><tt class="prefix">-</tt>'.$line.'</div>';

        }
        $this->result.=$div."</div>\n";
        $div = '<div class="difftext">';
        foreach ($diff->_final() as $line) {
            $div.="\n".'<div class="final"><tt class="prefix">+</tt>'.$line.'</div>';

        }
        $this->result.=$div."</div>\n";

    }
}

class InlineHTMLDiffFormatter extends DiffFormatter{
	var $mode;
	var $contextIndex;
	var $orig;
	function InlineHTMLDiffFormatter($mode, $orig){
	  //$orig is needed in order to print the whole context ...
	  $this->mode = $mode;
	  $this->contextIndex=1;
	  $this->orig=$orig;
	}
	function _localBlock($lines, $mode){
		   if ($this->mode != $mode) return '';
		   echo '<'.$mode.'>';
		   foreach ($lines as $line)
				   echo str_replace("\n", '</'.$mode.'></p><p><'.$mode.'>',$line).' ';
		   echo '</'.$mode.'>';

	}
	function _added($lines) {
		   $this->_localBlock($lines, 'ins');
	}
   function _deleted($lines) {
       $this->_localBlock($lines, 'del');
   }
   function _block_header($xbeg, $xlen, $ybeg, $ylen) {
       if($this->mode=='ins'){
               $beg=$ybeg;
               $len=$ylen;
       }else{
               $beg=$xbeg;
               $len=$xlen;
       }                                                                                                                                                             
       $untouched=str_replace("\n",'</p><p>', implode(' ',array_slice($this->orig,$this->contextIndex -1, $beg - $this->contextIndex )));
       $this->contextIndex=$beg+$len;
       return $untouched;
   }
   function _changed($orig, $final) {
       $this->_deleted($orig);
       $this->_added($final);
   }
   function format($diff){
       $f = parent::format($diff);
       $untouched=str_replace("\n",'</p><p>',implode(' ',array_slice($this->orig,$this->contextIndex -1, count($this->orig) - $this->contextIndex +1))); //Print last chunk of untouched text
       return $f.$untouched;
       }
}

class HTMLDiffFormatter extends DiffFormatter {
  function HTMLDiffFormatter($context_lines = 4) {
       $this->leading_context_lines = $context_lines;
       $this->trailing_context_lines = $context_lines;
   }
 function _localBlock($lines, $mode){
       echo '<div class="'.$mode.'"><p><'.$mode.'>';
       foreach ($lines as $line)
               echo str_replace("\n", '</'.$mode.'></p><p><'.$mode.'>',$line).' ';
       echo '</'.$mode.'></p></div>';
                                                                                                                                                            
}                                                                                                                                                             
   function _added($lines) {
   	   echo '<tr><td class="statut"><img src="/img/misc/ajouter.png" alt="Ajouté" /></td><td class="ins">';
   	   $this->_localBlock($lines, 'ins');
       echo '</td></tr>';
   }
   function _deleted($lines) {
   	   echo '<tr><td class="statut"><img src="/img/misc/ajouter.png" altl="Ajouté" /></td><td class="del">';
       $this->_localBlock($lines,'del');
       echo '</td></tr>';
   }
   
   function _start_block($header) {
   		if(strpos($header, 'a'))
   			$header = preg_replace('`(.+)a(.+)`', 'Ligne(s) $2 ajoutée(s) après ligne(s) $1', $header);
   		if(strpos($header, 'c'))
   			$header = preg_replace('`(.+)c(.+)`', 'Ligne(s) $1 changée(s)', $header);
   		if(strpos($header, 'd'))
   			$header = preg_replace('`(.+)c(.+)`', 'Ligne(s) $1 supprimée(s)', $header);
        echo '<table class="diff"><thead><tr><th colspan="2">'.$header.'</th></tr></thead><tbody>';
    }
    
    function _end_block() {
    	echo '</tbody></table>';
    }
    
    function _context($lines) {
    	echo '<tr><td class="statut">-</td><td>';
    	ob_start();
        parent::_context($lines);
        $val = ob_get_contents();
        ob_end_clean();
        echo nl2br($val);
        echo '</td></tr>';
    }
   
   function _changed($orig, $final) {
       $orig_string = implode("\n",$orig);
       $orig_array=explode(' ',$orig_string);
       $final_string = implode("\n", $final);
       $final_array=explode(' ',$final_string);
       //stop and store output buffer:
       $val = ob_get_contents();
       ob_end_clean();
       //Make inline diffs
       $diff = new Diff($orig_array, $final_array);
       $delForm = new InlineHTMLDiffFormatter('del', $orig_array);
       $del=$delForm->format($diff);
       $insForm= new InlineHTMLDiffFormatter('ins', $final_array);
       $ins=$insForm->format($diff);
       ob_start(); //restart output buffer
       echo $val.'
       <tr><td class="statut"><img src="/img/misc/delete.png" alt="Supprimé" /></td>' .
       '<td class="del"><p>'.$del.'</p></td></tr>
       <tr><td class="statut"><img src="/img/misc/ajouter.png" alt="Ajouté" /></td>' .
       '<td class="ins"><p>'.$ins.'</p></td></tr>';
   }
}