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
 * Fichier pour l'export d'un tutoriel vers le Site du Zéro.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class ExporterSdzAction extends ZcorrectionActions
{
	public function execute()
	{
		return redirect('La liaison avec le SdZ est fermée. Désolé !', 'index.html', MSG_ERROR);
		zCorrecteurs::VerifierFormatageUrl(null, true);

		//On met le temps limite à une minute car les big-tutos peuvent-être
		//longs à importer.
		set_time_limit(60);

		$domaine = explode('.', $_SERVER['HTTP_HOST']);
		$ss_domaine = $domaine[0];
		$domaine_name = $domaine[1];
		$tld = $domaine[2];
		if(!in_array($ss_domaine, array('dev', 'www')) OR $domaine_name !== 'zcorrecteurs' OR ($tld !== 'fr' AND $tld !== 'net'))
		{
			exit('Erreur de sous-domaine, de domaine ou de TLD');
		}
		if($ss_domaine == 'www')
		{
			$rep = 'prod';
		}
		else
		{
			$ss_domaine = 'dev2';
			$rep = 'dev';
		}

		$dbh = Doctrine_Manager::connection()->getDbh();

		if(!verifier('zcorriger')) {
			// Seul les zCo/Admins peuvent exporter. :)
			return redirect(1, '/zcorrection/mes-tutoriels.html', MSG_ERROR);
		}
		else
		{
			if (!empty($_GET['id']))
			{
				if (!is_numeric($_GET['id']))
				{
					return redirect(138, '/zcorrection/mes-tutoriels.html', MSG_ERROR);
				}
				else
				{
					$sql = Doctrine_Manager::connection()->getDbh();

					$requete = $sql->query("SELECT
							soumission_token as token,
							soumission_recorrection,
							soumission_type_tuto,
							correction.correction_correcteur_invisible as correcteur_invisible,
							correction.correction_date_fin as correction_date_fin,
							correction.correction_commentaire as correction,
							correction.correction_commentaire_valido as correction_valido,
							correction.correction_id_tuto_corrige as correction_tuto_id,
							correction.correction_id AS correction_id,
							correcteur.utilisateur_id as id_correcteur,
							correcteur.utilisateur_pseudo as correcteur,
							recorrection.correction_correcteur_invisible as recorrecteur_invisible,
							recorrection.correction_date_fin as recorrection_date_fin,
							recorrection.correction_commentaire as recorrection,
							recorrection.correction_commentaire_valido as recorrection_valido,
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
					/*
					 s.soumission_type_tuto = {$type}
						AND (correction.correction_id_tuto_corrige = {$tuto_id}
							OR recorrection.correction_id_tuto_corrige = {$tuto_id})
					 */

					$infos = $requete->fetch(PDO::FETCH_ASSOC);
					$requete->closeCursor();

					if (false === $infos) {

						exit('<pre>'.print_r($sql->errorInfo(), true).'</pre>');
					}
					/*
					elseif($infos['soumission_recorrection'] AND empty($infos['recorrection_date_fin']))
					{
						exit('Erreur : la recorrection n\'est pas finie');
					}
					elseif(!$infos['soumission_recorrection'] AND empty($infos['correction_date_fin']))
					{
						exit('Erreur : la correction n\'est pas finie');
					}
					*/
					$_GET['type'] = ($infos['soumission_type_tuto'] == 2) ? 'big' : 'mini';
					if(empty($infos['recorrection_id']))
					{
						$tuto_id = $infos['correction_tuto_id'];
					}
					else
					{
						$tuto_id = $infos['recorrection_tuto_id'];
					}

					$xml = '<push>'."\n\n";
					$xml .= "\t".'<informations token="'.$infos['token'].'">'."\n";

					$xml .= "\t\t".'<correcteur pseudo="'.$infos['correcteur'].'" idzco="'.$infos['id_correcteur'].'" cacher="'.$infos['correcteur_invisible'].'">'."\n";
					$xml .= "\t\t\t\t".'<commentaireAuteur><![CDATA['.$infos['correction'].']]></commentaireAuteur>'."\n";
					$xml .= "\t\t\t\t".'<commentaireValidateur><![CDATA['.$infos['correction_valido'].']]></commentaireValidateur>'."\n";
					$xml .= "\t\t".'</correcteur>'."\n";

					$xml .= "\t\t".'<recorrecteur pseudo="'.$infos['recorrecteur'].'" idzco="'.$infos['id_recorrecteur'].'" cacher="'.$infos['recorrecteur_invisible'].'">'."\n";
					$xml .= "\t\t\t\t".'<commentaireAuteur><![CDATA['.$infos['recorrection'].']]></commentaireAuteur>'."\n";
					$xml .= "\t\t\t\t".'<commentaireValidateur><![CDATA['.$infos['recorrection_valido'].']]></commentaireValidateur>'."\n";
					$xml .= "\t\t".'</recorrecteur>'."\n";

					$xml .= "\t".'</informations>'."\n\n";

					//------------------
					//     BIG TUTO
					//------------------
					if ($_GET['type'] == 'big')
					{
						$big = $sql->query("SELECT big_tuto_titre, big_tuto_avancement, big_tuto_introduction, big_tuto_conclusion, big_tuto_id_sdz, big_tuto_difficulte
							FROM zcov2_push_big_tutos
							WHERE big_tuto_id = {$tuto_id}");

						list($titre, $avancement, $intro, $ccl, $id, $difficulte) = $big->fetch(PDO::FETCH_NUM);
						$big->closeCursor();

						if (!empty($id))
						{
							$xml .= '<bigtuto id="'.$id.'" generator="zCo Tutos v1.0">'."\n";
						}
						else
						{
							$xml .= '<bigtuto>'."\n";
						}
						$xml .= "\t<titre>\n\t\t<![CDATA[$titre]]>\n\t</titre>\n";
						$xml .= "\t<avancement>\n\t\t<![CDATA[$avancement]]>\n\t</avancement>\n";
						$xml .= "\t<difficulte>\n\t\t<![CDATA[$difficulte]]>\n\t</difficulte>\n";
						$xml .= "\t<introduction>\n\t<![CDATA[$intro]]>\n\t</introduction>\n";

						$stmt = $sql->prepare("SELECT partie_id, partie_titre, partie_introduction, partie_conclusion, partie_id_sdz
							FROM zcov2_push_big_tutos_parties
							WHERE partie_id_big_tuto = :id
							ORDER BY partie_id ASC");
						$stmt->bindParam(':id', $tuto_id);
						$stmt->execute();
						$xml .= "\t<parties>\n";
						$parties = $stmt->fetchAll();
						$stmt ->closeCursor();

						foreach($parties as $p)
						{
							if (!empty($p['partie_id_sdz'])) { $xml .="\t\t".'<partie id="'.$p['partie_id_sdz'].'">'."\n"; }
							else { $xml .= "\t\t<partie>\n"; }

							$xml .= "\t\t\t<titre>\n\t\t\t\t<![CDATA[{$p['partie_titre']}]]>\n\t\t\t</titre>\n";
							$xml .= "\t\t\t<introduction>\n\t\t\t<![CDATA[{$p['partie_introduction']}]]>\n\t\t\t</introduction>\n";

							$stmt = $sql->prepare("SELECT mini_tuto_id, mini_tuto_titre, mini_tuto_avancement, mini_tuto_introduction, mini_tuto_conclusion, mini_tuto_id_sdz, mini_tuto_difficulte
							FROM zcov2_push_mini_tutos
							WHERE mini_tuto_id_partie = :id
							ORDER BY mini_tuto_id ASC");
							$stmt->bindParam(':id', $p['partie_id']);
							$stmt->execute();
							$chapitres = $stmt->fetchAll();
							$stmt->closeCursor();

							$xml .= "\t\t\t<chapitres>\n";
							foreach($chapitres as $c)
							{
								if (!empty($c['mini_tuto_id_sdz'])) { $xml .= "\t\t\t\t".'<chapitre id="'.$c['mini_tuto_id_sdz'].'">'."\n"; }
								else { $xml .= "\t\t\t\t<chapitre>\n"; }

								$xml .="\t\t\t\t\t<titre>\n\t\t\t\t\t\t<![CDATA[{$c['mini_tuto_titre']}]]>\n\t\t\t\t\t</titre>\n";
								$xml .="\t\t\t\t\t<avancement>\n\t\t\t\t\t\t<![CDATA[{$c['mini_tuto_avancement']}]]>\n\t\t\t\t\t</avancement>\n";
								$xml .= "\t\t\t\t\t<difficulte>\n\t\t\t\t\t\t<![CDATA[{$c['mini_tuto_difficulte']}]]>\n\t\t\t\t\t</difficulte>\n";
								$xml .= "\t\t\t\t\t<introduction>\n\t\t\t\t\t<![CDATA[{$c['mini_tuto_introduction']}]]>\n\t\t\t\t\t</introduction>\n";

								$stmt = $dbh->prepare("SELECT sous_partie_id, sous_partie_titre, sous_partie_texte, sous_partie_id_sdz
								FROM zcov2_push_mini_tuto_sous_parties
								WHERE sous_partie_id_mini_tuto = :id
								ORDER BY sous_partie_id ASC");
								$stmt->bindParam(':id', $c['mini_tuto_id']);
								$stmt->execute();
								$sous_parties = $stmt->fetchAll();
								$stmt->closeCursor();

								$xml .= "\t\t\t\t\t<sousparties>\n";
								foreach($sous_parties as $s)
								{
									if (!empty($s['sous_partie_id_sdz'])) { $xml .= "\t\t\t\t\t\t".'<souspartie id="'.$s['sous_partie_id_sdz'].'">'."\n"; }
									else { $xml .= "\t\t\t\t\t\t<souspartie>\n"; }

									$xml .= "\t\t\t\t\t\t\t<titre>\n\t\t\t\t\t\t\t\t<![CDATA[{$s['sous_partie_titre']}]]>\n\t\t\t\t\t\t\t</titre>\n";
									$xml .= "\t\t\t\t\t\t\t<texte>\n\t\t\t\t\t\t\t\t<![CDATA[{$s['sous_partie_texte']}]]>\n\t\t\t\t\t\t\t</texte>\n";

									$xml .= "\t\t\t\t\t\t</souspartie>\n";

								}
								$xml .= "\t\t\t\t\t</sousparties>\n";

								// QCM
								$stmt = $sql->prepare("SELECT question_id, question_label, question_explications, question_id_sdz
								FROM zcov2_push_qcm_questions
								WHERE question_id_mini_tuto = :id
								ORDER BY question_id ASC");
								$stmt->bindParam(':id', $c['mini_tuto_id']);
								$stmt->execute();
								$questions = $stmt->fetchAll();
								$stmt->closeCursor();

								if (!empty($questions))
								{
									$xml .= "\t\t\t\t\t\t<qcm>\n";
									foreach($questions as $q)
									{
									$xml .= "\t\t\t\t\t\t\t<question";
									if (!empty($q['question_id_sdz'])) { $xml .= ' id="'.$q['question_id_sdz'].'"'; }
									$xml .= ">\n";
									$xml .= "\t\t\t\t\t\t\t\t<label>\n\t\t\t\t\t\t\t\t<![CDATA[{$q['question_label']}]]>\n\t\t\t\t\t\t\t\t</label>\n";

									$stmt = $sql->prepare("SELECT reponse_texte, reponse_vrai, reponse_id_sdz
									FROM zcov2_push_qcm_reponses
									WHERE reponse_id_qcm_question = :id");
									$stmt->bindParam(':id', $q['question_id']);
									$stmt->execute();
									$reponses = $stmt->fetchAll();
									$stmt->closeCursor();
									$xml .= "\t\t\t\t\t\t\t\t\t<reponses>\n";
									foreach($reponses as $r)
									{
										$xml .= "\t\t\t\t\t\t\t\t\t<reponse ";
										if ($r['reponse_vrai'] == 1) { $xml .= 'vrai="1"'; }
										else { $xml .= 'vrai="0"'; }
										if (!empty($r['reponse_id_sdz'])) { $xml .= ' id="'.$r['reponse_id_sdz'].'"'; }
										$xml .= ">\n\t\t\t\t\t\t\t\t\t<![CDATA[{$r['reponse_texte']}]]>\n\t\t\t\t\t\t\t\t\t</reponse>\n";
									}
									$xml .= "\t\t\t\t\t\t\t\t\t</reponses>\n";
									$xml .= "\t\t\t\t\t\t\t\t<explication>\n\t\t\t\t\t\t\t\t<![CDATA[{$q['question_explications']}]]>\n\t\t\t\t\t\t\t\t</explication>\n";
									$xml .= "\t\t\t\t\t\t\t</question>\n";
									}
									$xml .= "\t\t\t\t\t\t</qcm>\n";
								}

								$xml .= "\t\t\t\t\t<conclusion>\n\t\t\t\t\t<![CDATA[{$c['mini_tuto_conclusion']}]]>\n\t\t\t\t\t</conclusion>\n";
								$xml .= "\t\t\t\t</chapitre>\n";
							}
							$xml .= "\t\t\t</chapitres>\n";

							$xml .= "\t\t\t<conclusion>\n\t\t\t<![CDATA[{$p['partie_conclusion']}]]>\n\t\t\t</conclusion>\n";

							$xml .= "\t\t</partie>\n";
						}
						$xml .= "\t</parties>\n";

						$xml .= "\t<conclusion>\n\t<![CDATA[$ccl]]>\n\t</conclusion>\n";
						$xml .= '</bigtuto>'."\n";
					}
					//------------------
					//    MINI TUTO
					//------------------
					else if ($_GET['type'] == 'mini')
					{
						$mini = $sql->query("SELECT mini_tuto_titre, mini_tuto_avancement, mini_tuto_introduction, mini_tuto_conclusion, mini_tuto_id_sdz, mini_tuto_difficulte
						FROM zcov2_push_mini_tutos
						WHERE mini_tuto_id = {$tuto_id}");

						list($titre, $avancement, $intro, $ccl, $id, $difficulte) = $mini->fetch(PDO::FETCH_NUM);
						$mini->closeCursor();

						if (!empty($id))
						{
							$xml .= '<minituto id="'.$id.'" generator="zCo Tutos v1.0">'."\n";
						}
						else
						{
							$xml .= '<minituto>'."\n";
						}

						$xml .= "\t<titre>\n\t\t<![CDATA[$titre]]>\n\t</titre>\n";
						$xml .= "\t<avancement>\n\t\t<![CDATA[$avancement]]>\n\t</avancement>\n";
						$xml .= "\t<difficulte>\n\t\t<![CDATA[$difficulte]]>\n\t</difficulte>\n";
						$xml .= "\t<introduction>\n\t<![CDATA[$intro]]>\n\t</introduction>\n";

						$sous_parties = $sql->query("SELECT sous_partie_id, sous_partie_titre, sous_partie_texte, sous_partie_id_sdz
						FROM zcov2_push_mini_tuto_sous_parties
						WHERE sous_partie_id_mini_tuto = {$tuto_id}
						ORDER BY sous_partie_id ASC");
						$xml .= "\t<sousparties>\n";
						foreach($sous_parties as $s)
						{
							if (!empty($s['sous_partie_id_sdz'])) { $xml .= "\t\t".'<souspartie id="'.$s['sous_partie_id_sdz'].'">'."\n"; }
							else { $xml .= "\t\t<souspartie>\n"; }

							$xml .= "\t\t\t<titre>\n\t\t\t\t<![CDATA[{$s['sous_partie_titre']}]]>\n\t\t\t</titre>\n";
							$xml .= "\t\t\t<texte>\n\t\t\t\t<![CDATA[{$s['sous_partie_texte']}]]>\n\t\t\t</texte>\n";
							$xml .= "\t\t</souspartie>\n";

						}
						$sous_parties->closeCursor();
						$xml .= "\t</sousparties>\n";

						// QCM
						$stmt = $sql->prepare("SELECT question_id, question_label, question_explications, question_id_sdz
						FROM zcov2_push_qcm_questions
						WHERE question_id_mini_tuto = :id
						ORDER BY question_id ASC");
						$stmt->bindParam(':id', $tuto_id);
						$stmt->execute();
						$questions = $stmt->fetchAll();
						$stmt->closeCursor();

						if (!empty($questions))
						{
							$xml .= "\t\t<qcm>\n";
							foreach($questions as $q)
							{
							$xml .= "\t\t\t<question";
							if (!empty($q['question_id_sdz'])) { $xml .= ' id="'.$q['question_id_sdz'].'"'; }
							$xml .= ">\n";
							$xml .= "\t\t\t\t<label>\n\t\t\t\t<![CDATA[{$q['question_label']}]]>\n\t\t\t\t</label>\n";
							$reponses = $sql->query("SELECT reponse_texte, reponse_vrai, reponse_id_sdz
							FROM zcov2_push_qcm_reponses
							WHERE reponse_id_qcm_question = {$q['question_id']}");
							$xml .= "\t\t\t\t\t<reponses>\n";
							foreach($reponses as $r)
							{
								$xml .= "\t\t\t\t\t<reponse ";
								if ($r['reponse_vrai'] == 1) { $xml .= 'vrai="1"'; }
								else { $xml .= 'vrai="0"'; }
								if (!empty($r['reponse_id_sdz'])) { $xml .= ' id="'.$r['reponse_id_sdz'].'"'; }
								$xml .= ">\n\t\t\t\t\t<![CDATA[{$r['reponse_texte']}]]>\n\t\t\t\t\t</reponse>\n";
							}
							$xml .= "\t\t\t\t\t</reponses>\n";
							$xml .= "\t\t\t\t<explication>\n\t\t\t\t<![CDATA[{$q['question_explications']}]]>\n\t\t\t\t</explication>\n";
							$xml .= "\t\t\t</question>\n";
							}
							$xml .= "\t\t\t</qcm>\n";
						}

						$xml .= "\t<conclusion>\n\t<![CDATA[$ccl]]>\n\t</conclusion>\n";
						$xml .= "</minituto>\n";
					}

					$xml .= "</push>\n";

					$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".$xml;

					//Cryptage du XML
					$key = $this->container->getParameter('zco_zcorrection.mcrypt_key');
					$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
					$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
					$xml = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $xml, MCRYPT_MODE_ECB, $iv);

					//Enregistrement du XML
					file_put_contents(BASEPATH.'/web/tutos/xml_sdz/'.$infos['token'].'.xml', $xml);

					include BASEPATH.'/src/Zco/Bundle/ZcorrectionBundle/modeles/send_http_request.php';
					$uri = '/get_tut_zco.php';
					$server = $ss_domaine.'.siteduzero.com';
					$port = 80;
					$post_results = httpPost($server,$port,$uri,'token='.$infos['token']);
					if(is_string($post_results) AND $post_results == 'OK')
					{
						//Suppression du fichier XML
						unlink(BASEPATH.'/web/tutos/xml_sdz/'.$infos['token'].'.xml');

						//Réglage du tutoriel comme zCorrigé
						$correctionTerminee = 2;
						$infos['recorrection_id'] ? TerminerCorrection($infos['recorrection_id'], $correctionTerminee, 0) : TerminerCorrection($infos['correction_id'], $correctionTerminee, 0);
						return redirect(307, '/zcorrection/');
					}
					else
					{
						echo $post_results;
						return redirect(308, '/zcorrection/terminer-'.$_GET['id'].'.html', MSG_ERROR);
					}
				}
			}
		}
	}
}
