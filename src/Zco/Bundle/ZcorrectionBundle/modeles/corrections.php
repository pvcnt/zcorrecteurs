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

function InfosCorrection($id_soumission)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	/* TODO : vérifier que la correction n'est pas commencée !! */

	$stmt = $dbh->prepare("
	SELECT DISTINCT
	soumission_id,
	soumission_date,
	soumission_type_tuto,
	soumission_id_tuto_sdz,
	soumission_id_utilisateur AS tutoteur_idsdz,
	soumission_pseudo_utilisateur AS tutoteur_pseudo,
	soumission_id_valido AS valido_idsdz,
	soumission_pseudo_valido AS valido_pseudo,
	soumission_sauvegarde,
	soumission_ip,
	soumission_prioritaire,
	soumission_id_tuto,
	soumission_recorrection,
	soumission_description,
	soumission_news,
	soumission_commentaire,
	soumission_avancement,
	soumission_etat,
	c1.correction_id AS correction_id,
	c2.correction_id AS recorrection_id,
	c1.correction_marque AS correction_marque,
	c2.correction_marque AS recorrection_marque,
	c1.correction_id_correcteur AS id_correcteur,
	c2.correction_id_correcteur AS id_recorrecteur,
	c1.correction_id_tuto_corrige as id_tuto_correction,
	c2.correction_id_tuto_corrige AS id_tuto_recorrection,
	c1.correction_commentaire as commentaire_correction,
	c2.correction_commentaire AS commentaire_recorrection,
	c1.correction_correcteur_invisible as correcteur_invisible_correction,
	c2.correction_correcteur_invisible AS correcteur_invisible_recorrection,
	c1.correction_abandonee AS correction_abandonee,
	c2.correction_abandonee AS recorrection_abandonee,
	c1.correction_commentaire_valido AS commentaire_valido_correction,
	c2.correction_commentaire_valido AS commentaire_valido_recorrection,
	mt.mini_tuto_titre AS mini_tuto_titre,
	bt.big_tuto_titre AS big_tuto_titre,
	c1.correction_date_debut as correction_date_debut,
	c1.correction_date_fin as correction_date_fin,
	c2.correction_date_debut as recorrection_date_debut,
	c2.correction_date_fin as recorrection_date_fin,
	u1.utilisateur_pseudo AS pseudo_correcteur,
	u2.utilisateur_pseudo AS pseudo_recorrecteur,
	u3.utilisateur_pseudo AS pseudo_envoyeur,
	u3.utilisateur_email AS email_envoyeur
	FROM zcov2_push_soumissions s
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	LEFT JOIN zcov2_utilisateurs u1 ON c1.correction_id_correcteur = u1.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u2 ON c2.correction_id_correcteur = u2.utilisateur_id
	LEFT JOIN zcov2_utilisateurs u3 ON soumission_id_utilisateur = u3.utilisateur_id
	LEFT JOIN zcov2_push_mini_tutos mt ON soumission_id_tuto = mini_tuto_id
	LEFT JOIN zcov2_push_big_tutos bt ON soumission_id_tuto = big_tuto_id
	WHERE s.soumission_id = :id_soumission");

	$stmt->bindParam(':id_soumission', $id_soumission);

	if ($stmt->execute() && ($resultat = $stmt->fetch(PDO::FETCH_ASSOC)))
	{
		$stmt->closeCursor();
		return $resultat;
	}

	return false;
}

function AjouterCorrection($id_tutoriel, $id_correcteur)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("INSERT INTO zcov2_push_corrections
	(correction_id_tuto_corrige, correction_id_correcteur)
	VALUES
	(:id_tutoriel, :id_correcteur)");

	$stmt->bindParam(':id_tutoriel',   $id_tutoriel);
	$stmt->bindParam(':id_correcteur', $id_correcteur);

	if($stmt->execute())
	{
		$stmt->closeCursor();
		return $dbh->lastInsertId();
	}

	return false;
}

function MettreAJourCorrection($correction_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_corrections
		SET correction_commentaire = :texte,
		correction_correcteur_invisible = :confidentialite,
		correction_commentaire_valido = :texte2
		WHERE correction_id=:correction_id");

	$conf = isset($_POST['confidentialite']) ? 1 : 0;
	$stmt->bindParam(':correction_id', $correction_id);
	$stmt->bindParam(':texte', $_POST['comm']);
	$stmt->bindParam(':texte2', $_POST['comm_valido']);
	$stmt->bindParam(':confidentialite', $conf);

	$stmt->execute();

}

function ChangerTutorielCorrige($correction_id, $tuto_id)
{
        $dbh = Doctrine_Manager::connection()->getDbh();

        $stmt = $dbh->prepare("UPDATE zcov2_push_corrections
                SET correction_id_tuto_corrige = :tuto_id 
                WHERE correction_id=:correction_id");

        $stmt->bindParam(':correction_id', $correction_id);
	$stmt->bindParam(':tuto_id', $tuto_id);

        $stmt->execute();
}


function MettreAJourAvancement()
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_soumissions
	SET soumission_avancement = :avancement
	WHERE soumission_id=:soumission_id
	");

	$stmt->bindParam(':soumission_id', $_GET['id']);
	$stmt->bindParam(':avancement', $_POST['avancement_nombre']);

	if($stmt->execute())
	{
		$stmt->closeCursor();
		return true;
	}
	return false;
}

function CommencerCorrection($correction_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_corrections
	SET correction_date_debut = NOW()
	WHERE correction_id = :correction_id
	");

	$stmt->bindParam(':correction_id', $correction_id);

	if($stmt->execute())
	{
		$stmt->closeCursor();
		return true;
	}

	return false;
}

function DemanderRecorrection($soumission_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_soumissions
	SET soumission_recorrection = 1
	WHERE soumission_id = :id
	");

	$stmt->bindParam(':id', $soumission_id);

	if ($stmt->execute()){
		$stmt->closeCursor();
		return true;
	}
	else{
		return false;
	}
}

function TerminerCorrection($correction_id, $correction = 1, $besoinRecorrection = 0)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On règle l'avancement
	if ($correction === 2)
	{
		$jointureSoumission = 'LEFT JOIN zcov2_push_soumissions ON soumission_id_correction_2 = correction_id';
		$avancement = 100;
	}
	else
	{
		$jointureSoumission = 'LEFT JOIN zcov2_push_soumissions ON soumission_id_correction_1 = correction_id';
		$avancement = 50;
	}

	//Si une recorrection est demandée
	if ($besoinRecorrection === 1)
	{$recorrection = ', soumission_recorrection = 1';}
	else
	{$recorrection = NULL;}

	//Enfin la requête
	$stmt = $dbh->prepare('UPDATE zcov2_push_corrections '.$jointureSoumission.'
	SET correction_date_fin = NOW(), soumission_avancement = '.$avancement.''.$recorrection.'
	WHERE correction_id = :correction_id
	');

	$stmt->bindParam(':correction_id', $correction_id);

	if($stmt->execute())
	{
		$stmt->closeCursor();
		return true;
	}

	return false;
}

function AbandonnerCorrection($correction_id)
{
	return RetirerCorrection($correction_id);
}

function RetirerCorrection($correction_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_corrections
	SET correction_abandonee = 1
	WHERE correction_id = :correction_id
	");

	$stmt->bindParam(':correction_id', $correction_id);

	if($stmt->execute())
	{
		$stmt->closeCursor();
		return true;
	}

	return false;
}

function ReprendreCorrection($correction_id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("UPDATE zcov2_push_corrections
	SET correction_abandonee = 0, correction_id_correcteur = :correcteur_id
	WHERE correction_id = :correction_id
	");

	$stmt->bindParam(':correcteur_id', $_SESSION['id']);
	$stmt->bindParam(':correction_id', $correction_id);

	if($stmt->execute())
	{
		$stmt->closeCursor();
		return true;
	}

	return false;
}

function RecupInfosCorrection($id_correction){
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("
	SELECT
	soumission_date, correction_date_debut, correction_date_fin, soumission_type_tuto, soumission_recorrection,
	correction_id_correcteur, correction_id_tuto_corrige, correction_commentaire,
	correction_correcteur_invisible, correction_abandonee, utilisateur_pseudo
	FROM zcov2_push_corrections
	LEFT JOIN zcov2_utilisateurs ON correction_id_correcteur = utilisateur_id
	LEFT JOIN zcov2_push_soumissions ON (soumission_id_correction_1=correction_id OR soumission_id_correction_2=correction_id)
	WHERE correction_id = :id_correction");

	$stmt->bindParam(':id_correction', $id_correction);

	if ($stmt->execute() && ($resultat = $stmt->fetch(PDO::FETCH_ASSOC)))
	{
		$stmt->closeCursor();
		return $resultat;
	}

	return false;
}
?>
