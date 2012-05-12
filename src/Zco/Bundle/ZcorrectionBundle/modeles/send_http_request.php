<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Librairie permettant l'envoi en POST de données à un site, avec récupération du retour.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 * @link http://au.php.net/manual/fr/function.fsockopen.php#85572
 */

/**
 * Post provided content to an http server and optionally
 * convert chunk encoded results.  Returns false on errors,
 * result of post on success.  This example only handles http,
 * not https.
 * @param string $host					Le hostname cible
 * @param string $port					Port
 * @param string $uri					L'URL ciblée.
 * @param string $content				Le contenu envoyé.
 * @return false|string					Le retour du site, ou false en cas d'erreur.
 */
function httpPost($host=null,$port=null,$uri=null,$content=null) {
    if (empty($host))         { return false; }
    if (!is_numeric($port)) { return false; }
    if (empty($uri))        { return false; }
    if (empty($content))    { return false; }
    // generate headers in array.
    $t   = array();
    $t[] = 'POST ' . $uri . ' HTTP/1.1';
    $t[] = 'Content-Type: application/x-www-form-urlencoded';
    $t[] = 'Host: ' . $host;
    $t[] = 'Content-Length: ' . strlen($content);
    $t[] = 'Connection: close';
    $t   = implode("\r\n",$t) . "\r\n\r\n" . $content;
    //
    // Open socket, provide error report vars and timeout of 10
    // seconds.
    //

    $ip = gethostbyname($host);

    $fp  = @fsockopen($ip,$port,$errno,$errstr,10);
    // If we don't have a stream resource, abort.
    if (!(get_resource_type($fp) == 'stream')) { return false; }
    //
    // Send headers and content.
    //
    if (!fwrite($fp,$t)) {
        fclose($fp);
        return false;
        }
    //
    // Read all of response into $rsp and close the socket.
    //
    $rsp = '';
    while(!feof($fp)) { $rsp .= fgets($fp,8192); }
    fclose($fp);
    //
    // Call parseHttpResponse() to return the results.
    //
        return parseHttpResponse($rsp);
}


/**
 * Accepts provided http content, checks for a valid http response,
 * unchunks if needed, returns http content without headers on
 * success, false on any errors.
 */
function parseHttpResponse($content=null) {
    if (empty($content)) { return false; }
    // split into array, headers and content.
    $hunks = explode("\r\n\r\n",trim($content));
    if (!is_array($hunks) or count($hunks) < 2) {
        return false;
        }
    $header  = $hunks[count($hunks) - 2];
    $body    = $hunks[count($hunks) - 1];
    $headers = explode("\n",$header);
    unset($hunks);
    unset($header);
    if (!verifyHttpResponse($headers)) { return false; }
    if (in_array('Transfer-Coding: chunked',$headers)) {
        return trim(unchunkHttpResponse($body));
        } else {
        return trim($body);
        }
}

/**
 * Validate http responses by checking header.
 * @param array|null $headers		Les headers.
 * @return boolean
 */
function verifyHttpResponse($headers=null) {
    if (!is_array($headers) or count($headers) < 1) { return false; }
    switch(trim(strtolower($headers[0]))) {
        case 'http/1.0 100 ok':
        case 'http/1.0 200 ok':
        case 'http/1.1 100 ok':
        case 'http/1.1 200 ok':
            return true;
        break;
        }
    return false;
}

/**
 * Unchunk http content.  Returns unchunked content on success,
 * false on any errors...  Borrows from code posted above by
 * jbr at ya-right dot com.
 */
function unchunkHttpResponse($str=null) {
    if (!is_string($str) or strlen($str) < 1) { return false; }
    $eol = "\r\n";
    $add = strlen($eol);
    $tmp = $str;
    $str = '';
    do {
        $tmp = ltrim($tmp);
        $pos = strpos($tmp, $eol);
        if ($pos === false) { return false; }
        $len = hexdec(substr($tmp,0,$pos));
        if (!is_numeric($len) or $len < 0) { return false; }
        $str .= substr($tmp, ($pos + $add), $len);
        $tmp  = substr($tmp, ($len + $pos + $add));
        $check = trim($tmp);
        } while(!empty($check));
    unset($tmp);
    return $str;
}
?>
