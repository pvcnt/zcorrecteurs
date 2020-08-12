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

/**
 * Modifie le profil d'un membre.
 *
 * @param integer $id					L'id de l'utilisateur.
 * @param array $params					Les champs à modifier (champ => nouvelle_valeur).
 */
function ModifierUtilisateur($id, $params)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$set = array();
	$bind = array();

	foreach($params as $cle => $valeur)
	{
		//Cas particulier de la date de naissance
		if($cle == 'date_naissance')
		{
			$set[] = 'utilisateur_date_naissance = STR_TO_DATE(:date_naissance, \'%d/%m/%Y\')';
			$bind['date_naissance'] = $valeur;
		}
		else
		{
			if($valeur === 'NOW()')
			{
				$set[] = 'utilisateur_'.$cle.' = NOW()';
			}
			else
			{
				$set[] = 'utilisateur_'.$cle.' = :'.$cle;

				if(is_bool($valeur))
					$valeur = $valeur ? 1 : 0;
				$bind[$cle] = $valeur;
			}
		}
	}

	//Exécution de la requête
	$stmt = $dbh->prepare("UPDATE zcov2_utilisateurs " .
			"SET ".implode(', ', $set)." " .
			"WHERE utilisateur_id = :id");
	$stmt->bindParam(':id', $id);
	foreach($bind as $cle => &$valeur)
		$stmt->bindParam(':'.$cle, $valeur);
	$stmt->execute();
}

/**
 * Envoie un MP automatique (envoyé par le bot zGardien).
 *
 * @param  string $titre Titre du MP
 * @param  string $SousTitre Sous-titre du MP
 * @param  string|array $participants Pseudo du destinataire ou tableau des pseudos des participants
 * @param  string $message Le message formaté en zCode
 * @return integer Identifiant du message créé
 */
function AjouterMPAuto($titre, $SousTitre, $participants, $message)
{
	if(!is_array($participants))
	{
		$participants = array($participants);
	}
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On crée le nouveau MP
	$stmt = $dbh->prepare("INSERT INTO zcov2_mp_mp (mp_titre, mp_sous_titre,
	mp_date, mp_ferme)
	VALUES (:titre, :sous_titre, NOW(), :ferme)");
	$stmt->bindParam(':titre', $titre);
	$stmt->bindParam(':sous_titre', $SousTitre);
	$stmt->bindValue(':ferme', 1);
	$stmt->execute();

	//On récupère l'id du MP nouvellement créé.
	$NouveauMPID = $dbh->lastInsertId();
	$stmt->closeCursor();

	//On crée le message
	$stmt = $dbh->prepare("INSERT INTO zcov2_mp_messages (mp_message_mp_id,
	mp_message_auteur_id, mp_message_date, mp_message_texte, mp_message_ip)
	VALUES (:NouveauMPID, :auteur, NOW(), :texte, :ip)");
	$stmt->bindParam(':NouveauMPID', $NouveauMPID);
	$stmt->bindValue(':auteur', ID_COMPTE_AUTO);
	$stmt->bindParam(':texte', $message);
	$stmt->bindValue(':ip', ip2long(\Container::getService('request')->getClientIp(true)));
	$stmt->execute();

	//On récupère l'id de du message nouvellement créé.
	$NouveauMessageID = $dbh->lastInsertId();
	$stmt->closeCursor();

	//Grâce au numéro du message récupéré, on peut updater la table des MP pour indiquer que ce message est le premier et le dernier du MP.
	$stmt = $dbh->prepare("UPDATE zcov2_mp_mp
	SET mp_premier_message_id = :NouveauMessageID, mp_dernier_message_id = :NouveauMessageID
	WHERE mp_id = :NouveauMPID");
	$stmt->bindParam(':NouveauMessageID', $NouveauMessageID);
	$stmt->bindParam(':NouveauMPID', $NouveauMPID);
	$stmt->execute();
	$stmt->closeCursor();

	//Création des participants

	//On va d'abord préparer la requête
	$stmt = $dbh->prepare("INSERT INTO zcov2_mp_participants (mp_participant_mp_id, mp_participant_id, mp_participant_statut, mp_participant_dernier_message_lu)
	VALUES (:mp_id, :participant_id, :statut, :dernier_msg_lu)");
	$stmt->bindParam(':mp_id', $NouveauMPID); //Ce paramètre ne changera pour aucun des participants : on ne le définit qu'une fois.

	//On ajoute déjà le créateur du MP comme participant avec le statut de MP_STATUT_SUPPRIME
	$stmt->bindValue(':participant_id', ID_COMPTE_AUTO);
	$stmt->bindValue(':statut', MP_STATUT_SUPPRIME);
	$stmt->bindParam(':dernier_msg_lu', $NouveauMessageID);
	$stmt->execute();

	//Puis, pour chaque participant, on va l'ajouter en BDD et on vide son cache.
	$stmt->bindValue(':dernier_msg_lu', 0); //Le MP sera non-lu pour tous les autres participants
	foreach($participants as &$valeur)
	{
		$stmt->bindParam(':participant_id', $valeur);
		$stmt->bindValue(':statut', MP_STATUT_NORMAL);
		$stmt->execute();

		Container::getService('zco_core.cache')->save('MPnonLu'.$valeur, true, strtotime('+1 hour'));
	}
	$stmt->closeCursor();

	return $NouveauMPID;
}

/**
 * Vérifie l'existence d'un membre à partir de son pseudo ou de son id.
 *
 * @param  string|integer $search Identifiant du membre ou pseudo
 * @return boolean
 */
function ChercherExistenceUtilisateur($search)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if (is_numeric($search))
	{
		$stmt = $dbh->prepare("SELECT COUNT(*) AS nb
		FROM zcov2_utilisateurs
		WHERE utilisateur_id = :id");
		$stmt->bindParam(':id',  $search);
		$stmt->execute();
		
		return $stmt->fetchColumn() > 0;
	}
	else
	{
		$stmt = $dbh->prepare("SELECT COUNT(*) AS nb
		FROM zcov2_utilisateurs
		WHERE utilisateur_pseudo = :pseudo");
		$stmt->bindParam(':pseudo',  $search);
		$stmt->execute();
		
		return $stmt->fetchColumn() > 0;
	}
}

/**
 * Récupère les informations sur un membre à partir de son pseudo ou de son id.
 *
 * @param  string|integer $search Identifiant du membre ou pseudo
 * @return array
 */
function InfosUtilisateur($search)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if (is_numeric($search))
	{
		$stmt = $dbh->prepare("SELECT *, CASE
		WHEN utilisateur_date_naissance IS NULL THEN 0
		ELSE FLOOR(DATEDIFF(NOW(), utilisateur_date_naissance) / 365)
		END AS age
		FROM zcov2_utilisateurs
		LEFT JOIN zcov2_groupes ON utilisateur_id_groupe=groupe_id
		LEFT JOIN zcov2_utilisateurs_preferences ON preference_id_utilisateur = utilisateur_id
		WHERE utilisateur_id = :id");
		$stmt->bindParam(':id',  $search);
		$stmt->execute();
		
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	else
	{
		$stmt = $dbh->prepare("SELECT *, CASE
		WHEN utilisateur_date_naissance IS NULL THEN 0
		ELSE DATEDIFF(NOW(), utilisateur_date_naissance) / 365
		END AS age
		FROM zcov2_utilisateurs
		LEFT JOIN zcov2_groupes ON utilisateur_id_groupe=groupe_id
		LEFT JOIN zcov2_utilisateurs_preferences ON preference_id_utilisateur = utilisateur_id
		WHERE utilisateur_pseudo = :pseudo");
		$stmt->bindParam(':pseudo',  $search);
		$stmt->execute();
		
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
}

//Cherche les membres ayant un mail spécifié
function ChercherAdresseMail($mail, $type = 'strict')
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$mail = str_replace('%', '\%', $mail);

	if($type == 'debut')
	{
		$where = 'LIKE';
		$mail = $mail.'%';
	}
	elseif($type == 'fin')
	{
		$where = 'LIKE';
		$mail = '%'.$mail;
	}
	elseif($type == 'contenu')
	{
		$where = 'LIKE';
		$mail = '%'.$mail.'%';
	}
	else
		$where = '=';

	$stmt = $dbh->prepare("SELECT utilisateur_id, utilisateur_pseudo, utilisateur_email, utilisateur_date_inscription, utilisateur_ip, " .
			"groupe_nom, groupe_class " .
			"FROM zcov2_utilisateurs " .
			"LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id " .
			"WHERE utilisateur_email ".$where." :mail " .
			"ORDER BY utilisateur_pseudo ASC");
			$stmt->bindParam(':mail', $mail);

	$stmt->execute();

	return $stmt->fetchAll();
}

/**
 * Vérifie si une adresse mail est valide (si la forme est correcte et qu'elle n'est pas déjà utilisée).
 * @param string $mail						L'adresse à tester.
 * @return bool
 */
function VerifierValiditeMail($mail)
{
	if (preg_match('`^[a-z0-9+._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$`', $mail))
	{
		$search = ChercherAdresseMail($mail);
		if (empty($search))
		{
			return !Doctrine_Core::getTable('MailBanni')->VerifierBannissement($mail);
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

/**
 * Liste les utilisateurs ayant un certain droit.
 *
 * @param  string $droit	Nom du droit
 * @return array
 */
function ListerUtilisateursDroit($droit)
{
	// On obtient des performances pourries en faisant une jointure sur le nom du droit
	$groupes = array();
	foreach(ListerGroupes() as $grp)
	{
		$droits = RecupererDroitsGroupe($grp['groupe_id']);
		if(isset($droits[$droit]))
			$groupes[] = $grp['groupe_id'];
	}

	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare('SELECT utilisateur_id, utilisateur_pseudo, groupe_class, groupe_nom '
		.'FROM zcov2_utilisateurs '
		.'LEFT JOIN zcov2_groupes ON utilisateur_id_groupe = groupe_id '
		.'WHERE utilisateur_id_groupe IN('.implode(',', $groupes).')');
	$stmt->execute();
	return $stmt->fetchAll();
}

function getUtilisateurID($pseudo)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare('SELECT utilisateur_id '
		.'FROM zcov2_utilisateurs '
		.'WHERE utilisateur_pseudo = ?');
	$stmt->execute(array($pseudo));
	return $stmt->fetchColumn();
}

/**
 * Liste tous les utilisateurs d'un groupe donné.
 *
 * @param integer $groupe				L'id du groupe.
 * @return array
 */
function ListerUtilisateursGroupe($groupe)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT utilisateur_id, utilisateur_pseudo, utilisateur_email
	FROM zcov2_utilisateurs
	WHERE utilisateur_id_groupe = :groupe");
	$stmt->bindParam(':groupe', $groupe);

	$stmt->execute();

	$retour = $stmt->fetchAll();
	$stmt->closeCursor();
	return $retour;
}

/**
 * Liste tous les développeurs.
 *
 * @return array
 */
function ListerDeveloppeurs()
{
	// On obtient de mauvaises performances en faisant une jointure sur le nom du droit
	$groupes = array();
	foreach(ListerGroupes() as $grp)
	{
		$droits = RecupererDroitsGroupe($grp['groupe_id']);
		if(isset($droits['depot_commit']))
			$groupes[] = $grp['groupe_id'];
	}

	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare('SELECT utilisateur_id, utilisateur_pseudo '
		.'FROM zcov2_utilisateurs '
		.'WHERE utilisateur_id_groupe IN('.implode(',', $groupes).')');
	$stmt->execute();
	$retour = $stmt->fetchAll();
	$stmt->closeCursor();

	$devs = array();
	foreach($retour as &$dev)
		$devs[$dev['utilisateur_pseudo']] = (int)$dev['utilisateur_id'];
	return $devs;
}

function Geolocaliser($ip)
{
    //Inclusion de la librairie
    include_once(BASEPATH.'/lib/geoip/geoipcity.php');

    $ip = long2ip($ip);
    $match = explode('.', $ip);

    //Si l'adresse est spécifique (type localhost)
    if ($match[0] == '127' or $match[0] == '10' or ($match[0] == '172' and $match[1] >= '16' and $match[1] <= '31') or ($match[0] == '192' and $match[1] == '168'))
    {
        return array(false, '-', null);
    }

    //Lancement de la procédure de localisation
    if (!is_file(BASEPATH.'/lib/geoip/GeoLiteCity.dat')) {
        return ['Inconnu', null, null];
    }
    $gi = geoip_open(BASEPATH.'/lib/geoip/GeoLiteCity.dat', GEOIP_STANDARD);
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
