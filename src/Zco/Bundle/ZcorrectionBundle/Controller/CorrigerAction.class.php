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
 * Contrôleur pour la correction d'un tutoriel.
 *
 * @author Savageman, vincent1870, DJ Fox
 */
class CorrigerAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true, true);
		Page::$titre .= ' - Corriger un tutoriel';

		//Si aucun tuto n'a été envoyé
		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(138, '/zcorrection/', MSG_ERROR);
		}
		else
		{
		    //On récupère des infos sur la correction
			$s = InfosCorrection($_GET['id']);
			$zco_mark = $s['recorrection_id'] ? $s['recorrection_marque'] : $s['correction_marque'];
			
			$this->get('zco_vitesse.resource_manager')->requireResources(array(
			    '@ZcoZcorrectionBundle/Resources/public/js/tutoriel.js',
			    '@ZcoZcorrectionBundle/Resources/public/js/mozInnerHTML.js',
			    '@ZcoCoreBundle/Resources/public/js/zform.js',
			    //'_ajax_data[\'zco_mark\'] = '.json_encode($zco_mark).';'
			    //     => array('type' => 'inline'),
			    '@ZcoCoreBundle/Resources/public/css/zcode.css',
    		    '@ZcoZcorrectionBundle/Resources/public/css/correction.css',
			));

			//Si le tutoriel n'existe pas
			if (empty($s))
				return redirect(139, '/zcorrection/', MSG_ERROR);

			//Si on n'a pas le droit de corriger ce tutoriel
			else if(((!$s['soumission_recorrection'] && $s['id_correcteur'] != $_SESSION['id']) || ($s['soumission_recorrection'] && $s['id_recorrecteur'] != $_SESSION['id'])) && !verifier('zcorrection_editer_tutos'))
			{
				return redirect(139, '/zcorrection/', MSG_ERROR);
			}
			else
			{
				$is_proprio = $s['recorrection_id'] ? ($_SESSION['id'] == $s['id_recorrecteur']) : ($_SESSION['id'] == $s['id_correcteur']);
				fil_ariane(array(
					$s['soumission_type_tuto'] == MINI_TUTO ? htmlspecialchars($s['mini_tuto_titre']) : htmlspecialchars($s['big_tuto_titre']) => 'fiche-tuto-'.$_GET['id'].'.html',
					'Corriger le tutoriel'
				));

				//Si c'est un mini-tuto
				if (MINI_TUTO == $s['soumission_type_tuto'])
				{
					if (!empty($s['id_tuto_recorrection']))
					{
						$parties   = RecupererMiniTuto($s['id_tuto_recorrection']);
						$InfosTuto = InfosMiniTuto($s['id_tuto_recorrection']);
						$qcm       = RecupererQCM($s['id_tuto_recorrection']);
					}
					else
					{
						$parties   = RecupererMiniTuto($s['id_tuto_correction']);
						$InfosTuto = InfosMiniTuto($s['id_tuto_correction']);
						$qcm       = RecupererQCM($s['id_tuto_correction']);
					}
					foreach($qcm as $k => $q)
					{
						$qcm[$k]['reponses'] = RecupererReponses($q['question_id']);
					}

					if(empty($s['correction_date_debut']) && $is_proprio)
					{
						CommencerCorrection($s['correction_id']);
					}
					else if(!empty($s['correction_abandonee']) && $is_proprio)
					{
						ReprendreCorrection($s['correction_id']);
					}
					else if(empty($s['recorrection_date_debut']) && $is_proprio)
					{
						CommencerCorrection($s['recorrection_id']);
					}
					else if(!empty($s['recorrection_abandonee']) && $is_proprio)
					{
						ReprendreCorrection($s['recorrection_id']);
					}

					return render_to_response('ZcoZcorrectionBundle::correctionMiniTuto.html.php', array(
						's' => $s,
						'parties' => $parties,
						'InfosTuto' => $InfosTuto,
						'qcm' => $qcm,
						'zco_mark' => $zco_mark
					));
				}

				//Si c'est un big-tuto
				else if (BIG_TUTO == $s['soumission_type_tuto'])
				{
					$is_proprio = $s['recorrection_id'] ? ($_SESSION['id'] == $s['id_recorrecteur']) : ($_SESSION['id'] == $s['id_correcteur']);

					if(empty($s['correction_date_debut']) && $is_proprio)
					{
						CommencerCorrection($s['correction_id']);
					}
					else if(!empty($s['correction_abandonee']) && $is_proprio)
					{
						ReprendreCorrection($s['correction_id']);
					}
					else if(empty($s['recorrection_date_debut']) && $is_proprio)
					{
						CommencerCorrection($s['recorrection_id']);
					}
					else if(!empty($s['recorrection_abandonee']) && $is_proprio)
					{
						ReprendreCorrection($s['recorrection_id']);
					}

					//Si on veut corriger un mini-tuto
					if (!empty($_GET['id2']) && is_numeric($_GET['id2']))
					{
						//Infos sur la correction à faire
						$parties = RecupererMiniTuto($_GET['id2']);
						$InfosTuto = InfosMiniTuto($_GET['id2']);
						$qcm       = RecupererQCM($_GET['id2']);
						foreach($qcm as $k => $q)
						{
							$qcm[$k]['reponses'] = RecupererReponses($q['question_id']);
						}

						//Infos sur le big-tuto
						if (!empty($s['id_tuto_recorrection']))
						{
							$InfosBigTuto = InfosBigTuto($s['id_tuto_recorrection']);
							$ListeParties = ListePartiesBigTuto($s['id_tuto_recorrection']);
						}
						else
						{
							$InfosBigTuto = InfosBigTuto($s['id_tuto_correction']);
							$ListeParties = ListePartiesBigTuto($s['id_tuto_correction']);
						}

						$ListeMiniTutos = array();

						foreach($ListeParties as $v)
						{
							$ListeMiniTutos[$v['partie_id']] = ListeTutosPartie($v['partie_id']);
						}

						//Génération du saut rapide
						$nb = 0;
						$SautRapide = '
						<div class="flot_droite">
							<form method="get" action="">
								Saut rapide : <select id="id2" name="id2" onchange="document.location=\'/zcorrection/corriger-'.$_GET['id'].'-\' + this.value + \'.html\';">
									<option value="0">'.htmlspecialchars($InfosBigTuto['big_tuto_titre']).'</option>';
						foreach($ListeParties as $v)
						{
							if($nb != 0)
								$SautRapide .= '</optgroup>';
							$SautRapide .= '<optgroup label="'.htmlspecialchars($v['partie_titre']).'">';
							if($ListeMiniTutos[$v['partie_id']])
							{
								foreach($ListeMiniTutos[$v['partie_id']] as $t)
									$SautRapide .= '<option value="'.$t['mini_tuto_id'].'"'.($t['mini_tuto_id'] == $_GET['id2'] ? ' selected="selected"' : '').'>'.str_replace('&amp;euro;', '&euro;', htmlspecialchars($t['mini_tuto_titre'])).'</a></li>';
							}
							$nb ++;
						}
						$SautRapide .= '
								</select>
								<input type="submit" value="Aller" />
							</form>
						</div>';

						//Inclusion de la vue
						return render_to_response('ZcoZcorrectionBundle::correctionMiniTuto2.php', array(
							's' => $s,
							'parties' => $parties,
							'InfosTuto' => $InfosTuto,
							'SautRapide' => $SautRapide,
							'InfosBigTuto' => $InfosBigTuto,
							'ListeParties' => $ListeParties,
							'ListeMiniTutos' => $ListeMiniTutos,
							'qcm' => $qcm,
							'zco_mark' => $zco_mark
						));
					}
					//Sinon on corrige le sommaire du big-tuto
					else
					{
						//Infos sur le big-tuto
						if (!empty($s['id_tuto_recorrection']))
						{
							$InfosTuto = InfosBigTuto($s['id_tuto_recorrection']);
							$ListeParties = ListePartiesBigTuto($s['id_tuto_recorrection']);
						}
						else
						{
							$InfosTuto = InfosBigTuto($s['id_tuto_correction']);
							$ListeParties = ListePartiesBigTuto($s['id_tuto_correction']);
						}

						$ListeMiniTutos = array();

						foreach($ListeParties as $v)
						{
							$ListeMiniTutos[$v['partie_id']] = ListeTutosPartie($v['partie_id']);
						}

						//Génération du saut rapide
						$nb = 0;
						$SautRapide = '
						<div class="flot_droite">
							<form method="get" action="">
								Saut rapide : <select name="id2" onchange="document.location=\'/zcorrection/corriger-'.$_GET['id'].'-\' + this.value + \'.html\';">
									<option value="0" selected="selected">'.htmlspecialchars($InfosTuto['big_tuto_titre']).'</option>';
						foreach($ListeParties as $v)
						{
							if($nb != 0)
								$SautRapide .= '</optgroup>';
							$SautRapide .= '<optgroup label="'.htmlspecialchars($v['partie_titre']).'">';
							if($ListeMiniTutos[$v['partie_id']])
							{
								foreach($ListeMiniTutos[$v['partie_id']] as $t)
									$SautRapide .= '<option value="'.$t['mini_tuto_id'].'">'.str_replace('&amp;euro;', '&euro;', htmlspecialchars($t['mini_tuto_titre'])).'</a></li>';
							}
							$nb ++;
						}
						$SautRapide .= '
								</select>
								<noscript><input type="submit" value="Aller" /></noscript>
							</form>
						</div>';

						//Inclusion de la vue
						return render_to_response('ZcoZcorrectionBundle::correctionBigTuto.html.php', array(
							's' => $s,
							'ListeParties' => $ListeParties,
							'ListeMiniTutos' => $ListeMiniTutos,
							'InfosTuto' => $InfosTuto,
							'SautRapide' => $SautRapide,
						));
					}
				}

				//Sinon...
				else
				{
					return redirect(0, '/zcorrection/', MSG_ERROR);
				}
			}
		}
	}
}
