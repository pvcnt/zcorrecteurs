<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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
// +----------------------------------------------------------------------+
// | Copyright (c) www.zcorrecteurs.fr 2008                               |
// +----------------------------------------------------------------------+
// | Modèle concernant la mise en cache du nombre de MP non-lus           |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Auteurs:      Original DJ Fox <marthe59@yahoo.fr>                    |
// +----------------------------------------------------------------------+
// | Commencé le              : 03 septembre                              |
// | Dernière modification le : 06 septembre                              |
// +----------------------------------------------------------------------+

function CompteMPnonLu($id = null)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$id = $id ? (int)$id : $_SESSION['id'];

	$stmt = $dbh->prepare("
	SELECT COUNT(mp_participant_mp_id) AS nb
	FROM zcov2_mp_mp
	LEFT JOIN zcov2_mp_participants ON zcov2_mp_mp.mp_id = zcov2_mp_participants.mp_participant_mp_id
	WHERE mp_participant_id = :id AND mp_participant_statut >= 0 AND mp_participant_dernier_message_lu <> mp_dernier_message_id");

	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetchColumn();
}

function CompteMPTotal($id = null)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$id = $id ? (int)$id : $_SESSION['id'];

	$stmt = $dbh->prepare("
	SELECT COUNT(mp_participant_mp_id) AS nb
	FROM zcov2_mp_mp
	LEFT JOIN zcov2_mp_participants ON zcov2_mp_mp.mp_id = zcov2_mp_participants.mp_participant_mp_id
	WHERE mp_participant_id = :id AND mp_participant_statut >= 0");

	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetchColumn();
}

