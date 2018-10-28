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
 * Récupère les commentaires d'un billet.
 * @param integer $id			L'id du billet.
 * @param integer $page			La page demandée.
 * @return array
 */
function ListerCommentairesBillet($id, $page)
{
	$nbCommentairesParPage = 15;
	$dbh = Doctrine_Manager::connection()->getDbh();
	if($page < 0)
	{
		$debut = 0;
		$nombre = $nbCommentairesParPage;
		$order = 'DESC';
	}
	else
	{
		$debut = ($page - 1) * $nbCommentairesParPage;
		$nombre = $nbCommentairesParPage;
		$order = 'ASC';
	}

	$stmt = $dbh->prepare("SELECT commentaire_id, commentaire_texte, " .
			"commentaire_ip, Ma.utilisateur_id AS id_auteur, " .
			"Ma.utilisateur_pseudo AS pseudo_auteur, " .
			"Ma.utilisateur_avatar AS avatar_auteur, " .
			"Ma.utilisateur_sexe, " .
			"Ma.utilisateur_signature AS signature_auteur, " .
			"Ma.utilisateur_nb_sanctions AS nb_sanctions_auteur, " .
			"Ma.utilisateur_pourcentage AS pourcentage_auteur, " .
			"Ma.utilisateur_titre, " .
			"Ma.utilisateur_citation, Ma.utilisateur_absent, " .
			"Ma.utilisateur_fin_absence, " .

			"Mb.utilisateur_id AS id_edite, " .
			"Mb.utilisateur_pseudo AS pseudo_edite, " .
			"groupe_class, groupe_nom, groupe_logo, groupe_logo_feminin, " .
			"commentaire_date, commentaire_edite_date " .

			"FROM zcov2_blog_commentaires " .
			"LEFT JOIN zcov2_utilisateurs Ma ON Ma.utilisateur_id = commentaire_id_utilisateur " .
			"LEFT JOIN zcov2_utilisateurs Mb ON Mb.utilisateur_id = commentaire_id_edite " .
			"LEFT JOIN zcov2_groupes ON Ma.utilisateur_id_groupe = groupe_id " .

			"WHERE commentaire_id_billet = :id " .
			"ORDER BY commentaire_date ".$order." " .
			"LIMIT ".$nombre." OFFSET ".$debut);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetchAll();
}

/**
 * Récupère le nombre de commentaires d'un billet.
 * @param integer $id				L'id du billet.
 * @return integer
 */
function CompterCommentairesBillet($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT COUNT(*) " .
			"FROM zcov2_blog_commentaires " .
			"WHERE commentaire_id_billet = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetchColumn();
}

/**
 * Ajoute un commentaire.
 * @param integer $id				L'id du billet.
 * @param integer $id_u				L'id de l'auteur.
 * @param string $texte				Le nouveau texte.
 * @return integer					L'id du commentaire inséré.
 */
function AjouterCommentaire($id, $id_u, $texte)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("INSERT INTO zcov2_blog_commentaires(" .
			"commentaire_id_billet, commentaire_id_utilisateur, " .
			"commentaire_ip, commentaire_texte, commentaire_date) " .
			"VALUES(:id_billet, :id_utilisateur, :ip, :texte, NOW())");
	$stmt->bindParam(':id_billet', $id);
	$stmt->bindParam(':id_utilisateur', $id_u);
	$stmt->bindValue(':ip', ip2long(\Container::request()->getClientIp()));
	$stmt->bindParam(':texte', $texte);
	$stmt->execute();
	return $dbh->lastInsertId();
}

/**
 * Édite un commentaire.
 * @param integer $id				L'id du commentaire.
 * @param integer $id_u				L'id du visiteur éditant le commentaire.
 * @param string $texte				Le nouveau texte.
 * @return void.
 */
function EditerCommentaire($id, $id_u, $texte)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_blog_commentaires " .
			"SET commentaire_texte = :texte, " .
			"commentaire_id_edite = :id_edite, " .
			"commentaire_edite_date = NOW() " .
			"WHERE commentaire_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->bindParam(':id_edite', $id_u);
	$stmt->bindParam(':texte', $texte);
	$stmt->execute();
}

/**
 * Supprime un commentaire.
 * @param integer $id				L'id du commentaire.
 * @return void
 */
function SupprimerCommentaire($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("DELETE FROM zcov2_blog_commentaires " .
			"WHERE commentaire_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

/**
 * Retourne la page d'un commentaire.
 * @param integer $id_comm				L'id du commentaire.
 * @param integer $id_billet			L'id du billet.
 * @return integer
 */
function TrouverPageCommentaire($id_comm, $id_billet)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT commentaire_id " .
			"FROM zcov2_blog_commentaires " .
			"WHERE commentaire_id_billet = :id " .
			"ORDER BY commentaire_date");
	$stmt->bindParam(':id', $id_billet);
	$stmt->execute();
	$billets = $stmt->fetchAll();
	$nb = 1;
	$page = 1;

	foreach($billets as $b)
	{
		if($nb > 15)
		{
			$page ++;
			$nb = 1;
		}

		if($b['commentaire_id'] == $id_comm)
			return $page;

		$nb ++;
	}

	return false;
}

/**
 * Marquer les commentaires comme lus.
 * @param integer $infos			Infos sur le billet.
 * @param integer $page				La page courante.
 * @param integer $comms			La liste des commentaires.
 */
function MarquerCommentairesLus($infos, $page, $comms)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On regarde si on a déjà lu le sujet
	$stmt = $dbh->prepare("SELECT lunonlu_id_commentaire
	FROM zcov2_blog_lunonlu
	WHERE lunonlu_id_utilisateur = :id_utilisateur AND lunonlu_id_billet = :id_billet");
	$stmt->bindParam(':id_utilisateur', $_SESSION['id']);
	$stmt->bindParam(':id_billet', $infos['blog_id']);
	$stmt->execute();
	$dernier_lu = $stmt->fetchColumn();

	//On récupère l'id du dernier message
	$id_comm = null;
	foreach($comms as $c)
		$id_comm = $c['commentaire_id'];

	//Si l'id du dernier commentaire est supérieur à celui du dernier lu, où si le billet n'a jamais été lu
	if(empty($dernier_lu) || $id_comm > $dernier_lu)
	{
		$stmt = $dbh->prepare("INSERT INTO zcov2_blog_lunonlu(lunonlu_id_utilisateur, lunonlu_id_billet, lunonlu_id_commentaire)
		VALUES(:id_utilisateur, :id_billet, :id_comm)
		ON DUPLICATE KEY UPDATE lunonlu_id_commentaire = :id_comm");
		$stmt->bindParam(':id_utilisateur', $_SESSION['id']);
		$stmt->bindParam(':id_billet', $infos['blog_id']);
		$stmt->bindParam(':id_comm', $id_comm);
		$stmt->execute();
	}
}

/**
 * Supprime tous les commentaires d'un billet.
 * @param integer $id				L'id du billet.
 * @return void
 */
function SupprimerCommentairesBillet($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("DELETE FROM zcov2_blog_commentaires " .
			"WHERE commentaire_id_billet = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	$stmt = $dbh->prepare("DELETE FROM zcov2_blog_lunonlu " .
			"WHERE lunonlu_id_billet = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
}

/**
 * Récupère des informations sur un billet.
 * @param integer $id				L'id du commentaire.
 * @return void
 */
function InfosCommentaire($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT commentaire_id, commentaire_texte, " .
			"commentaire_ip, utilisateur_id, utilisateur_pseudo, blog_id, " .
			"version_titre, version_sous_titre, blog_commentaires, blog_id_categorie, " .
			"commentaire_id_billet " .
			"FROM zcov2_blog_commentaires " .
			"LEFT JOIN zcov2_utilisateurs ON utilisateur_id = commentaire_id_utilisateur " .
			"LEFT JOIN zcov2_blog ON blog_id = commentaire_id_billet " .
			"LEFT JOIN zcov2_blog_versions ON blog_id_version_courante = version_id " .
			"WHERE commentaire_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}