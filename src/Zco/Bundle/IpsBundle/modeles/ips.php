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

/**
 * Modèle pour la gestion des ips
 *
 * @package zCorrecteurs.fr
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @begin 03/10/2007
 * @last 07/04/2008 vincent1870
 */

function BannirIP($ip, $raison, $raison_admin, $duree)
{
	$ip = ip2long($ip);
	$dbh = Doctrine_Manager::connection()->getDbh();

	if($ip && $ip != -1)
	{
		//Enregistrement en BDD
		$stmt = $dbh->prepare("INSERT INTO zcov2_ips_bannies(ip_ip, ip_raison, ip_raison_admin, ip_date, ip_duree, ip_duree_restante, ip_id_admin) " .
				"VALUES(:ip, :raison, :raison_admin, NOW(), :duree, :duree_restante, :id)");
		$stmt->bindParam(':ip', $ip);
		$stmt->bindParam(':raison', $raison);
		$stmt->bindParam(':raison_admin', $raison_admin);
		$stmt->bindParam(':duree', $duree);
		$stmt->bindParam(':duree_restante', $duree);
		$stmt->bindParam(':id', $_SESSION['id']);
		$stmt->execute();

		//Mise en cache
		$contenu = Container::getService('zco_core.cache')->Get('ips_bannies');
		$contenu[] = $ip;
		Container::getService('zco_core.cache')->Set('ips_bannies', $contenu, 0);

		return true;
	}
	else
	{
		return false;
	}
}

function DebannirIP($id)
{
	//Enregistrement en BDD
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare("UPDATE zcov2_ips_bannies SET ip_fini = 1 WHERE ip_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$stmt->closeCursor();

	//Mise en cache
	Container::getService('zco_core.cache')->Delete('ips_bannies');
}

function SupprimerIP($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Enregistrement en BDD
	$stmt = $dbh->prepare("DELETE FROM zcov2_ips_bannies WHERE ip_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	//Mise en cache
	Container::getService('zco_core.cache')->Delete('ips_bannies');
}

function ListerIPsBannies($fini = null, $ip = null)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if(!is_null($fini))
	{
		$add = 'WHERE ip_fini = '.($fini ? 1 : 0).' ';
	}
	elseif(!is_null($ip))
	{
		$add = 'WHERE ip_ip = '.ip2long($ip).' ';
	}
	else
	{
		$add = '';
	}

	$stmt = $dbh->prepare("SELECT ip_id, ip_ip, ip_duree, ip_duree_restante, ip_raison, ip_raison_admin, utilisateur_id, " .
			"utilisateur_pseudo, ip_fini, ip_date AS ip_date_debut, " .
			"CASE WHEN ip_duree = 0 " .
			"THEN 'Jamais' " .
			"ELSE (DATE(ip_date) + INTERVAL ip_duree DAY) " .
			"END AS ip_date_fin " .
			"FROM zcov2_ips_bannies " .
			"LEFT JOIN zcov2_utilisateurs ON ip_id_admin=utilisateur_id " .
			$add .
			"ORDER BY ip_fini, ip_date DESC");

	$stmt->execute();
	return $stmt->fetchAll();
}

function ListerIPsMembre($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT ip_ip, ip_proxy, ip_date_debut, ip_date_last, ip_localisation " .
			"FROM zcov2_utilisateurs_ips " .
			"WHERE ip_id_utilisateur = :id " .
			"ORDER BY ip_date_last DESC");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetchAll();
}

function AnalyserIP($ip)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$retour = array();

	//Dernière IP
	$stmt = $dbh->prepare("SELECT utilisateur_id, utilisateur_pseudo, utilisateur_ip, utilisateur_id_groupe, groupe_nom,
	groupe_class, utilisateur_forum_messages, utilisateur_date_derniere_visite
	FROM zcov2_utilisateurs
	LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
	WHERE utilisateur_ip = :ip");
	$stmt->bindParam(':ip',  $ip);
	$stmt->execute();
	$retour['last_ip'] = $stmt->fetchAll();
	$stmt->closeCursor();

	//IP de membre (pas que la dernière)
	$stmt = $dbh->prepare("SELECT utilisateur_id, utilisateur_pseudo, utilisateur_ip, utilisateur_id_groupe, ip_ip,
	ip_date_debut, ip_date_last, groupe_nom, groupe_class, utilisateur_forum_messages
	FROM zcov2_utilisateurs_ips
	LEFT JOIN zcov2_utilisateurs ON ip_id_utilisateur = utilisateur_id
	LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
	WHERE ip_ip = :ip");
	$stmt->bindParam(':ip',  $ip);
	$stmt->execute();
	$retour['ips'] = $stmt->fetchAll();
	$stmt->closeCursor();

	//Posts sur le forum
	$stmt = $dbh->prepare("SELECT utilisateur_id, utilisateur_pseudo, utilisateur_ip, utilisateur_id_groupe, message_id,
	message_ip, message_sujet_id, sujet_titre, message_date, groupe_nom, groupe_class, utilisateur_forum_messages, sujet_forum_id
	FROM zcov2_forum_messages
	LEFT JOIN zcov2_utilisateurs ON message_auteur=utilisateur_id
	LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
	LEFT JOIN zcov2_forum_sujets ON message_sujet_id = sujet_id
	WHERE message_ip = :ip");
	$stmt->bindParam(':ip',  $ip);
	$stmt->execute();
	$retour['forum'] = $stmt->fetchAll();
	$stmt->closeCursor();

	//Candidatures
	$retour['recrutement'] = array();
	$resultat = array();
	$stmt = $dbh->prepare("SELECT utilisateur_id, utilisateur_pseudo, utilisateur_ip, utilisateur_id_groupe, candidature_id,
	candidature_date, groupe_class, utilisateur_forum_messages
	FROM zcov2_recrutements_candidatures
	LEFT JOIN zcov2_utilisateurs ON candidature_id_utilisateur = utilisateur_id
	LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id
	WHERE candidature_ip = :ip");
	$stmt->bindParam(':ip',  $ip);
	$stmt->execute();
	$retour['recrutement'] = $stmt->fetchAll();

	return $retour;
}

function Geolocaliser($ip)
{
	//Inclusion de la librairie
	include_once(BASEPATH.'/vendor/geoip/geoipcity.php');

	$ip = long2ip($ip);
	$match = explode('.', $ip);

	//Si l'adresse est spécifique (type localhost)
	if ($match[0] == '127' or $match[0] == '10' or ($match[0] == '172' and $match[1] >= '16' and $match[1] <= '31') or ($match[0] == '192' and $match[1] == '168'))
	{
		return array(false, '-', null);
	}

	//Lancement de la procédure de localisation
	if (!is_file(BASEPATH.'/vendor/geoip/GeoLiteCity.dat')) {
        return ['Inconnu', null, null];
    }
	$gi = geoip_open(BASEPATH.'/vendor/geoip/GeoLiteCity.dat', GEOIP_STANDARD);
	$location = geoip_record_by_addr($gi, $ip);
	geoip_close($gi);

	//En cas d'échec de la localisation
	if (empty($location))
	{
		return array('Inconnu', null, null);
	}

	//Si on a le pays
	if (!empty($location->country_code))
	{
	    $objet = Doctrine_Core::getTable('Pays')->findOneByCode(strtoupper($location->country_code));
		return array($location->country_name, $location, $objet ? $objet['id'] : null);
	}
	else
	{
		return null;
	}
}

function IsBot($ip)
{
	$ip = long2ip($ip);
	$host = gethostbyaddr($ip);

	//Google bot
	if(preg_match('`^66\.249\.[0-9]{1,3}\.[0-9]{1,3}$`', $ip) || strpos('googlebot', $host))
	{
		$bot = 'Googlebot';
	}
	//MSN bot
	elseif(preg_match('`^(207\.68\.146\.[0-9]{1,3})|(65_.54\.188\.[0-9]{1,3})$`', $ip))
	{
		$bot = 'MSNBot';
	}
	//Yahoo! Slurp
	elseif(preg_match('`^(66\.196\.[0-9]{1,3}\.[0-9]{1,3})|(68\.142\.[0-9]{1,3}\.[0-9]{1,3})$`', $ip))
	{
		$bot = 'Yahoo! Slurp';
	}
	//Voila
	elseif(preg_match('`^195\.101\.94\.[0-9]{1,3}$`', $ip))
	{
		$bot = 'VoilaBot';
	}
	else
	{
		$bot = false;
	}

	return $bot;
}

function getDoublons()
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare('SELECT ip_ip, COUNT(ip_ip) AS nombre FROM zcov2_utilisateurs_ips GROUP BY ip_ip HAVING COUNT(ip_ip) > 1 ORDER BY COUNT(ip_ip) DESC');
	$stmt->execute();
	return $stmt->fetchAll();
}