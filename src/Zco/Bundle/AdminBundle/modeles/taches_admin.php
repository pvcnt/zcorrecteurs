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
 * Modèle gérant le comptage des tâches d'administration.
 *
 * @package	zCorrecteurs.fr
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @see		libs/admin.class.php
 */

function CompterTachesRecrutement()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT COUNT(*) " .
			"FROM zcov2_recrutements_candidatures " .
			"LEFT JOIN zcov2_recrutements ON candidature_id_recrutement = recrutement_id " .
			"WHERE recrutement_etat = ".RECRUTEMENT_OUVERT." AND " .
					"(candidature_etat = ".CANDIDATURE_ENVOYE." OR " .
					"candidature_etat = ".CANDIDATURE_TESTE." OR " .
					"candidature_etat = ".CANDIDATURE_ATTENTE_TEST.")");
	$stmt->execute();
	return $stmt->fetchColumn();
}

function CompterTachesBlog()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT COUNT(*) " .
			"FROM zcov2_blog " .
			"WHERE blog_etat IN(".BLOG_PROPOSE.",".BLOG_PREPARATION.")");
	$stmt->execute();
	return $stmt->fetchColumn();
}

function CompterTachesDictees()
{
	return Doctrine_Query::create()
		->select('COUNT(*)')
		->from('Dictee')
		->where('etat = ?', DICTEE_PROPOSEE)
		->count();
}

function CompterTachesCommentairesBlog()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$groupes = array();
	foreach(ListerGroupes() as $grp)
	{
		$droits = RecupererDroitsGroupe($grp['groupe_id']);
		if(isset($droits['blog_supprimer_commentaires'])
		 ||isset($droits['blog_editer_commentaires']))
			$groupes[] = (int)$grp['groupe_id'];
	}
	$groupes = implode(', ', $groupes);

	$stmt = $dbh->prepare('SELECT COUNT(*) '
		.'FROM '.Container::getParameter('database.prefix').'blog_commentaires '

		.'INNER JOIN '.Container::getParameter('database.prefix').'blog '
		.'ON blog_id = commentaire_id_billet '

		.'INNER JOIN '.Container::getParameter('database.prefix').'blog_versions '
		.'ON version_id = blog_id_version_courante '

		.'LEFT JOIN ( '
			.'SELECT lunonlu_id_billet AS billet, '
			.'MAX(commentaire_id) AS dernier_commentaire, '
			.'MAX(lunonlu_id_commentaire) AS dernier_lu '
			.'FROM '.Container::getParameter('database.prefix').'blog_lunonlu '

			.'INNER JOIN '.Container::getParameter('database.prefix').'blog '
			.'ON blog_id = lunonlu_id_billet '

			.'LEFT JOIN '.Container::getParameter('database.prefix').'utilisateurs '
			.'ON lunonlu_id_utilisateur = utilisateur_id '

			.'INNER JOIN '.Container::getParameter('database.prefix').'blog_commentaires '
			.'ON commentaire_id_billet = lunonlu_id_billet '

			.'WHERE blog_etat = '.BLOG_VALIDE.' '
			.'AND utilisateur_id_groupe IN('.$groupes.') '
			.'GROUP BY lunonlu_id_billet '
		.') AS admin_commentaires '
		.'ON billet = commentaire_id_billet '

		.'WHERE blog_etat = '.BLOG_VALIDE.' '
		.'AND (dernier_lu IS NULL '
		.'OR commentaire_id > dernier_lu) ');
	$stmt->execute();
	return $stmt->fetchColumn();
}

function CompterTachesChangementsPseudo()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT COUNT(*) " .
			"FROM zcov2_changements_pseudos " .
			"WHERE changement_etat = ".CH_PSEUDO_ATTENTE);
	$stmt->execute();
	return $stmt->fetchColumn();
}

function CompterTachesAlertes()
{
	return Doctrine_Query::create()
		->select('COUNT(*)')
		->from('ForumAlerte')
		->where('resolu = ?', false)
		->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
}

function CompterTachesAlertesMP()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT COUNT(*) " .
			"FROM zcov2_mp_alertes " .
			"WHERE mp_alerte_resolu = 0");
	$stmt->execute();
	return $stmt->fetchColumn();
}

function CompterTachesZcorrection()
{
	include_once(BASEPATH.'/src/Zco/Bundle/ZcorrectionBundle/modeles/soumissions.php');
	return CompterSoumissions();
}

function CompterTachesDemandes()
{
	include_once(BASEPATH.'/src/Zco/Bundle/EvolutionBundle/modeles/tickets.php');
	$tickets = CompterTicketsEtat(true, 'bug');
	return $tickets['open'];
}

function CompterTachesDemandesTodo()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT COUNT(*) " .
			"FROM zcov2_tracker_tickets " .
			"LEFT JOIN zcov2_tracker_tickets_versions ON ticket_id_version_courante = version_id " .
			"WHERE version_id_admin = :id AND version_etat NOT IN(4,5,7,8,9)");
	$stmt->bindParam(':id', $_SESSION['id']);
	$stmt->execute();
	return $stmt->fetchColumn();
}

function CompterTachesTaches()
{
	include_once(BASEPATH.'/src/Zco/Bundle/EvolutionBundle/modeles/tickets.php');
	$tickets = CompterTicketsEtat(true, 'tache');
	return $tickets['open'];
}

function CompterTachesFactures()
{
	return Doctrine_Query::create()
		->select('COUNT(*)')
		->from('Facture')
		->where('payee = ?', false)
		->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
}

function CompterTachesDons()
{
	return Doctrine_Query::create()
		->select('COUNT(*)')
		->from('Don')
		->where('paiement_valide = ?', false)
		->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
}

function CompterTachesPublicite()
{
	return Doctrine_Query::create()
		->select('COUNT(*)')
		->from('Publicite')
		->where('approuve = ?', 'attente')
		->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
}
