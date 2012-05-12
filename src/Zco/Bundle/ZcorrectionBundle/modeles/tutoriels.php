<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Modèle pour la gestion des tutoriels.
 *
 * @author Savageman, vincent1870
 * @begin 2007
 * @last 13/04/2008 vincent1870
 */

/**
 * Ajoute un nouveau big-tutoriel.
 * @param string $titre				Le titre du tutoriel.
 * @param integer $avancement		L'avancement du tutoriel.
 * @param integer $difficulte		La difficulté du tutoriel.
 * @param string $introduction		L'introduction générale.
 * @param string $conclusion		Le conclusion générale.
 * @param integer $id_sdz			L'id du tutoriel sur le SdZ.
 * @return integer					L'id de l'enregistrement inséré.
 */
function AjouterBigTuto($titre, $avancement, $difficulte, $introduction, $conclusion, $id_sdz)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if (is_null($id_sdz))
	{
		$id_sdz = 0;
	}

	$stmt = $dbh->prepare("INSERT INTO zcov2_push_big_tutos " .
			"(big_tuto_titre, big_tuto_avancement, big_tuto_difficulte, " .
			"big_tuto_introduction, big_tuto_conclusion, big_tuto_id_sdz) " .
			"VALUES(:titre, :avancement, :difficulte, :introduction, " .
			":conclusion, :id_sdz)");
	$stmt->bindParam(':titre',        $titre);
	$stmt->bindParam(':avancement',   $avancement);
	$stmt->bindParam(':difficulte',   $difficulte);
	$stmt->bindParam(':introduction', $introduction);
	$stmt->bindParam(':conclusion',   $conclusion);
	$stmt->bindParam(':id_sdz',       $id_sdz);

	$stmt->execute();
	$stmt->closeCursor();
	return $dbh->lastInsertId();
}

/**
 * Ajoute une sous-partie à un big-tutoriel.
 * @param integer $id_big_tuto		L'id du big-tutoriel père.
 * @param string $titre				Le titre de la partie.
 * @param string $introduction		L'introduction.
 * @param string $conclusion		Le conclusion.
 * @param integer $id_sdz			L'id du tutoriel sur le SdZ.
 * @return integer					L'id de l'enregistrement inséré.
 */
function AjouterPartie($id_big_tuto, $titre, $introduction, $conclusion, $id_sdz)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if (is_null($id_sdz))
	{
		$id_sdz = 0;
	}

	$stmt = $dbh->prepare("INSERT INTO zcov2_push_big_tutos_parties" .
			"(partie_id_big_tuto, partie_titre, partie_introduction, " .
			"partie_conclusion, partie_id_sdz) " .
			"VALUES(:id_big_tuto, :titre, :introduction, :conclusion, :id_sdz) ");
	$stmt->bindParam(':id_big_tuto',  $id_big_tuto);
	$stmt->bindParam(':titre',        $titre);
	$stmt->bindParam(':introduction', $introduction);
	$stmt->bindParam(':conclusion',   $conclusion);
	$stmt->bindParam(':id_sdz',       $id_sdz);

	$stmt->execute();
	$stmt->closeCursor();
	return $dbh->lastInsertId();
}

/**
 * Ajoute un nouveau mini-tutoriel.
 * @param integer $id_partie		L'id de la partie parente (dans le cas d'un big-tuto).
 * @param string $titre				Le titre du tutoriel.
 * @param integer $avancement		L'avancement du tutoriel.
 * @param integer $difficulte		La difficulté du tutoriel.
 * @param string $introduction		L'introduction générale.
 * @param string $conclusion		Le conclusion générale.
 * @param integer $id_sdz			L'id du tutoriel sur le SdZ.
 * @return integer					L'id de l'enregistrement inséré.
 */
function AjouterMiniTuto($id_partie, $titre, $avancement, $difficulte, $introduction, $conclusion, $id_sdz) {
	$dbh = Doctrine_Manager::connection()->getDbh();

	if (is_null($id_sdz))
	{
		$id_sdz = 0;
	}

	$stmt = $dbh->prepare("INSERT INTO zcov2_push_mini_tutos " .
			"(mini_tuto_id_partie, mini_tuto_titre, mini_tuto_avancement, " .
			"mini_tuto_difficulte, mini_tuto_introduction, mini_tuto_conclusion, " .
			"mini_tuto_id_sdz) " .
			"VALUES(:id_partie, :titre, :avancement, :difficulte, :introduction, " .
			":conclusion, :id_sdz)");
	$stmt->bindParam(':id_partie',    $id_partie);
	$stmt->bindParam(':titre',        $titre);
	$stmt->bindParam(':avancement',   $avancement);
	$stmt->bindParam(':difficulte',   $difficulte);
	$stmt->bindParam(':introduction', $introduction);
	$stmt->bindParam(':conclusion',   $conclusion);
	$stmt->bindParam(':id_sdz',       $id_sdz);

	$stmt->execute();
	$stmt->closeCursor();
	return $dbh->lastInsertId();
}

/**
 * Ajoute une sous-partie à un mini-tutoriel.
 * @param integer $id_mini_tuto		L'id du mini-tutoriel père.
 * @param string $titre				Le titre de la partie.
 * @param string $texte				Le contenu de la partie.
 * @param integer $id_sdz			L'id de la partie sur le SdZ.
 * @return integer					L'id de l'enregistrement inséré.
 */
function AjouterSousPartie($id_mini_tuto, $titre, $texte, $id_sdz)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	if (is_null($id_sdz))
	{
		$id_sdz = 0;
	}

	$stmt = $dbh->prepare("INSERT INTO zcov2_push_mini_tuto_sous_parties " .
			"(sous_partie_id_mini_tuto, sous_partie_titre, sous_partie_texte, " .
			"sous_partie_id_sdz) " .
			"VALUES(:id_mini_tuto, :titre, :texte, :id_sdz)");
	$stmt->bindParam(':id_mini_tuto', $id_mini_tuto);
	$stmt->bindParam(':titre',        $titre);
	$stmt->bindParam(':texte',        $texte);
	$stmt->bindParam(':id_sdz',       $id_sdz);

	$stmt->execute();
	$stmt->closeCursor();
	return $dbh->lastInsertId();
}

function AjouterQuestion($id_mini_tuto, $label, $explication, $id_sdz) {
	$dbh = Doctrine_Manager::connection()->getDbh();

	if (is_null($id_sdz))
	{
		$id_sdz = 0;
	}

	$stmt = $dbh->prepare("INSERT INTO zcov2_push_qcm_questions
	(question_id_mini_tuto, question_label, question_explications, question_id_sdz)
	VALUES
	(:id_mini_tuto, :label, :explication, :id_sdz) ");

	$stmt->bindParam(':id_mini_tuto', $id_mini_tuto);
	$stmt->bindParam(':label',        $label);
	$stmt->bindParam(':explication',  $explication);
	$stmt->bindParam(':id_sdz',       $id_sdz);

	if ($stmt->execute()) { $stmt->closeCursor(); return $dbh->lastInsertId(); }

	return false;
}

function AjouterReponse($id_qcm_question, $texte, $vrai, $id_sdz) {
	$dbh = Doctrine_Manager::connection()->getDbh();

	if (is_null($id_sdz))
	{
		$id_sdz = 0;
	}

	$stmt = $dbh->prepare("INSERT INTO zcov2_push_qcm_reponses
	(reponse_id_qcm_question, reponse_texte, reponse_vrai, reponse_id_sdz)
	VALUES
	(:id_qcm_question, :texte, :vrai, :id_sdz) ");

	$stmt->bindParam(':id_qcm_question', $id_qcm_question);
	$stmt->bindParam(':texte',           $texte);
	$stmt->bindParam(':vrai',            $vrai);
	$stmt->bindParam(':id_sdz',          $id_sdz);

	if ($stmt->execute()) { $stmt->closeCursor(); return $dbh->lastInsertId(); }

	return false;
}

function CopierTutoSoumission($id_soumission)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare("SELECT
	soumission_type_tuto,
	soumission_id_tuto,
	c1.correction_id_tuto_corrige as id_tuto_corrige,
	c2.correction_id_tuto_corrige as id_tuto_recorrige,
	c1.correction_abandonee AS correction_abandonee,
	c2.correction_abandonee AS recorrection_abandonee
	FROM zcov2_push_soumissions
	LEFT JOIN zcov2_push_corrections c1 ON soumission_id_correction_1 = c1.correction_id
	LEFT JOIN zcov2_push_corrections c2 ON soumission_id_correction_2 = c2.correction_id
	WHERE soumission_id = :id_soumission");

	$stmt->bindParam(':id_soumission', $id_soumission);

	if($stmt->execute())
	{
		list($type_tuto, $id_tuto_origine, $id_tuto_corrige, $id_tuto_recorrige, $correction_abandonee, $recorrection_abandonee) = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();

		if (!empty($id_tuto_recorrige))
		{
			if (0 == $recorrection_abandonee)
			{
				return false;
			}
			else
			{
				return $id_tuto_recorrige;
			}
		}
		else if (!empty($id_tuto_corrige))
		{
			if (0 == $correction_abandonee)
			{
				if (MINI_TUTO == $type_tuto)
				{
					return DupliquerMiniTuto($id_tuto_corrige);
				}
				else if (BIG_TUTO == $type_tuto)
				{
					return DupliquerBigTuto($id_tuto_corrige);
				}
				else
				{
					//afficher_erreur('Erreur : type du tutoriel inconnu.');
					return false;
				}
			}
			else
			{
				//afficher_message('Tutoriel déjà copié, reprise du tutoriel en cours de correction.');
				return $id_tuto_corrige;
			}
		}
		else
		{
			//afficher_message('Tutoriel jamais corrigé ! Copie du tutoriel d\'origine pour la correction.');

			if (MINI_TUTO == $type_tuto)
			{
				return DupliquerMiniTuto($id_tuto_origine);
			}
			else if (BIG_TUTO == $type_tuto)
			{
				return DupliquerBigTuto($id_tuto_origine);
			}
			else
			{
				//afficher_erreur('Erreur : type du tuto inconnu.');
				return false;
			}
		}
	}
	return false;

}

function DupliquerBigTuto($id_big_tuto)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Informations sur le big-tuto
	$stmt = $dbh->prepare("SELECT big_tuto_titre, big_tuto_avancement, big_tuto_introduction, big_tuto_conclusion, big_tuto_id_sdz, big_tuto_difficulte
	FROM zcov2_push_big_tutos
	WHERE big_tuto_id = $id_big_tuto");

	if ($stmt->execute())
	{
		list($a, $b, $c, $d, $e, $f) = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();

		//Duplication du big-tuto
		$id_copie_big_tuto = AjouterBigTuto($a, $b, $f, $c, $d, $e);

		//Listage des parties
		$stmt = $dbh->prepare("SELECT partie_id, partie_titre, partie_introduction, partie_conclusion, partie_id_sdz
		FROM zcov2_push_big_tutos_parties
		WHERE partie_id_big_tuto = $id_big_tuto
		ORDER BY partie_id ASC");

		if ($stmt->execute())
		{
			$ListeParties = $stmt->fetchAll();
			$stmt->closeCursor();

			//Duplication des parties
			foreach($ListeParties as $partie)
			{
				if (!empty($partie))
				{
					$id_copie_partie = AjouterPartie($id_copie_big_tuto, $partie['partie_titre'], $partie['partie_introduction'], $partie['partie_conclusion'], $partie['partie_id_sdz']);

					//Duplication des mini-tutos
					$stmt = $dbh->prepare("SELECT mini_tuto_id
					FROM zcov2_push_mini_tutos
					WHERE mini_tuto_id_partie = ".$partie['partie_id']."
					ORDER BY mini_tuto_id ASC");

					if ($stmt->execute())
					{
						$ListeMiniTutos = $stmt->fetchAll();
						$stmt->closeCursor();

						foreach($ListeMiniTutos as $mini_tuto)
						{
							// $id_copie_mini_tuto = DupliquerMiniTuto($mini_tuto['mini_tuto_id'], $id_copie_partie);
							DupliquerMiniTuto($mini_tuto['mini_tuto_id'], $id_copie_partie);
						}
					}
				}
			}
		}
		else
		{
			return false;
		}
		return $id_copie_big_tuto;
	}

	return false;
}

function DupliquerMiniTuto($id_mini_tuto, $id_partie = 0)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//On récupère les infos sur le mini-tuto
	$stmt = $dbh->prepare("SELECT mini_tuto_titre, mini_tuto_avancement, mini_tuto_introduction, mini_tuto_conclusion, mini_tuto_id_sdz, mini_tuto_difficulte
	FROM zcov2_push_mini_tutos
	WHERE mini_tuto_id = $id_mini_tuto");

	if ($stmt->execute())
	{
		list($a, $b, $c, $d, $e, $f) = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();

		//On ajoute le mini-tuto
		$id_copie_mini_tuto = AjouterMiniTuto($id_partie, $a, $b, $f, $c, $d, $e);

		//On liste les parties
		$stmt = $dbh->prepare("SELECT sous_partie_id, sous_partie_titre, sous_partie_texte, sous_partie_id_sdz
		FROM zcov2_push_mini_tuto_sous_parties
		WHERE sous_partie_id_mini_tuto = $id_mini_tuto
		ORDER BY sous_partie_id ASC");

		if ($stmt->execute())
		{
			$ListeParties = $stmt->fetchAll();
			$stmt->closeCursor();

			//On ajoute les parties
			foreach($ListeParties as $partie)
			{
				$id_copie_partie = AjouterSousPartie($id_copie_mini_tuto, $partie['sous_partie_titre'], $partie['sous_partie_texte'], $partie['sous_partie_id_sdz']);
			}
		}

		//On liste questions
		$stmt = $dbh->prepare("SELECT question_id, question_label, question_explications, question_id_sdz
		FROM zcov2_push_qcm_questions
		WHERE question_id_mini_tuto = $id_mini_tuto
		ORDER BY question_id ASC");
		if ($stmt->execute())
		{
			$ListeQcm = $stmt->fetchAll();
			$stmt->closeCursor();

			//On ajoute les questions
			foreach($ListeQcm as $qcm)
			{
				$id_copie_qcm = AjouterQuestion($id_copie_mini_tuto, $qcm['question_label'], $qcm['question_explications'], $qcm['question_id_sdz']);

				//On liste les réponses
				$stmt = $dbh->prepare("SELECT reponse_texte, reponse_vrai, reponse_id_sdz
				FROM zcov2_push_qcm_reponses
				WHERE reponse_id_qcm_question = ".$qcm['question_id']."
				ORDER BY reponse_id ASC");

				if ($stmt->execute())
				{
					$ListeReponses = $stmt->fetchAll();
					$stmt->closeCursor();

					//On ajoute les réponses
					foreach($ListeReponses as $reponse)
					{
						AjouterReponse($id_copie_qcm, $reponse['reponse_texte'], $reponse['reponse_vrai'], $reponse['reponse_id_sdz']);
					}
				}
			}
		}

		return $id_copie_mini_tuto;
	}
	else
	{
		return false;
	}
}

function RecupererMiniTuto($id_mini_tuto)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT
	sous_partie_id,
	sous_partie_titre,
	sous_partie_texte
	FROM zcov2_push_mini_tuto_sous_parties
	WHERE sous_partie_id_mini_tuto = :id_mini_tuto
	ORDER BY sous_partie_id ASC");

	$stmt->bindParam(':id_mini_tuto', $id_mini_tuto);

	if ($stmt->execute() && $retour[0] = $stmt->fetch(PDO::FETCH_ASSOC))
	{
 		while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$retour[] = $resultat;
		}
		$stmt->closeCursor();
		return $retour;
	}



	return false;
}

function InfosMiniTuto($id_mini_tuto)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT
	mini_tuto_id,
	mini_tuto_id_partie,
	mini_tuto_avancement,
	mini_tuto_titre,
	mini_tuto_introduction,
	mini_tuto_conclusion,
	mini_tuto_id_sdz
	FROM zcov2_push_mini_tutos
	WHERE mini_tuto_id = :id_mini_tuto");

	$stmt->bindParam(':id_mini_tuto', $id_mini_tuto);

	if ($stmt->execute() && $retour = $stmt->fetch(PDO::FETCH_ASSOC))
	{
 		$stmt->closeCursor();
		return $retour;
	}
	else
	{
		return false;
	}
}

function ListeTutosPartie($id_partie)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT
	mini_tuto_id,
	mini_tuto_titre
	FROM zcov2_push_mini_tutos
	WHERE mini_tuto_id_partie = :id_partie
	ORDER BY mini_tuto_id ASC");

	$stmt->bindParam(':id_partie', $id_partie);

	if ($stmt->execute() && $retour[0] = $stmt->fetch(PDO::FETCH_ASSOC))
	{
 		while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$retour[] = $resultat;
		}
		$stmt->closeCursor();
		return $retour;
	}



	return false;
}

function InfosBigTuto($id_big_tuto)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT
	big_tuto_id,
	big_tuto_avancement,
	big_tuto_titre,
	big_tuto_introduction,
	big_tuto_conclusion,
	big_tuto_id_sdz
	FROM zcov2_push_big_tutos
	WHERE big_tuto_id = :id_big_tuto");

	$stmt->bindParam(':id_big_tuto', $id_big_tuto);

	if ($stmt->execute() && $retour = $stmt->fetch(PDO::FETCH_ASSOC))
	{
 		$stmt->closeCursor();
		return $retour;
	}
	else
	{
		return false;
	}
}

function ListePartiesBigTuto($id_big_tuto)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT
	partie_id,
 	partie_titre,
 	partie_introduction,
 	partie_conclusion,
 	partie_id_sdz
	FROM zcov2_push_big_tutos_parties
	WHERE partie_id_big_tuto = :id_big_tuto
	ORDER BY partie_id ASC");

	$stmt->bindParam(':id_big_tuto', $id_big_tuto);

	if ($stmt->execute() && $retour[0] = $stmt->fetch(PDO::FETCH_ASSOC))
	{
 		while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$retour[] = $resultat;
		}
		$stmt->closeCursor();
		return $retour;
	}
	else
	{
		return false;
	}
}

function RecupererQCM($id_mini_tuto)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT
	question_id,
	question_label,
	question_explications
	FROM zcov2_push_qcm_questions
	WHERE question_id_mini_tuto = :id_mini_tuto
	ORDER BY question_id_sdz");

	$stmt->bindParam(':id_mini_tuto', $id_mini_tuto);

	if ($stmt->execute() && $retour[0] = $stmt->fetch(PDO::FETCH_ASSOC))
	{
 		while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$retour[] = $resultat;
		}
		$stmt->closeCursor();
		return $retour;
	}
	$erreur = $stmt->errorInfo();
	if (isset($erreur[0]))
	{
		return array();
	}
	else
	{
		return false;
	}
}

function RecupererReponses($id_qcm_question)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	$stmt = $dbh->prepare("SELECT
	reponse_id,
	reponse_texte,
	reponse_vrai
	FROM zcov2_push_qcm_reponses
	WHERE reponse_id_qcm_question = :id_qcm_question
	ORDER BY reponse_id ASC");

	$stmt->bindParam(':id_qcm_question', $id_qcm_question);

	if ($stmt->execute() && $retour[0] = $stmt->fetch(PDO::FETCH_ASSOC))
	{
 		while($resultat = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$retour[] = $resultat;
		}
		$stmt->closeCursor();
		return $retour;
	}



	return false;
}

?>
