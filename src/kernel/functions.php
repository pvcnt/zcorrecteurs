<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

use Symfony\Component\HttpFoundation\Response;
use Zco\Component\Templating\TemplatingEvents;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Zco\Component\Templating\Event\FilterVariablesEvent;

/**
 * Réduit le charset pour une URL.
 *
 * @param   string $t Texte.
 * @return  string
 */
function rewrite($t)
{
    //Remplacement des caractères non acceptés
    $t = remove_accents(mb_strtolower($t));
    $t = preg_replace('`[^a-z0-9]+`', '-', $t);
    $t = trim($t, '-');

    //Éviter la confusion avec id2 si 'nom' commence par un chiffre
    if (isset($t[0]) && is_numeric($t[0])) {
        $t = '-' . $t;
    }

    return $t === '' ? 'n-a' : $t;
}

/**
 * Converts all accent characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @param string $string Text that might have accent characters
 * @return string Filtered string with replaced "nice" characters.
 */
//From https://core.trac.wordpress.org/browser/tags/4.3/src/wp-includes/formatting.php
function remove_accents($string)
{
    if (!preg_match('/[\x80-\xff]/', $string)) {
        return $string;
    }

    $chars = array(
        // Decompositions for Latin-1 Supplement
        chr(194) . chr(170) => 'a', chr(194) . chr(186) => 'o',
        chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
        chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
        chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
        chr(195) . chr(134) => 'AE', chr(195) . chr(135) => 'C',
        chr(195) . chr(136) => 'E', chr(195) . chr(137) => 'E',
        chr(195) . chr(138) => 'E', chr(195) . chr(139) => 'E',
        chr(195) . chr(140) => 'I', chr(195) . chr(141) => 'I',
        chr(195) . chr(142) => 'I', chr(195) . chr(143) => 'I',
        chr(195) . chr(144) => 'D', chr(195) . chr(145) => 'N',
        chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
        chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
        chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
        chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
        chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
        chr(195) . chr(158) => 'TH', chr(195) . chr(159) => 's',
        chr(195) . chr(160) => 'a', chr(195) . chr(161) => 'a',
        chr(195) . chr(162) => 'a', chr(195) . chr(163) => 'a',
        chr(195) . chr(164) => 'a', chr(195) . chr(165) => 'a',
        chr(195) . chr(166) => 'ae', chr(195) . chr(167) => 'c',
        chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
        chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
        chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
        chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
        chr(195) . chr(176) => 'd', chr(195) . chr(177) => 'n',
        chr(195) . chr(178) => 'o', chr(195) . chr(179) => 'o',
        chr(195) . chr(180) => 'o', chr(195) . chr(181) => 'o',
        chr(195) . chr(182) => 'o', chr(195) . chr(184) => 'o',
        chr(195) . chr(185) => 'u', chr(195) . chr(186) => 'u',
        chr(195) . chr(187) => 'u', chr(195) . chr(188) => 'u',
        chr(195) . chr(189) => 'y', chr(195) . chr(190) => 'th',
        chr(195) . chr(191) => 'y', chr(195) . chr(152) => 'O',
        // Decompositions for Latin Extended-A
        chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
        chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
        chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
        chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
        chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
        chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
        chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
        chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
        chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
        chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
        chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
        chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
        chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
        chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
        chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
        chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
        chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
        chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
        chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
        chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
        chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
        chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
        chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
        chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
        chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
        chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
        chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
        chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
        chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
        chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
        chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
        chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
        chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
        chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
        chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
        chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
        chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
        chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
        chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
        chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
        chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
        chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
        chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
        chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
        chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
        chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
        chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
        chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
        chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
        chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
        chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
        chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
        chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
        chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
        chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
        chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
        chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
        chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
        chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
        chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
        chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
        chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
        chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
        chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',
        // Decompositions for Latin Extended-B
        chr(200) . chr(152) => 'S', chr(200) . chr(153) => 's',
        chr(200) . chr(154) => 'T', chr(200) . chr(155) => 't',
        // Euro Sign
        chr(226) . chr(130) . chr(172) => 'E',
        // GBP (Pound) Sign
        chr(194) . chr(163) => '',
        // Vowels with diacritic (Vietnamese)
        // unmarked
        chr(198) . chr(160) => 'O', chr(198) . chr(161) => 'o',
        chr(198) . chr(175) => 'U', chr(198) . chr(176) => 'u',
        // grave accent
        chr(225) . chr(186) . chr(166) => 'A', chr(225) . chr(186) . chr(167) => 'a',
        chr(225) . chr(186) . chr(176) => 'A', chr(225) . chr(186) . chr(177) => 'a',
        chr(225) . chr(187) . chr(128) => 'E', chr(225) . chr(187) . chr(129) => 'e',
        chr(225) . chr(187) . chr(146) => 'O', chr(225) . chr(187) . chr(147) => 'o',
        chr(225) . chr(187) . chr(156) => 'O', chr(225) . chr(187) . chr(157) => 'o',
        chr(225) . chr(187) . chr(170) => 'U', chr(225) . chr(187) . chr(171) => 'u',
        chr(225) . chr(187) . chr(178) => 'Y', chr(225) . chr(187) . chr(179) => 'y',
        // hook
        chr(225) . chr(186) . chr(162) => 'A', chr(225) . chr(186) . chr(163) => 'a',
        chr(225) . chr(186) . chr(168) => 'A', chr(225) . chr(186) . chr(169) => 'a',
        chr(225) . chr(186) . chr(178) => 'A', chr(225) . chr(186) . chr(179) => 'a',
        chr(225) . chr(186) . chr(186) => 'E', chr(225) . chr(186) . chr(187) => 'e',
        chr(225) . chr(187) . chr(130) => 'E', chr(225) . chr(187) . chr(131) => 'e',
        chr(225) . chr(187) . chr(136) => 'I', chr(225) . chr(187) . chr(137) => 'i',
        chr(225) . chr(187) . chr(142) => 'O', chr(225) . chr(187) . chr(143) => 'o',
        chr(225) . chr(187) . chr(148) => 'O', chr(225) . chr(187) . chr(149) => 'o',
        chr(225) . chr(187) . chr(158) => 'O', chr(225) . chr(187) . chr(159) => 'o',
        chr(225) . chr(187) . chr(166) => 'U', chr(225) . chr(187) . chr(167) => 'u',
        chr(225) . chr(187) . chr(172) => 'U', chr(225) . chr(187) . chr(173) => 'u',
        chr(225) . chr(187) . chr(182) => 'Y', chr(225) . chr(187) . chr(183) => 'y',
        // tilde
        chr(225) . chr(186) . chr(170) => 'A', chr(225) . chr(186) . chr(171) => 'a',
        chr(225) . chr(186) . chr(180) => 'A', chr(225) . chr(186) . chr(181) => 'a',
        chr(225) . chr(186) . chr(188) => 'E', chr(225) . chr(186) . chr(189) => 'e',
        chr(225) . chr(187) . chr(132) => 'E', chr(225) . chr(187) . chr(133) => 'e',
        chr(225) . chr(187) . chr(150) => 'O', chr(225) . chr(187) . chr(151) => 'o',
        chr(225) . chr(187) . chr(160) => 'O', chr(225) . chr(187) . chr(161) => 'o',
        chr(225) . chr(187) . chr(174) => 'U', chr(225) . chr(187) . chr(175) => 'u',
        chr(225) . chr(187) . chr(184) => 'Y', chr(225) . chr(187) . chr(185) => 'y',
        // acute accent
        chr(225) . chr(186) . chr(164) => 'A', chr(225) . chr(186) . chr(165) => 'a',
        chr(225) . chr(186) . chr(174) => 'A', chr(225) . chr(186) . chr(175) => 'a',
        chr(225) . chr(186) . chr(190) => 'E', chr(225) . chr(186) . chr(191) => 'e',
        chr(225) . chr(187) . chr(144) => 'O', chr(225) . chr(187) . chr(145) => 'o',
        chr(225) . chr(187) . chr(154) => 'O', chr(225) . chr(187) . chr(155) => 'o',
        chr(225) . chr(187) . chr(168) => 'U', chr(225) . chr(187) . chr(169) => 'u',
        // dot below
        chr(225) . chr(186) . chr(160) => 'A', chr(225) . chr(186) . chr(161) => 'a',
        chr(225) . chr(186) . chr(172) => 'A', chr(225) . chr(186) . chr(173) => 'a',
        chr(225) . chr(186) . chr(182) => 'A', chr(225) . chr(186) . chr(183) => 'a',
        chr(225) . chr(186) . chr(184) => 'E', chr(225) . chr(186) . chr(185) => 'e',
        chr(225) . chr(187) . chr(134) => 'E', chr(225) . chr(187) . chr(135) => 'e',
        chr(225) . chr(187) . chr(138) => 'I', chr(225) . chr(187) . chr(139) => 'i',
        chr(225) . chr(187) . chr(140) => 'O', chr(225) . chr(187) . chr(141) => 'o',
        chr(225) . chr(187) . chr(152) => 'O', chr(225) . chr(187) . chr(153) => 'o',
        chr(225) . chr(187) . chr(162) => 'O', chr(225) . chr(187) . chr(163) => 'o',
        chr(225) . chr(187) . chr(164) => 'U', chr(225) . chr(187) . chr(165) => 'u',
        chr(225) . chr(187) . chr(176) => 'U', chr(225) . chr(187) . chr(177) => 'u',
        chr(225) . chr(187) . chr(180) => 'Y', chr(225) . chr(187) . chr(181) => 'y',
        // Vowels with diacritic (Chinese, Hanyu Pinyin)
        chr(201) . chr(145) => 'a',
        // macron
        chr(199) . chr(149) => 'U', chr(199) . chr(150) => 'u',
        // acute accent
        chr(199) . chr(151) => 'U', chr(199) . chr(152) => 'u',
        // caron
        chr(199) . chr(141) => 'A', chr(199) . chr(142) => 'a',
        chr(199) . chr(143) => 'I', chr(199) . chr(144) => 'i',
        chr(199) . chr(145) => 'O', chr(199) . chr(146) => 'o',
        chr(199) . chr(147) => 'U', chr(199) . chr(148) => 'u',
        chr(199) . chr(153) => 'U', chr(199) . chr(154) => 'u',
        // grave accent
        chr(199) . chr(155) => 'U', chr(199) . chr(156) => 'u',
    );

    return strtr($string, $chars);
}

/**
 * Vérification des autorisations
 *
 * @param   string $droit Le droit à vérifier.
 * @param   integer $cat La catégorie où l'ont veut vérifier le droit (null par défaut).
 * @return  bool|integer   Retourne true / false en cas de droit binaire, la valeur numérique sinon.
 */
function verifier($droit, $cat = 0, $groupe = null)
{
    static $liste_droits = array();
    static $cache_droits = array();

    //Si on teste le fait que le visiteur soit connecté
    if ($droit == 'connecte') {
        $result = isset($_SESSION['id']) && $_SESSION['id'] > 0;
        return $result;
    }

    //Si on teste le fait que le visiteur soit anonyme
    if ($droit === 'anonyme') {
        return !verifier('connecte');
    }

    if ($groupe == null && isset($cache_droits[$cat][$droit])) {
        return $cache_droits[$cat][$droit];
    }

    //Si on n'a pas spécifié de groupe, c'est celui en session
    if (is_null($groupe)) {
        if (!isset($_SESSION['groupe'])) {
            return false;
        }
        $groupe = $_SESSION['groupe'];
        $groupes = isset($_SESSION['groupes_secondaires']) ? $_SESSION['groupes_secondaires'] : array();
        array_unshift($groupes, $groupe);
        $ret = false;
        foreach ($groupes as $groupe_id) {
            if (false == $ret) {
                $ret = verifier($droit, $cat, $groupe_id);
            }
        }
        return $ret;
    }

    //On récupère les droits du groupe et les stocke dans une variable statique pour ne pas les perdre
    if (!isset($liste_droits[$groupe])) {
        include_once(BASEPATH . '/src/Zco/Bundle/GroupesBundle/modeles/droits.php');
        $liste_droits[$groupe] = RecupererDroitsGroupe($groupe);
    }
    $droits = $liste_droits[$groupe];

    //On vérifie que le droit existe, sinon refus
    if (!array_key_exists($droit, $droits)) {
        $cache_droits[$cat][$droit] = false;
        return false;
    }

    //Si aucune catégorie n'a été spécifiée
    if ($cat == 0) {
        //Droit numérique ou binaire
        if (is_numeric($droits[$droit])) {
            $cache_droits[$cat][$droit] = $droits[$droit];
            return $droits[$droit];
        }
        //Si on a un array (c'était en fait un droit choisissable par catégories)
        //on retourne true s'il y a au moins un droit à true, false si tout est à false (ou droit non binaire)
        elseif (is_array($droits[$droit])) {
            foreach ($droits[$droit] as $cle => $valeur) {
                if ($valeur === 1) {
                    $cache_droits[$cat][$droit] = true;
                    return true;
                }
            }
            $cache_droits[$cat][$droit] = false;
            return false;
        }
    } //Si on avait spécifié une catégorie
    else {
        //Si on a bien un array
        if (is_array($droits[$droit])) {
            //Si cette catégorie n'est pas dans l'array
            if (!array_key_exists($cat, $droits[$droit])) {
                $cache_droits[$cat][$droit] = false;
                return false;
            } else {
                $cache_droits[$cat][$droit] = $droits[$droit][$cat];
                return $droits[$droit][$cat];
            }
        } //Sinon c'est un droit qui ne se gère pas par catégorie, on retourne sa valeur
        elseif (is_numeric($droits[$droit])) {
            $cache_droits[$cat][$droit] = $droits[$droit];
            return $droits[$droit];
        }
    }
}

function verifier_array($credentials)
{
    if (empty($credentials)) {
        return true;
    }

    //Doubles crochets => condition de type OR entre les droits cités.
    if (is_array($credentials[0])) {
        foreach ($credentials[0] as $auth) {
            if (verifier($auth))
                return true;
        }
        return false;
    } //Sinon tableau simple => condition de type AND entre les droits cités.
    else {
        foreach ($credentials as $auth) {
            if (!verifier($auth))
                return false;
        }
        return true;
    }
}

/**
 * Fonction permettant le listage des pages à la SdZ
 * (Page : Précédent 1 2 3 ... 7 8 9 Suivante).
 *
 * @author winzou, DJ Fox, vincent1870
 * @link http://www.siteduzero.com/forum-83-33940-254991.html#r254991
 * @param int $page Page courante.
 * @param int $nb_page Nombre de pages en tout.
 * @param int $nb_mess Nombre de messages.
 * @param int $nb_mess_par_page Nombre de messages par page.
 * @param string $url L'url, avec un %s pour le numéro de la page.
 * @param int $nb Nombre de pages de chaque côté de la page courante.
 * @param bool $reverse Doit-on inverser les pages ?
 * @return array
 */
function liste_pages($page, $nb_page, $nb_mess, $nb_mess_par_page, $url, $reverse = false, $nb = 3)
{
    // Initialisations
    $list_page = array();
    $_page = $page;
    $page <= 0 && $page = 1;

    // Page précédente
    if ($page > 1 && $_page != -1)
        $list_page[] = '<a href="' . str_replace(array('%s', '%d'), $page - 1, $url) . '">'
            . ($reverse ? 'Suivante' : 'Précédente') . '</a>&nbsp;';

    // Création de l'array
    for ($i = 1; $i <= $nb_page; $i++) {
        if (($i < $nb) || ($i > $nb_page - $nb) || (($i < $page + $nb) && ($i > $page - $nb))) {
            if ($i == $page && $_page != -1)
                $list_page[] = '<span class="UI_pageon">' . $i . '</span>&nbsp;';
            else
                $list_page[] = '<a href="' . str_replace(array('%s', '%d'), $i, $url) . '">' . $i . '</a>&nbsp;';
        } else {
            if ($i >= $nb && $i <= $page - $nb)
                $i = $page - $nb;
            elseif ($i >= $page + $nb && $i <= $nb_page - $nb)
                $i = $nb_page - $nb;
            $parts_url = explode('%s', $url);
            $list_page[] = '<a href="#" onclick="page=prompt(\'Sur quelle page voulez-vous vous rendre ('
                . $nb_page . ' pages) ?\'); if(page) document.location=\'' . $parts_url[0]
                . '\' + page + \'' . (isset($parts_url[1]) ? $parts_url[1] : '')
                . '\'; return false;">…</a>&nbsp';
        }
    }

    // Page suivante
    if ($page < $nb_page && $_page != -1)
        $list_page[] = '<a href="' . str_replace(array('%s', '%d'), $page + 1, $url) . '">'
            . ($reverse ? 'Précédente' : 'Suivante') . '</a>&nbsp;';

    // Si ce qu'on retourne est vide, on ajoute une page
    if (empty($list_page)) {
        if ($_page == -1)
            $list_page[] = '1&nbsp;';
        else
            $list_page[] = '<span class="UI_pageon">1</span>&nbsp;';
    }

    return $reverse ? array_reverse($list_page) : $list_page;
}

//Types de messages
define('MSG_ERROR', 0);
define('MSG_NEUTRAL', 1);
define('MSG_OK', 2);

/**
 * Fonction permettant de rediriger le visiteur avec un message.
 *
 * @param string $message Message à afficher.
 * @param string $url L'url cible.
 * @param integer $type Le type de message (confirmation par défaut).
 * @return Response
 */
function redirect($message = null, $url = '', $type = MSG_OK)
{
    //--- Si on est dans une requête Ajax ---
    if (Container::getService('request')->isXmlHttpRequest()) {
        $type = ($type == MSG_OK) ? 'info' : 'error';
        return new Response(json_encode(array(
            'msg' => $message,
            'type' => $type,
            'url' => $url,
        )));
    } //--- Sinon on redirige de la façon ordinaire ---
    else {
        if (empty($url)) {
            $action = Container::getService('request')->attributes->get('_action');
            $url = str_replace('_', '-', $action) . '.html';
        }

        if ($message !== null) {
            if ($type == MSG_OK || $type == MSG_NEUTRAL) {
                $_SESSION['message'][] = $message;
            } else {
                $_SESSION['erreur'][] = $message;
            }
        }
        
        return new Symfony\Component\HttpFoundation\RedirectResponse($url);
    }
}

/**
 * Récupère la valeur d'une préférence.
 *
 * @param string $nom Le nom de la préférence.
 * @return mixed
 */
function preference($nom)
{
    $id = verifier('connecte') ? $_SESSION['id'] : 0;

    //Si la préférence est déjà en session
    if (isset($_SESSION['prefs'][$nom])) {
        return $_SESSION['prefs'][$nom];
    }

    //Sinon on les récupère toutes et on les met en session.
    if ($preferences = \Doctrine_Core::getTable('UserPreference')->getById($id)) {
        $preferences->apply();
        if (isset($_SESSION['prefs'][$nom])) {
            return $_SESSION['prefs'][$nom];
        }

        $container = \Container::getInstance();
        if ($container->has('logger')) {
            $container->get('logger')->warn(sprintf(
                'La préférence "%s" n\'existe pas.', $nom
            ));
        }
        return false;
    }
}

/**
 * Envoie un mail.
 *
 * @param string $destinataire_adresse L'adresse du destinataire.
 * @param string $destinataire_nom Le nom du destinataire.
 * @param string $objet L'objet du message.
 * @param string $message_html Le message formaté en HTML.
 * @param string $expediteur_nom Le nom de l'expéditeur.
 * @return bool
 */
function send_mail($destinataire_adresse, $destinataire_nom, $objet, $message_html, $expediteur_nom = 'Contact des zCorrecteurs')
{
    if (!empty($destinataire_adresse) AND !empty($message_html)) {
        $fromAddress = \Container::getParameter('mailgun_username');
        $message = \Swift_Message::newInstance()
            ->setSubject($objet)
            ->setFrom(array($fromAddress => $expediteur_nom))
            ->setSender($fromAddress)
            ->setReplyTo($fromAddress)
            ->setTo(array($destinataire_adresse => $destinataire_nom))
            ->setBody($message_html, 'text/html');

        return \Container::getService('mailer')->send($message);
    }

    return false;
}

/**
 * array_sum récursif
 *
 * @author mwsaz
 * @param  array $arr Array de nombres pouvant contenir des arrays
 * @return int
 */
function array_sum_r($array)
{
    $sum = 0;
    foreach ($array as &$v) {
        if (is_array($v))
            $sum += array_sum_r($v);
        else
            $sum += (int)$v;
    }
    return $sum;
}

/**
 * Remplace les longs isset($_POST[x], $_POST[y]...
 *
 * @author mwsaz
 * @param  array $arr Array des clés à vérifier
 * @return bool
 */
function check_post_vars($a)
{
    foreach ((is_array($a) ? $a : func_get_args()) as $arg)
        if (!isset($_POST[$arg]))
            return false;
    return true;
}

function array_trim($vars, $index = null)
{
    if ($index != null) {
        if (!is_array($index))
            $index = array($index);

        $v2 = array();
        foreach ($index as $ind)
            $v2[$ind] = $vars[$ind];
        $vars = $v2;
    }

    foreach ($vars as &$var)
        $var = trim($var);
    return $vars;
}

/**
 * Génération d'un objet réponse à partir d'un nom de template et de variables
 * à y insérer.
 *
 * @param string $template Le nom du template.
 * @param array $vars Variables à remplacer.
 * @param array $headers Options pour personnaliser la réponse.
 * @return Response
 */
function render_to_response($template = array(), array $vars = array(), array $headers = array())
{
    //DÉPRÉCIÉ : le premier paramètre peut-être omis.
    if (is_array($template) && $vars == array()) {
        $vars = $template;
        $bundle = Container::getService('request')->attributes->get('_bundle');
        $action = Container::getService('request')->attributes->get('_action');
        $template = $bundle . '::' . lcfirst(\Util_Inflector::camelize($action)) . '.html.php';
    }

    $dispatcher = \Container::getService('event_dispatcher');
    $event = new FilterVariablesEvent($vars);
    $dispatcher->dispatch(TemplatingEvents::FILTER_VARIABLES, $event);
    $vars = $event->getAll();

    //Register resources.
    $event = new FilterResourcesEvent(
        \Container::getService('zco_core.resource_manager'),
        \Container::getService('zco_core.javelin')
    );
    $dispatcher->dispatch(TemplatingEvents::FILTER_RESOURCES, $event);

    //Template rendering.
    $engine = \Container::getService('templating');

    return new Response($engine->render($template, $vars), 200, $headers);
}

function render_to_string($template = array(), array $vars = array())
{
    //First parameter can be omitted.
    if (is_array($template) && $vars == array()) {
        $vars = $template;
        $bundle = Container::getService('request')->attributes->get('_bundle');
        $action = Container::getService('request')->attributes->get('_action');
        $template = $bundle . '::' . lcfirst(\Util_Inflector::camelize($action)) . '.html.php';
    }

    $engine = \Container::getService('templating');
    return $engine->render($template, $vars);
}

/**
 * Retourne l'équivalent en octets de la sortie de sizeformat
 * @param string $size Le nombre à formater.
 * @return string Le nombre formaté.
 */
function sizeint($size)
{
    $sint = (int)$size;
    if ((string)$sint != (string)$size) // Unite à la fin ?
    {
        $unite = substr($size, strlen($sint));
        if ($unite[0] == 'K')
            $size = $sint * 1024;
        elseif ($unite[0] == 'M')
            $size = $sint * 1024 * 1024;
        elseif ($unite[0] == 'G')
            $size = $sint * 1024 * 1024 * 1024;
    }
    return $size;
}

/**
 * Fonction permettant la correction des « s » (singulier / pluriel)
 * Exemple : 'chev' . pluriel(3, 'aux', 'al') affiche 'chevaux'
 *
 * @author vincent1870, Zopieux
 * @param  integer $nb Le nombre à tester
 * @param  string $alt Le pluriel à afficher
 * @param  string $normal La forme singulière
 * @return array
 */
function pluriel($nb, $alt = 's', $normal = '')
{
    return $nb > 1 ? $alt : $normal;
}

/**
 * Constantes utiles par la suite.
 */
define('DATETIME', 0);
define('DATE', 1);
define('MAJUSCULE', 2);
define('MINUSCULE', 3);

/**
 * Transforme une date en une une date relative (Hier, dans 20min…) ou la
 * formate en tenant compte du décalage horaire de l'utilisateur actuel.
 *
 * @author mwsaz
 * @param string|int $dateheure Timestamp ou date compréhensible par strtotime
 * @param integer $casse MAJUSCULE ou MINUSCULE, selon la casse de la première lettre
 * @param integer $format DATE ou DATETIME, pour afficher ou non l'heure avec la date
 * @return string
 */
function dateformat($dateHeure, $casse = MAJUSCULE, $format = DATETIME)
{
    //Omission ou inversion du second paramètre.
    if ($casse === DATE || $casse === DATETIME) {
        $_format = $format;
        $format = $casse;
        $casse = in_array($_format, array(MINUSCULE, MAJUSCULE)) ? $_format : MAJUSCULE;
    }

    if (!is_numeric($dateHeure)) {
        if (strpos($dateHeure, '0000-00-00') === 0) {
            $dateHeure = 0;
        } else {
            $dateHeure = strtotime($dateHeure);
        }
    }

    $casse = $casse === MAJUSCULE ? 'ucfirst' : 'sprintf';
    $out = '';

    if (!$dateHeure) {
        return $casse('jamais');
    }

    // Gestion du décalage
    static $decalage = false;
    if ($decalage === false) {
        $decalage = preference('time_difference');
        // Les timestamps sont enregistrés en GMT+1 dans la base de données
        $decalage -= 3600;
    }

    // Dates relatives
    $difference = time() - $dateHeure;
    $aujourdhui = mktime(0, 0, 0) - $decalage;

    //La différence en nombre de jours est fort imprécise (pas de prise en
    //compte des années bissextiles et des mois à 31 jours) mais suffit pour
    //ce dont on a besoin : savoir si on est à 0, 1, 2 ou 3 jours de décalage.
    $jours = abs(
        ((int)date('d', $dateHeure) - (int)date('d', $aujourdhui))
        + ((int)date('m', $dateHeure) - (int)date('m', $aujourdhui)) * 30
        + ((int)date('Y', $dateHeure) - (int)date('Y', $aujourdhui)) * 365
    );

    if (0 === $jours) // Même jour
    {
        // ±4h autour de maintenant
        if ($format === DATETIME && abs($difference) < 3600 * 4) {
            $s = abs($difference) % 60;
            $m = (int)(abs($difference) / 60);
            $h = (int)($m / 60);
            $m %= 60;

            $out = '';

            if ($h > 0) $out = $h . ' h ' . ($m < 10 ? '0' : '') . $m;
            elseif ($m > 0) $out = $m . ' min';
            elseif ($s > 0) $out = $s . ' s';

            return $out ? $casse($difference < 0 ? 'dans' : 'il y a') . ' ' . $out
                : $casse('maintenant');
        }

        $out = 'aujourd\'hui';
    } elseif ($jours >= -1 && $jours < 2)
        $out = $difference < 0 ? 'demain' : 'hier';
    elseif ($jours >= -2 && $jours < 3)
        $out = $difference < 0 ? 'après-demain' : 'avant-hier';
    else {
        $out = 'le ' . date('d/m/Y', $dateHeure + $decalage);
    }

    if ($format === DATETIME)
        $out .= (is_numeric(substr($out, -1)) ? '' : ',')
            . ' à ' . date('H \\h i', $dateHeure + $decalage);

    return $casse($out);
}

/**
 * Formate une taille en octets nombre de façon agréable pour l'affichage.
 *
 * @param float $size Le nombre à formater.
 * @return string Le nombre formaté.
 */
function sizeformat($size)
{
    $size = sizeint($size);
    if ($size < 1024) return $size . ' o';
    if (($size /= 1024) < 1024) return round($size, 2) . ' Ko';
    if (($size /= 1024) < 1024) return round($size, 2) . ' Mo';
    if (($size /= 1024) < 1024) return round($size, 2) . ' Go';
    if (($size /= 1024) < 1024) return round($size, 2) . ' To';
    if (($size /= 1024) < 1024) return round($size, 2) . ' Po';
    if (($size /= 1024) < 1024) return round($size, 2) . ' Eo';
    if (($size /= 1024) < 1024) return round($size, 2) . ' Zo';
    return round($size, 2) . ' Yo';
}

/**
 * Génère un fil d'Ariane.
 *
 * @param integer $id L'id de la catégorie de base.
 * @param array $enfants Les éléments additionnels à ajouter.
 */
function fil_ariane($id = null, $enfants = array())
{
    $appendTitle = !($id === null && empty($enfants));

    //Détection de l'id de catégorie de base si besoin
    if (!is_numeric($id) && $id !== null) {
        $enfants = $id;
    }
    if (is_null($id) || !is_numeric($id)) {
        $id = GetIDCategorieCourante();
    }

    $ListerParents = ListerParents($id, true);
    if (empty($ListerParents)) {
        $ListerParents = ListerParents(GetIDCategorie('informations'), false);
    }

    $items = array();
    $url = '';

    //Ajout automatique des parents
    foreach ($ListerParents as $i => $p) {
        if (!empty($p['cat_url']) && ($appendTitle || $i < count($ListerParents) - 1)) {
            if (!preg_match('`\.`', $url))
                $url .= FormateURLCategorie($p['cat_id']);
            else
                $url = FormateURLCategorie($p['cat_id']);
            $items[] = '<a href="' . $url . '">' . htmlspecialchars($p['cat_nom']) . '</a>';
        } else {
            $items[] = htmlspecialchars($p['cat_nom']);
        }
    }

    //Ajout des enfants à la main
    if (!is_array($enfants)) {
        $enfants = array($enfants);
    }
    foreach ($enfants as $cle => $valeur) {
        if (!empty($cle)) {
            $items[] = '<a href="' . $valeur . '">' . $cle . '</a>';
        } else {
            $items[] = $valeur;
        }
    }

    Page::$fil_ariane = $items;
}

function extrait($texte, $taille = 50)
{
    $extrait = wordwrap($texte, $taille);
    $extrait = explode("\n", $extrait);
    if ($extrait[0] != $texte)
        $extrait[0] .= '…';
    return $extrait[0];
}

/**
 * Réalise un diff entre deux chaines de caractères.
 *
 * @param string $old L'ancienne chaine de caractères.
 * @param string $new La nouvelle chaine de caractères.
 * @param bool $new Renvoyer le diff brut ?
 * @return string
 */
function diff($old, $new, $raw = false)
{
    include_once(BASEPATH . '/lib/diff/diff.php');
    include_once(BASEPATH . '/lib/diff/htmlformatter.php');

    $old = explode("\n", $raw ? $old : strip_tags($old));
    $new = explode("\n", $raw ? $new : strip_tags($new));

    $diff = new Diff($old, $new);
    if ($raw)
        $formatter = new UnifiedDiffFormatter();
    else    $formatter = new HTMLDiffFormatter();

    return $formatter->format($diff);
}

/**
 * Affiche un message de confirmation
 *
 * @param string $message Le message à afficher
 */
function afficher_message($message)
{
    echo '<p class="UI_infobox">' . $message . '</p>';
}

/**
 * Affiche un message d'erreur
 *
 * @param string $message Le message à afficher
 */
function afficher_erreur($message)
{
    echo '<p class="UI_errorbox">' . $message . '</p>';
}
