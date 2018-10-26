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