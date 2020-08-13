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

function TicketsConstruireWhere($params)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if(array_key_exists('etat', $params))
	{
		if(!is_array($params['etat']))
			$params['etat'] = array($params['etat']);

		if(is_string($params['etat'][0]) && $params['etat'][0] == 'not')
		{
			unset($params['etat'][0]);
			$params['etat'] = array_map('intval', $params['etat']);
			$where[] = 'version_etat NOT IN('.implode(',', $params['etat']).')';
		}
		else
		{
			$params['etat'] = array_map('intval', $params['etat']);
			$where[] = 'version_etat IN('.implode(',', $params['etat']).')';
		}
	}
	if(array_key_exists('priorite', $params))
	{
		if(!is_array($params['priorite']))
			$params['priorite'] = array($params['priorite']);
		$params['priorite'] = array_map('intval', $params['priorite']);
		$where[] = 'version_priorite IN('.implode(',', $params['priorite']).')';
	}
	if(array_key_exists('categorie', $params))
	{
		if(!is_array($params['categorie']))
			$params['categorie'] = array($params['categorie']);
		$params['categorie'] = array_map('intval', $params['categorie']);
		$where[] = 'version_id_categorie_concernee IN('.implode(',', $params['categorie']).')';
	}
	if(array_key_exists('admin', $params))
	{
		if($params['admin'] == false)
			$where[] = 'version_id_admin IS NULL';
		else
			$where[] = 'version_id_admin = '.(int)$params['admin'];
	}
	if(array_key_exists('prive', $params) && $params['prive'] == false)
		$where[] = 'ticket_prive = 0';
	if(array_key_exists('titre', $params) && !empty($params['titre']))
	{
		$w = 'ticket_titre LIKE '.$dbh->quote('%'.$params['titre'].'%');
		if(array_key_exists('description', $params) && !empty($params['description']))
			$w .= ' OR ticket_description LIKE '.$dbh->quote('%'.$params['description'].'%');
		$where[] = $w;
	}
	if(array_key_exists('lu', $params))
	{
		if($params['lu'] == true)
			$where[] = 'lunonlu_id_version = ticket_id_version_courante';
		else
			$where[] = '(lunonlu_id_version <> ticket_id_version_courante OR lunonlu_id_version IS NULL)';
	}
	if (isset($params['type']))
	{
		$where[] = 'ticket_type = \''.$params['type'].'\'';
	}

	return $where;
}

function ListerTickets($page = null, $params = array(), $orderby = null)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$where = array();

	//Création de la clause where
	$where = TicketsConstruireWhere($params);

	//Création de l'order by
	if(!is_null($orderby))
	{
		if(!is_array($orderby))
			$orderby = array($orderby);
		$order = array();
		foreach($orderby as $cle => $valeur)
		{
			if($valeur == 'etat')
				$order[] = 'version_etat ASC';
			elseif($valeur == 'priorite')
				$order[] = 'version_priorite DESC';
			elseif($valeur == 'edition')
				$order[] = 'version_date DESC';
			elseif($valeur == 'recent')
				$order[] = 'ticket_date DESC';
			elseif($valeur == 'ancien')
				$order[] = 'ticket_date ASC';
		}
		$order = implode(',', $order);
	}
	else
		$order = 'version_priorite DESC, version_date DESC';


	$stmt = $dbh->prepare("SELECT ticket_id, ticket_date, version_etat, ticket_prive, " .
			"ticket_id_version_courante, ticket_critique, " .
			"u1.utilisateur_id AS id_demandeur, u1.utilisateur_pseudo AS pseudo_demandeur, " .
			"u2.utilisateur_id AS id_admin, u2.utilisateur_pseudo AS pseudo_admin, g2.groupe_class AS class_admin," .
			"version_date, ticket_titre, version_etat, version_priorite, lunonlu_id_version, " .
			"cat_id, cat_nom " .
			"FROM zcov2_tracker_tickets " .
			"LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id " .
			"LEFT JOIN zcov2_utilisateurs u1 ON ticket_id_utilisateur = u1.utilisateur_id " .
			"LEFT JOIN zcov2_utilisateurs u2 ON version_id_admin = u2.utilisateur_id " .
			"LEFT JOIN zcov2_groupes g2 ON u2.utilisateur_id_groupe = g2.groupe_id " .
			"LEFT JOIN zcov2_categories ON version_id_categorie_concernee = cat_id " .
			"LEFT JOIN zcov2_tracker_tickets_flags ON ticket_id = lunonlu_id_ticket AND lunonlu_id_utilisateur = :id_u " .
			(!empty($where) ? 'WHERE '.implode(' AND ', $where) : '')." " .
			"ORDER BY ".$order." " .
			(!is_null($page) ? "LIMIT ".(($page-1)*30).", 30" : ''));
	$id_u = verifier('connecte') ? $_SESSION['id'] : 0;
	$stmt->bindParam(':id_u', $id_u);

	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function CompterTickets($params = array())
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$where = array();

	//Création de la clause where
	$where = TicketsConstruireWhere($params);

	$stmt = $dbh->prepare("SELECT COUNT(*) " .
			"FROM zcov2_tracker_tickets " .
			"LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id " .
			"LEFT JOIN zcov2_tracker_tickets_flags ON ticket_id = lunonlu_id_ticket AND lunonlu_id_utilisateur = :id_u " .
			(!empty($where) ? 'WHERE '.implode(' AND ', $where) : '')." ");
	$id_u = verifier('connecte') ? $_SESSION['id'] : 0;
	$stmt->bindParam(':id_u', $id_u);

	$stmt->execute();

	return $stmt->fetchColumn();
}

function Lister5DerniersTickets($prive, $type = 'bug')
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($prive == false)
		$add = ' AND ticket_prive  = 0 ';

	$stmt = $dbh->prepare("SELECT ticket_id, ticket_date, version_etat, " .
			"version_date, ticket_titre, version_priorite, utilisateur_id, utilisateur_pseudo, groupe_class " .
			"FROM zcov2_tracker_tickets " .
			"LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id " .
			"LEFT JOIN zcov2_utilisateurs ON ticket_id_utilisateur = utilisateur_id " .
			"LEFT JOIN zcov2_groupes ON groupe_id = utilisateur_id_groupe " .
			"WHERE ticket_type = :type".(!empty($add) ? $add : '')." " .
			"ORDER BY ticket_date DESC " .
			"LIMIT 0,5");
	$stmt->bindParam(':type', $type);
	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function Lister5DerniersTicketsModifies($prive, $type = 'bug')
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($prive == false)
		$add = 'AND ticket_prive  = 0';

	$stmt = $dbh->prepare("SELECT ticket_id, ticket_date, version_etat, " .
			"version_date, ticket_titre, version_priorite, utilisateur_id, utilisateur_pseudo, groupe_class " .
			"FROM zcov2_tracker_tickets " .
			"LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id " .
			"LEFT JOIN zcov2_utilisateurs ON version_id_utilisateur = utilisateur_id " .
			"LEFT JOIN zcov2_groupes ON groupe_id = utilisateur_id_groupe " .
			"WHERE ticket_type = :type AND ticket_id_version_courante <> ticket_id_version_first ".(!empty($add) ? $add : '')." " .
			"ORDER BY version_date DESC " .
			"LIMIT 0,5");
	$stmt->bindParam(':type', $type);
	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function CompterTicketsEtat($prive, $type = 'bug')
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT version_etat, version_id_admin, ticket_id_doublon " .
			"FROM zcov2_tracker_tickets " .
			"LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id " .
			"WHERE ticket_type = :type".(!$prive ? ' AND ticket_prive  = 0 ' : ''));
	$stmt->bindParam(':type', $type);

	$stmt->execute();

	$donnees = $stmt->fetchAll();
	$retour = array('all' => count($donnees), 'new' => 0, 'open' => 0, 'solved' => 0);

	foreach($donnees as $t)
	{
		if(is_null($t['version_id_admin']) && in_array($t['version_etat'], array(1)) && is_null($t['ticket_id_doublon']))
			$retour['new']++;
		if(!in_array($t['version_etat'], array(4, 5, 7, 8)) && is_null($t['ticket_id_doublon']))
			$retour['open']++;
		if(in_array($t['version_etat'], array(4, 5, 7, 8)))
			$retour['solved']++;
	}

	return $retour;
}

function InfosTicket($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT ticket_id, ticket_date, ticket_id_doublon, version_etat, " .
			"ticket_description, ticket_prive, ticket_url, version_date, ticket_titre, " .
			"version_priorite, ticket_critique, ticket_type, ticket_user_agent, " .
			"ticket_id_version_courante, ticket_id_version_first, " .
			"cat_id, cat_nom, u1.utilisateur_avatar AS avatar_demandeur, lunonlu_suivi, " .
			"u1.utilisateur_id AS id_demandeur, u1.utilisateur_pseudo AS pseudo_demandeur, " .
			"g1.groupe_class AS groupe_class_demandeur, u2.utilisateur_id AS id_admin, " .
			"u2.utilisateur_pseudo AS pseudo_admin, g2.groupe_class AS groupe_class_admin " .
			"FROM zcov2_tracker_tickets " .
			"LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id " .
			"LEFT JOIN zcov2_utilisateurs u1 ON ticket_id_utilisateur = u1.utilisateur_id " .
			"LEFT JOIN zcov2_utilisateurs u2 ON version_id_admin = u2.utilisateur_id " .
			"LEFT JOIN zcov2_groupes g1 ON g1.groupe_id = u1.utilisateur_id_groupe " .
			"LEFT JOIN zcov2_groupes g2 ON g2.groupe_id = u2.utilisateur_id_groupe " .
			"LEFT JOIN zcov2_categories ON version_id_categorie_concernee = cat_id " .
			"LEFT JOIN zcov2_tracker_tickets_flags ON lunonlu_id_ticket = ticket_id AND lunonlu_id_utilisateur = :id_u " .
			"WHERE ticket_id = :id");
	$id_u = verifier('connecte') ? $_SESSION['id'] : 0;
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':id_u', $id_u);
	$stmt->execute();

	$infos = $stmt->fetch(PDO::FETCH_ASSOC);
	if($infos['ticket_prive'] && !verifier('tracker_voir_prives'))
		$infos = array();

	return $infos;
}

function SupprimerTicket($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("DELETE FROM zcov2_tracker_tickets WHERE ticket_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();


	$stmt = $dbh->prepare("DELETE FROM zcov2_tracker_tickets_versions WHERE version_id_ticket = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	$stmt = $dbh->prepare("DELETE FROM zcov2_tracker_reponses WHERE reponse_id_ticket = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	Container::getService('zco_core.cache')->Delete('liste_tickets');
	\Container::getService('zco_admin.manager')->get('demandes', true);
}

function MarquerTicketCommeLu($id_t, $id_v)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("INSERT INTO zcov2_tracker_tickets_flags(" .
			"lunonlu_id_utilisateur, lunonlu_id_ticket, lunonlu_id_version, " .
			"lunonlu_suivi, lunonlu_suivi_envoye) " .
			"VALUES(:id_u, :id_t, :id_v, 0, 0) " .
			"ON DUPLICATE KEY UPDATE lunonlu_id_version = :id_v, lunonlu_suivi_envoye = 0");
	$stmt->bindParam(':id_u', $_SESSION['id']);
	$stmt->bindParam(':id_v', $id_v);
	$stmt->bindParam(':id_t', $id_t);
	$stmt->execute();
}

function ChangerSuiviTicket($id_u, $id_t, $etat)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("INSERT INTO zcov2_tracker_tickets_flags(" .
			"lunonlu_id_ticket, lunonlu_id_utilisateur, lunonlu_suivi) " .
			"VALUES(:id_t, :id_u, :etat) " .
			"ON DUPLICATE KEY UPDATE lunonlu_suivi = :etat");
	$stmt->bindParam(':id_u', $id_u);
	$stmt->bindParam(':id_t', $id_t);
	$stmt->bindParam(':etat', $etat);
	$stmt->execute();
}

function ListerSuivisTicket($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT utilisateur_id, utilisateur_pseudo, lunonlu_suivi_envoye " .
			"FROM zcov2_tracker_tickets_flags " .
			"LEFT JOIN zcov2_utilisateurs ON utilisateur_id = lunonlu_id_utilisateur " .
			"WHERE lunonlu_id_ticket = :id AND lunonlu_suivi = 1");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetchAll();
}

function ListerReponses($id)
{
    $dbh = Doctrine_Manager::connection()->getDbh();

    $stmt = $dbh->prepare("SELECT version_id, version_date, version_commentaire, version_priorite, version_etat, " .
        "u1.utilisateur_id AS utilisateur_id, u1.utilisateur_pseudo AS utilisateur_pseudo, " .
        "groupe_class, u2.utilisateur_id AS id_admin, u2.utilisateur_pseudo AS pseudo_admin, " .
        "u1.utilisateur_avatar, cat_id, cat_nom " .
        "FROM zcov2_tracker_tickets_versions " .
        "LEFT JOIN zcov2_utilisateurs u1 ON version_id_utilisateur = u1.utilisateur_id " .
        "LEFT JOIN zcov2_utilisateurs u2 ON version_id_admin = u2.utilisateur_id " .
        "LEFT JOIN zcov2_categories ON version_id_categorie_concernee = cat_id " .
        "LEFT JOIN zcov2_groupes ON u1.utilisateur_id_groupe = groupe_id " .
        "WHERE version_id_ticket = :id " .
        "ORDER BY version_date");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt->fetchAll();
}