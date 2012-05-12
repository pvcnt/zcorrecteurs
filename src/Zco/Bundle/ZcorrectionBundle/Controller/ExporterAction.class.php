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
 * Fichier pour l'export d'un tutoriel au format .tuto.
 *
 * @author Savageman, vincent1870
 */
class ExporterAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		$dbh = Doctrine_Manager::connection()->getDbh();

		if (!empty($_GET['id']))
		{
			if (!is_numeric($_GET['id']))
			{
				return redirect(138, '/zcorrection/', MSG_ERROR);
			}
			else
			{
				$requete = $dbh->query("SELECT
						soumission_token as token,
						soumission_recorrection,
						soumission_type_tuto,
						correction.correction_correcteur_invisible as correcteur_invisible,
						correction.correction_date_fin as correction_date_fin,
						correction.correction_commentaire as correction,
						correction.correction_id_tuto_corrige as correction_tuto_id,
						correction.correction_id AS correction_id,
						correcteur.utilisateur_id as id_correcteur,
						correcteur.utilisateur_pseudo as correcteur,
						recorrection.correction_correcteur_invisible as recorrecteur_invisible,
						recorrection.correction_date_fin as recorrection_date_fin,
						recorrection.correction_commentaire as recorrection,
						recorrection.correction_id_tuto_corrige as recorrection_tuto_id,
						recorrection.correction_id AS recorrection_id,
						recorrecteur.utilisateur_id as id_recorrecteur,
						recorrecteur.utilisateur_pseudo as recorrecteur
					FROM zcov2_push_soumissions s
					LEFT JOIN zcov2_push_corrections correction ON s.soumission_id_correction_1 = correction.correction_id
					LEFT JOIN zcov2_push_corrections recorrection ON s.soumission_id_correction_2 = recorrection.correction_id
					LEFT JOIN zcov2_utilisateurs correcteur ON correction.correction_id_correcteur = correcteur.utilisateur_id
					LEFT JOIN zcov2_utilisateurs recorrecteur ON recorrection.correction_id_correcteur = recorrecteur.utilisateur_id
					WHERE s.soumission_id = {$_GET['id']}");
				$infos = $requete->fetch(PDO::FETCH_ASSOC);
				$requete->closeCursor();

				if (false === $infos)
					exit('<pre>'.print_r($dbh->errorInfo(), true).'</pre>');

				$_GET['type'] = ($infos['soumission_type_tuto'] == 2) ? 'big' : 'mini';
				if (isset($_GET['tuto_id']))
				{
					$tuto_id = (int) $_GET['tuto_id'];
				}
				else if(empty($infos['recorrection_id']))
				{
					$tuto_id = $infos['correction_tuto_id'];
				}
				else
				{
					$tuto_id = $infos['recorrection_tuto_id'];
				}

				//Création du document XML + en-tête
				$xml = new DomDocument();
				$xml->formatOutput = true;

				//------------------
				//     BIG TUTO
				//------------------
				if ($_GET['type'] == 'big')
				{
					$big = $dbh->query("SELECT big_tuto_titre, big_tuto_avancement, big_tuto_introduction, big_tuto_conclusion, big_tuto_id_sdz, big_tuto_difficulte
						FROM zcov2_push_big_tutos
						WHERE big_tuto_id = {$tuto_id}");

					list($_titre, $_avancement, $_intro, $_ccl, $id, $_difficulte) = $big->fetch(PDO::FETCH_NUM);
					$big->closeCursor();


					$bigtuto = $xml->createElement('bigtuto');
					if (!empty($id))
					{
						$bigtuto->setAttribute('id', $id);
					}
					$bigtuto->setAttribute('generator', 'zCorrection v2.0');
					$bigtuto->setAttribute('language', 'zCode');
					$bigtuto = $xml->appendChild($bigtuto);

					$titre = $xml->createElement('titre');
					$titre = $bigtuto->appendChild($titre);
					$titre_text = $xml->createCDATASection($_titre);
					$titre_text = $titre->appendChild($titre_text);

					$avancement = $xml->createElement('avancement');
					$avancement = $bigtuto->appendChild($avancement);
					$avancement_text = $xml->createCDATASection($_avancement);
					$avancement_text = $avancement->appendChild($avancement_text);


					$licence = $xml->createElement('licence');
					$licence = $bigtuto->appendChild($licence);
					$licence_text = $xml->createCDATASection('1');
					$licence_text = $licence->appendChild($licence_text);

					$difficulte = $xml->createElement('difficulte');
					$difficulte = $bigtuto->appendChild($difficulte);
					$difficulte_text = $xml->createCDATASection($_difficulte);
					$difficulte_text = $difficulte->appendChild($difficulte_text);

					$introduction = $xml->createElement('introduction');
					$introduction = $bigtuto->appendChild($introduction);
					$introduction_text = $xml->createCDATASection($_intro);
					$introduction_text = $introduction->appendChild($introduction_text);

					$stmt = $dbh->prepare("SELECT partie_id, partie_titre, partie_introduction, partie_conclusion, partie_id_sdz
						FROM zcov2_push_big_tutos_parties
						WHERE partie_id_big_tuto = :id
						ORDER BY partie_id ASC");
					$stmt->bindParam(':id', $tuto_id);
					$stmt->execute();
					$_parties = $stmt->fetchAll();
					$stmt ->closeCursor();

					$parties = $xml->createElement('parties');
					$parties = $bigtuto->appendChild($parties);

					foreach($_parties as $p)
					{
						$partie = $xml->createElement('partie');
						if (!empty($p['partie_id_sdz']))
							$partie->setAttribute('id', $p['partie_id_sdz']);
						$partie = $parties->appendChild($partie);

						$titre = $xml->createElement('titre');
						$titre = $partie->appendChild($titre);
						$titre_text = $xml->createCDATASection($p['partie_titre']);
						$titre_text = $titre->appendChild($titre_text);

						$avancement = $xml->createElement('avancement');
						$avancement = $partie->appendChild($avancement);
						$avancement_text = $xml->createCDATASection('0');
						$avancement_text = $avancement->appendChild($avancement_text);

						$difficulte = $xml->createElement('difficulte');
						$difficulte = $partie->appendChild($difficulte);
						$difficulte_text = $xml->createCDATASection('1');
						$difficulte_text = $difficulte->appendChild($difficulte_text);

						$introduction = $xml->createElement('introduction');
						$introduction = $partie->appendChild($introduction);
						$introduction_text = $xml->createCDATASection($p['partie_introduction']);
						$introduction_text = $introduction->appendChild($introduction_text);

						$stmt = $dbh->prepare("SELECT mini_tuto_id, mini_tuto_titre, mini_tuto_avancement, mini_tuto_introduction, mini_tuto_conclusion, mini_tuto_id_sdz, mini_tuto_difficulte
						FROM zcov2_push_mini_tutos
						WHERE mini_tuto_id_partie = :id
						ORDER BY mini_tuto_id ASC");
						$stmt->bindParam(':id', $p['partie_id']);
						$stmt->execute();
						$_chapitres = $stmt->fetchAll();
						$stmt->closeCursor();

						$chapitres = $xml->createElement('chapitres');
						$chapitres = $partie->appendChild($chapitres);

						foreach($_chapitres as $c)
						{
							$chapitre = $xml->createElement('chapitre');
							if (!empty($c['mini_tuto_id_sdz']))
								$chapitre->setAttribute('id', $c['mini_tuto_id_sdz']);
							$chapitre = $chapitres->appendChild($chapitre);

							$titre = $xml->createElement('titre');
							$titre = $chapitre->appendChild($titre);
							$titre_text = $xml->createCDATASection($c['mini_tuto_titre']);
							$titre_text = $titre->appendChild($titre_text);

							$avancement = $xml->createElement('avancement');
							$avancement = $chapitre->appendChild($avancement);
							$avancement_text = $xml->createCDATASection($c['mini_tuto_avancement']);
							$avancement_text = $avancement->appendChild($avancement_text);

							$difficulte = $xml->createElement('difficulte');
							$difficulte = $chapitre->appendChild($difficulte);
							$difficulte_text = $xml->createCDATASection($c['mini_tuto_difficulte']);
							$difficulte_text = $difficulte->appendChild($difficulte_text);

							$introduction = $xml->createElement('introduction');
							$introduction = $chapitre->appendChild($introduction);
							$introduction_text = $xml->createCDATASection($c['mini_tuto_introduction']);
							$introduction_text = $introduction->appendChild($introduction_text);

							$stmt = $dbh->prepare("SELECT sous_partie_id, sous_partie_titre, sous_partie_texte, sous_partie_id_sdz
							FROM zcov2_push_mini_tuto_sous_parties
							WHERE sous_partie_id_mini_tuto = :id
							ORDER BY sous_partie_id ASC");
							$stmt->bindParam(':id', $c['mini_tuto_id']);
							$stmt->execute();
							$_sous_parties = $stmt->fetchAll();
							$stmt->closeCursor();

							$sousparties = $xml->createElement('sousparties');
							$sousparties = $chapitre->appendChild($sousparties);

							foreach($_sous_parties as $s)
							{
								$souspartie = $xml->createElement('souspartie');
								if(!empty($s['sous_partie_id_sdz']))
									$souspartie->setAttribute('id', $s['sous_partie_id_sdz']);
								$souspartie = $sousparties->appendChild($souspartie);

								$titre = $xml->createElement('titre');
								$titre = $souspartie->appendChild($titre);
								$titre_text = $xml->createCDATASection($s['sous_partie_titre']);
								$titre_text = $titre->appendChild($titre_text);

								$texte = $xml->createElement('texte');
								$texte = $souspartie->appendChild($texte);
								$texte_text = $xml->createCDATASection($s['sous_partie_texte']);
								$texte_text = $texte->appendChild($texte_text);
							}

							// QCM
							$stmt = $dbh->prepare("SELECT question_id, question_label, question_explications, question_id_sdz
							FROM zcov2_push_qcm_questions
							WHERE question_id_mini_tuto = :id
							ORDER BY question_id ASC");
							$stmt->bindParam(':id', $c['mini_tuto_id']);
							$stmt->execute();
							$_questions = $stmt->fetchAll();
							$stmt->closeCursor();

							if (!empty($_questions))
							{
								$qcm = $xml->createElement('qcm');
								$qcm = $chapitre->appendChild($qcm);

								foreach($_questions as $q)
								{
									$question = $xml->createElement('question');
									if(!empty($q['question_id_sdz']))
										$question->setAttribute('id', $q['question_id_sdz']);
									$question = $qcm->appendChild($question);

									$label = $xml->createElement('label');
									$label = $question->appendChild($label);
									$label_text = $xml->createCDATASection($q['question_label']);
									$label_text = $label->appendChild($label_text);

									$stmt = $dbh->prepare("SELECT reponse_texte, reponse_vrai, reponse_id_sdz
									FROM zcov2_push_qcm_reponses
									WHERE reponse_id_qcm_question = :id");
									$stmt->bindParam(':id', $q['question_id']);
									$stmt->execute();
									$_reponses = $stmt->fetchAll();
									$stmt->closeCursor();

									$reponses = $xml->createElement('reponses');
									$reponses = $qcm->appendChild($reponses);

									foreach($_reponses as $r)
									{
										$reponse = $xml->createElement('reponse');
										if(!empty($r['reponse_id_sdz']))
											$reponse->setAttribute('id', $r['reponse_id_sdz']);
										$reponse->setAttribute('vrai', $r['reponse_vrai'] == 1 ? 1 : 0);
										$reponse = $reponses->appendChild($reponse);

										$reponse_text = $xml->createCDATASection($r['reponse_texte']);
										$reponse_text = $reponse->appendChild($reponse_text);
									}

									$explication = $xml->createElement('label');
									$explication = $question->appendChild($label);
									$explication_text = $xml->createCDATASection($q['question_explications']);
									$explication_text = $explication->appendChild($explication_text);
								}
							}

							$conclusion = $xml->createElement('conclusion');
							$conclusion = $chapitre->appendChild($conclusion);
							$conclusion_text = $xml->createCDATASection($c['mini_tuto_conclusion']);
							$conclusion_text = $conclusion->appendChild($conclusion_text);
						}

						$conclusion = $xml->createElement('conclusion');
						$conclusion = $partie->appendChild($conclusion);
						$conclusion_text = $xml->createCDATASection($p['partie_conclusion']);
						$conclusion_text = $conclusion->appendChild($conclusion_text);

					}

					$conclusion = $xml->createElement('conclusion');
					$conclusion = $bigtuto->appendChild($conclusion);
					$conclusion_text = $xml->createCDATASection($_ccl);
					$conclusion_text = $conclusion->appendChild($conclusion_text);
				}
				//------------------
				//    MINI TUTO
				//------------------
				else if ($_GET['type'] == 'mini')
				{
					$mini = $dbh->query("SELECT mini_tuto_titre, mini_tuto_avancement, mini_tuto_introduction, mini_tuto_conclusion, mini_tuto_id_sdz, mini_tuto_difficulte
					FROM zcov2_push_mini_tutos
					WHERE mini_tuto_id = {$tuto_id}");
					list($_titre, $_avancement, $_intro, $_ccl, $_id, $_difficulte) = $mini->fetch(PDO::FETCH_NUM);
					$mini->closeCursor();

					$minituto = $xml->createElement('minituto');
					if (!empty($_id))
						$minituto->setAttribute('id', $_id);
					$minituto->setAttribute('generator', 'zCorrection v2.0');
					$minituto->setAttribute('language', 'zCode');
					$minituto = $xml->appendChild($minituto);

					$titre = $xml->createElement('titre');
					$titre = $minituto->appendChild($titre);
					$titre_text = $xml->createCDATASection($_titre);
					$titre_text = $titre->appendChild($titre_text);

					$avancement = $xml->createElement('avancement');
					$avancement = $minituto->appendChild($avancement);
					$avancement_text = $xml->createCDATASection($_avancement);
					$avancement_text = $avancement->appendChild($avancement_text);

					$licence = $xml->createElement('licence');
					$licence = $minituto->appendChild($licence);
					$licence_text = $xml->createCDATASection('1');
					$licence_text = $licence->appendChild($licence_text);

					$difficulte = $xml->createElement('difficulte');
					$difficulte = $minituto->appendChild($difficulte);
					$difficulte_text = $xml->createCDATASection($_difficulte);
					$difficulte_text = $difficulte->appendChild($difficulte_text);

					$introduction = $xml->createElement('introduction');
					$introduction = $minituto->appendChild($introduction);
					$introduction_text = $xml->createCDATASection($_intro);
					$introduction_text = $introduction->appendChild($introduction_text);

					$stmt = $dbh->prepare("SELECT sous_partie_id, sous_partie_titre, sous_partie_texte, sous_partie_id_sdz
					FROM zcov2_push_mini_tuto_sous_parties
					WHERE sous_partie_id_mini_tuto = :id
					ORDER BY sous_partie_id ASC");
					$stmt->bindParam(':id', $tuto_id);
					$stmt->execute();
					$_sous_parties = $stmt->fetchAll();
					$stmt->closeCursor();

					$sousparties = $xml->createElement('sousparties');
					$sousparties = $minituto->appendChild($sousparties);

					foreach($_sous_parties as $s)
					{
						$souspartie = $xml->createElement('souspartie');
						if(!empty($s['sous_partie_id_sdz']))
							$souspartie->setAttribute('id', $s['sous_partie_id_sdz']);
						$souspartie = $sousparties->appendChild($souspartie);

						$titre = $xml->createElement('titre');
						$titre = $souspartie->appendChild($titre);
						$titre_text = $xml->createCDATASection($s['sous_partie_titre']);
						$titre_text = $titre->appendChild($titre_text);

						$texte = $xml->createElement('texte');
						$texte = $souspartie->appendChild($texte);
						$texte_text = $xml->createCDATASection($s['sous_partie_texte']);
						$texte_text = $texte->appendChild($texte_text);
					}

					// QCM
					$stmt = $dbh->prepare("SELECT question_id, question_label, question_explications, question_id_sdz
					FROM zcov2_push_qcm_questions
					WHERE question_id_mini_tuto = :id
					ORDER BY question_id ASC");
					$stmt->bindParam(':id', $tuto_id);
					$stmt->execute();
					$_questions = $stmt->fetchAll();
					$stmt->closeCursor();

					if (!empty($_questions))
					{
						$qcm = $xml->createElement('qcm');
						$qcm = $minituto->appendChild($qcm);

						foreach($_questions as $q)
						{
							$question = $xml->createElement('question');
							if(!empty($q['question_id_sdz']))
								$question->setAttribute('id', $q['question_id_sdz']);
							$question = $qcm->appendChild($question);

							$label = $xml->createElement('label');
							$label = $question->appendChild($label);
							$label_text = $xml->createCDATASection($q['question_label']);
							$label_text = $label->appendChild($label_text);

							$stmt = $dbh->prepare("SELECT reponse_texte, reponse_vrai, reponse_id_sdz
							FROM zcov2_push_qcm_reponses
							WHERE reponse_id_qcm_question = :id");
							$stmt->bindParam(':id', $q['question_id']);
							$stmt->execute();
							$_reponses = $stmt->fetchAll();
							$stmt->closeCursor();

							$reponses = $xml->createElement('reponses');
							$reponses = $question->appendChild($reponses);

							foreach($_reponses as $r)
							{
								$reponse = $xml->createElement('reponse');
								if(!empty($r['reponse_id_sdz']))
									$reponse->setAttribute('id', $r['reponse_id_sdz']);
								$reponse->setAttribute('vrai', $r['reponse_vrai'] == 1 ? 1 : 0);
								$reponse = $reponses->appendChild($reponse);

								$reponse_text = $xml->createCDATASection($r['reponse_texte']);
								$reponse_text = $reponse->appendChild($reponse_text);
							}

							$explication = $xml->createElement('explication');
							$explication = $question->appendChild($explication);
							$explication_text = $xml->createCDATASection($q['question_explications']);
							$explication_text = $explication->appendChild($explication_text);
						}
					}

					$conclusion = $xml->createElement('conclusion');
					$conclusion = $minituto->appendChild($conclusion);
					$conclusion_text = $xml->createCDATASection($_ccl);
					$conclusion_text = $conclusion->appendChild($conclusion_text);
				}
			}
		}

		$response = new Symfony\Component\HttpFoundation\Response($xml->saveXML());
		$response->headers->set('Content-type', 'text/xml');
		return $response;
	}
}
