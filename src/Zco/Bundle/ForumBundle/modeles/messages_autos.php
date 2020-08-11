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
 * Modèle gérant les messages automatiques.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @begin 24/12/07
 * @last 01/01/09
 */

function ListerMessagesAutos()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT auto_id, auto_tag, auto_ferme, auto_nom, auto_resolu, auto_texte
	FROM zcov2_forum_messages_autos
	ORDER BY auto_nom");

	$stmt->execute();

	return $stmt->fetchAll();
}

function InfosMessageAuto($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT auto_id, auto_tag, auto_ferme, auto_nom, auto_texte, auto_resolu
	FROM zcov2_forum_messages_autos
	WHERE auto_id=:id");

	$stmt->bindParam(':id', $id);

	if($stmt->execute() && $retour = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		return $retour;
	}
	else
	{
		return false;
	}
}

function EditerMessageAuto($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$ferme = isset($_POST['ferme']) ? 1 : 0;
	$resolu = isset($_POST['resolu']) ? 1 : 0;

	$stmt = $dbh->prepare("
	UPDATE zcov2_forum_messages_autos
	SET auto_tag=:tag, auto_ferme=:ferme, auto_nom=:nom, auto_texte=:texte, auto_resolu=:resolu
	WHERE auto_id=:id");

	$stmt->bindParam(':ferme', $ferme);
	$stmt->bindParam(':resolu', $resolu);
	$stmt->bindParam(':tag', $_POST['tag']);
	$stmt->bindParam(':nom', $_POST['nom']);
	$stmt->bindParam(':texte', $_POST['texte']);
	$stmt->bindParam(':id', $id);

	$stmt->execute();

}

function AjouterMessageAuto()
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$ferme = isset($_POST['ferme']) ? 1 : 0;
	$resolu = isset($_POST['resolu']) ? 1 : 0;

	$stmt = $dbh->prepare("
	INSERT INTO zcov2_forum_messages_autos(auto_tag, auto_ferme, auto_nom, auto_texte, auto_resolu)
	VALUES(:tag, :ferme, :nom, :texte, :resolu)");

	$stmt->bindParam(':ferme', $ferme);
	$stmt->bindParam(':resolu', $resolu);
	$stmt->bindParam(':tag', $_POST['tag']);
	$stmt->bindParam(':nom', $_POST['nom']);
	$stmt->bindParam(':texte', $_POST['texte']);

	$stmt->execute();

}

function SupprimerMessageAuto($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	DELETE FROM zcov2_forum_messages_autos
	WHERE auto_id=:id");

	$stmt->bindParam(':id', $id);

	$stmt->execute();

}

function EnregistrerMessageAuto($forum_id, $annonce, $ferme, $resolu, $corbeille, $texte, $titre)
{
	//IP
	$ip = ip2long(\Container::getService('request')->getClientIp(true));

	$dbh = Doctrine_Manager::connection()->getDbh();

	//On crée le post
	$stmt = $dbh->prepare("INSERT INTO zcov2_forum_messages (message_id, message_auteur, message_texte, message_date, message_sujet_id, message_edite_auteur, message_edite_date, message_ip)
	VALUES ('', :message_auteur, :message_texte, NOW(), :message_sujet_id, 0, '', ".$ip.")");
	$stmt->bindParam(':message_auteur', $_SESSION['id']);
	$stmt->bindParam(':message_texte', $texte);
	$stmt->bindParam(':message_sujet_id', $_GET['s']);
	$stmt->execute();

	//On récupère l'id de l'enregistrement qui vient d'être créé (l'id du nouveau post).
	$nouveau_message_id = $dbh->lastInsertId();

	//Grâce au numéro du post récupéré, on peut updater la table des sujets pour indiquer que ce post est le dernier du sujet, et pour incrémenter le nombre de réponses, et pour changer (ou pas) le type, le statut du sujet, sa résolution ou sa place (corbeille ou pas).
	$stmt = $dbh->prepare("UPDATE zcov2_forum_sujets
	SET sujet_dernier_message = :sujet_dernier_message, sujet_reponses = sujet_reponses+1, sujet_annonce = :sujet_annonce, sujet_ferme = :sujet_ferme, sujet_resolu = :sujet_resolu, sujet_titre = :sujet_titre
	WHERE sujet_id = :sujet_id");
	$stmt->bindParam(':sujet_dernier_message', $nouveau_message_id);
	$stmt->bindParam(':sujet_id', $_GET['s']);
	$stmt->bindParam(':sujet_annonce', $annonce);
	$stmt->bindParam(':sujet_ferme', $ferme);
	$stmt->bindParam(':sujet_resolu', $resolu);
	$stmt->bindParam(':sujet_titre', $titre);
	$stmt->execute();



	//Puis on met à jour la table lu / nonlu
	$stmt = $dbh->prepare("UPDATE zcov2_forum_lunonlu
	SET lunonlu_message_id = :message_id, lunonlu_participe = 1
	WHERE lunonlu_utilisateur_id = :user_id AND lunonlu_sujet_id = :sujet_id");
	$stmt->bindParam(':user_id', $_SESSION['id']);
	$stmt->bindParam(':sujet_id', $_GET['s']);
	$stmt->bindParam(':message_id', $nouveau_message_id);

	$stmt->execute();

	if(!$corbeille)
	{
		//Enfin, on met à jour la table forums : on met à jour le dernier message posté du forum.
		$stmt = $dbh->prepare("UPDATE zcov2_categories
		SET cat_last_element = :forum_dernier_post_id
		WHERE cat_id = :forum_id");
		$stmt->bindParam(':forum_dernier_post_id', $nouveau_message_id);
		$stmt->bindParam(':forum_id', $forum_id);
		$stmt->execute();



		//Enfin, on incrémente le nombre de messages du membre :)
		$stmt = $dbh->prepare("UPDATE zcov2_utilisateurs
		SET utilisateur_forum_messages = utilisateur_forum_messages+1
		WHERE utilisateur_id = :utilisateur_id");
		$stmt->bindParam(':utilisateur_id', $_SESSION['id']);
		$stmt->execute();


	}

	return $nouveau_message_id;
}
?>
