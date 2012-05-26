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
 * Contrôleur gérant l'affichage de la liste des tutoriels à zCorriger, et
 * diverses actions (reprise d'une correction, abandon d'une correction, etc.).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class IndexAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		Page::$titre = 'Tutoriels en attente de correction / en correction';

		// Ajout de DJ Fox
		//Reprise d'une correction depuis Zér0
		if(!empty($_GET['reprise_correction_zero']) && is_numeric($_GET['reprise_correction_zero']) && verifier('zcorriger'))
		{
			//Si on n'a pas encore confirmé
			if(empty($_POST['confirmer']))
			{
				//Si on annule
				if(isset($_POST['annuler']))
				{
					return new Symfony\Component\HttpFoundation\RedirectResponse('index.html');
				}

				fil_ariane('Reprendre la correction depuis zéro');
				return render_to_response('ZcoZcorrectionBundle::zeroCorrectionConfirm.html.php');
			}
			//Si on confirme
			else
			{
				$message_renvoye = ReprendreDepuisZer0Correction($_GET['reprise_correction_zero']);
				if(!is_bool($message_renvoye))
				{
					$_SESSION['erreur'][] = $message_renvoye;
				}
				else
				{
					return redirect(140);
				}
			}
		}
		elseif(!empty($_GET['reprise_recorrection_zero']) && is_numeric($_GET['reprise_recorrection_zero']) && verifier('zcorriger'))
		{
			//Si on n'a pas encore confirmé
			if(empty($_POST['confirmer']))
			{
				//Si on annule
				if(isset($_POST['annuler']))
				{
					return new Symfony\Component\HttpFoundation\RedirectResponse('index.html');
				}

				fil_ariane('Reprendre la recorrection depuis zéro');
				return render_to_response('ZcoZcorrectionBundle::zeroReorrectionConfirm.html.php');
			}
			//Si on confirme
			else
			{
				$message_renvoye = ReprendreDepuisZer0Recorrection($_GET['reprise_recorrection_zero']);
				if(!is_bool($message_renvoye))
				{
					$_SESSION['erreur'][] = $message_renvoye;
				}
				else
				{
					return redirect(140);
				}
			}
		}
		//FIN Ajout de DJ Fox

		else
		{
			//Abandon de la correction d'un tutoriel
			if(!empty($_GET['abandon']) && is_numeric($_GET['abandon']))
			{
				$s2 = InfosCorrection($_GET['abandon']);
				$id_correcteur = $s2['id_recorrecteur'] ? $s2['id_recorrecteur'] : $s2['id_correcteur'];
				if($id_correcteur == $_SESSION['id'])
				{
					$id_abandon = $s2['recorrection_id'] ? $s2['recorrection_id'] : $s2['correction_id'];
					RetirerCorrection($id_abandon);
					return redirect(141);
				}
				else
				{
					return redirect(139, '', MSG_ERROR);
				}
			}
			else if (!empty($_GET['id']) && verifier('zcorriger'))
			{
				if (!is_numeric($_GET['id']))
				{
					return redirect(138, '', MSG_ERROR);
				}
				else
				{
					$s = InfosCorrection($_GET['id']);

					if (empty($s))
					{
						return redirect(139, '', MSG_ERROR);;
					}
					else
					{
						// REPRISE de la recorrection
						if (!empty($s['recorrection_abandonee']))
						{
							ReprendreCorrection($s['recorrection_id']);
							return redirect(142);
						}
						if (!empty($s['correction_abandonee']))
						{
							ReprendreCorrection($s['correction_id']);
							return redirect(143);
						}

						//Début/fin de (re)correction
						if (!empty($s['recorrection_date_fin']))
						{
							return redirect(144, '', MSG_ERROR);
						}
						else if (!empty($s['recorrection_date_debut']) || !empty($s['recorrection_id']))
						{
							return redirect(146, '', MSG_ERROR);
						}
						else if (!empty($s['correction_date_fin']) && 1 == $s['soumission_recorrection'])
						{
							if (false != ($id_tutoriel = CopierTutoSoumission($_GET['id'])))
							{
								$id_correction = AjouterCorrection($id_tutoriel, $_SESSION['id']);
								SoumissionAjouterRecorrection($_GET['id'], $id_correction);
								return redirect(148);
							}
							else
							{
								return redirect(0, '', MSG_ERROR);
							}
						}
						else if (!empty($s['correction_date_debut']) || !empty($s['correction_id']))
						{
							return redirect(145, '', MSG_ERROR);
						}
						else
						{
							if (false != ($id_tutoriel = CopierTutoSoumission($_GET['id'])))
							{
								$id_correction = AjouterCorrection($id_tutoriel, $_SESSION['id']);
								SoumissionAjouterCorrection($_GET['id'], $id_correction);
								return redirect(147);
							}
							else
							{
								return redirect(0, '', MSG_ERROR);
							}
						}
					// CopierTutoSoumission($_GET['id']);
					}
				}
			}

			fil_ariane('Liste des corrections en attente');
			$this->get('zco_vitesse.resource_manager')->requireResources(array(
    		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
    		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
    		));
            
			return render_to_response(array(
				'ListerSoumissions' => ListerSoumissions(),
				'ListerSoumissionsCorrecteur' => ListerSoumissionsCorrecteur(),
			));
		}
	}
}
