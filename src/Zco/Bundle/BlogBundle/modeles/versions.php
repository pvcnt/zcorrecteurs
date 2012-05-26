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
 * Modèle gérant tout ce qui concerne le versionnage des billets.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @begin 18/09/2007
 * @last 26/04/2009 vincent1870
 */

/**
 * Liste les versions d'un billet.
 * @param integer $id			L'id du billet.
 * @return array
 */
function ListerVersions($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

    //On récupère les versions
	$stmt = $dbh->prepare("SELECT version_titre, version_sous_titre,
			version_texte, version_intro, version_id, version_ip,
			utilisateur_id, utilisateur_pseudo, version_date, version_id_fictif,
			version_commentaire
			FROM zcov2_blog_versions
			LEFT JOIN zcov2_utilisateurs ON version_id_utilisateur = utilisateur_id
			WHERE version_id_billet = :id
			ORDER BY version_date");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$return = $stmt->fetchAll();
	$stmt->closeCursor();

	//Et on compare
	$texte_old = '';
	$intro_old = '';
	$titre_old = '';
	$sous_titre_old = '';
	$id_old = 0;
	$i = 0;
	while($i < count($return))
	{
		if($return[$i]['version_texte'] != $texte_old) $return[$i]['texte'] = 'rouge';
		else $return[$i]['texte'] = 'vertf';
		if($return[$i]['version_intro'] != $intro_old) $return[$i]['intro'] = 'rouge';
		else $return[$i]['intro'] = 'vertf';
		if($return[$i]['version_titre'] != $titre_old) $return[$i]['titre'] = 'rouge';
		else $return[$i]['titre'] = 'vertf';
		if($return[$i]['version_sous_titre'] != $sous_titre_old) $return[$i]['sous_titre'] = 'rouge';
		else $return[$i]['sous_titre'] = 'vertf';

		$return[$i]['id_precedent'] = $id_old;
		$id_old = $return[$i]['version_id'];
		$texte_old = $return[$i]['version_texte'];
		$titre_old = $return[$i]['version_titre'];
		$sous_titre_old = $return[$i]['version_sous_titre'];
		$intro_old = $return[$i]['version_intro'];
		$i++;
	}

	return array_reverse($return);
}

/**
 * Infos sur une version.
 * @param integer $id			L'id de la version.
 * @return array
 */
function InfosVersion($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT version_titre, version_sous_titre, version_intro, " .
			"version_texte, version_id, version_ip, version_id_billet, version_commentaire, " .
			"utilisateur_id, utilisateur_pseudo, version_date, version_id_billet " .
			"FROM zcov2_blog_versions " .
			"LEFT JOIN zcov2_utilisateurs ON utilisateur_id = version_id_utilisateur " .
			"WHERE version_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}