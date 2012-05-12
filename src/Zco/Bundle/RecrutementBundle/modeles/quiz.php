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
 * Gestion des quiz pour le recrutement.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
function DebutQuiz($candidature)
{
	if (!is_array($candidature))
		$candidature = array('candidature_id' => $candidature);
	if (!isset($candidature['candidature_id']))
		return;

	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare('UPDATE zcov2_recrutements_candidatures'
		.' SET candidature_quiz_debut = CURRENT_TIMESTAMP'
		.' WHERE candidature_id = ?'
		.' AND candidature_quiz_debut IS NULL');
	$stmt->execute(array($candidature['candidature_id']));
}

function FinQuiz($candidature)
{
	if (!is_array($candidature))
		$candidature = array('candidature_id' => $candidature);
	if (!isset($candidature['candidature_id']))
		return;

	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare('UPDATE zcov2_recrutements_candidatures'
		.' SET candidature_quiz_fin = CURRENT_TIMESTAMP'
		.' WHERE candidature_id = ?'
		.' AND candidature_quiz_debut IS NOT NULL'
		.' AND candidature_quiz_fin IS NULL');
	$stmt->execute(array($candidature['candidature_id']));
}

function EnregistrerReponses($recrutement, $reponses, $commentaires)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	$stmt = $dbh->prepare('INSERT INTO zcov2_recrutements_quiz_reponses'
		.' (utilisateur_id, recrutement_id, question_id, reponse_id, justification)'
		.' VALUES ('.$_SESSION['id'].', '.(int)$recrutement.', ?, ?, ?)');
	foreach ($reponses as $question => $rep)
	{
		$question = (int) $question;
		$justification = isset($commentaires[$question])
			? trim($commentaires[$question])
			: NULL;
		$stmt->execute(array($question, $rep, $justification));
	}
}

function RecupererReponses($recrutement, $utilisateur)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	// Id du quiz associé au recrutement
	if (ctype_digit($recrutement))
	{
		$stmt = $dbh->prepare('SELECT recrutement_id_quiz FROM zcov2_recrutements WHERE recrutement_id = ?');
		$stmt->execute(array($recrutement));
		$recrutement = array('recrutement_id' => $recrutement, 'recrutement_quiz_id' => $stmt->fetchCOlumn());
	}
	if ($recrutement['recrutement_quiz_id'] === NULL || $recrutement['recrutement_quiz_id'] === false)
		return null;

	// Réponses du candidat
	$stmt = $dbh->prepare('SELECT question_id, reponse_id, justification'
		.' FROM zcov2_recrutements_quiz_reponses'
		.' WHERE utilisateur_id = ? AND recrutement_id = ?');
	$stmt->execute(array($utilisateur, $recrutement['recrutement_id']));
	$reponses = array();
	foreach ($stmt->fetchAll() as $rep)
		$reponses[$rep['question_id']] = $rep;

	// Liste des questions du quiz, avec la bonne réponse
	$quiz = Doctrine_Core::getTable('Quiz')->find($recrutement['recrutement_quiz_id']);
	if ($quiz === false)
		return null;

	// Tout dans le même tableau
	foreach ($quiz->Questions(array_keys($reponses)) as $question)
		foreach (array('question', 'reponse1', 'reponse2', 'reponse3',
		               'reponse4', 'reponse_juste') as $rep)
			$reponses[$question['id']][$rep] = $question[$rep];
	return $reponses;
}
