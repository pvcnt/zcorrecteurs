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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur gérant la réponse à une candidature (test, acceptation ou refus).
 *
 * @author vincent1870, Vanger
 */
class RepondreAction extends Controller
{
	public function execute()
	{
        if (!verifier('recrutements_repondre')) {
            throw new AccessDeniedHttpException();
        }

		//Si on a bien envoyé une candidature
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosCandidature = InfosCandidature($_GET['id']);
			if(empty($InfosCandidature))
				return redirect(227, '/recrutement/', MSG_ERROR);
			zCorrecteurs::VerifierFormatageUrl($InfosCandidature['candidature_pseudo'], true);
			Page::$titre = 'Candidature de '.htmlspecialchars($InfosCandidature['utilisateur_pseudo']).' - Réponse à la candidature';

			if(!in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_TESTE, CANDIDATURE_ENVOYE)))
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

			//Si on a envoyé la réponse
			if(isset($_POST['etat']) && is_numeric($_POST['etat']) && in_array($_POST['etat'], array(CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE, CANDIDATURE_ATTENTE_TEST)))
			{
				if(in_array($_POST['etat'], array(CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE)) && !empty($_POST['comm']))
				{
					AccepterRefuserCandidature($_GET['id']);
					if($_POST['etat'] == CANDIDATURE_ACCEPTE)
					{
						//Changement de groupe si il le faut
						if(isset($_POST['change_grp']))
						{
							$_POST['id'] = $InfosCandidature['utilisateur_id'];
							$_POST['groupe'] = $InfosCandidature['recrutement_id_groupe'];
							ChangerGroupeUtilisateur();
							$this->get('zco_core.cache')->save('dernier_refresh_droits', time(), 0);
						}

						//Envoi du MP
						$message = render_to_string('::mp_auto/recrutements_acceptation.html.php', array(
							'pseudo'       => $InfosCandidature['utilisateur_pseudo'],
							'raison'       => $_POST['comm'],
							'pseudo_admin' => $_SESSION['pseudo'],
							'id_admin'     => $_SESSION['id'],
							'nom'          => $InfosCandidature['recrutement_nom'],
							'id'           => $InfosCandidature['recrutement_id'],
						));
						
						AjouterMPAuto('[Recrutement] Vous avez été accepté !', '', $InfosCandidature['utilisateur_id'], $message);
						return redirect(203, 'recrutement-'.$InfosCandidature['recrutement_id'].'.html#candidatures');
					}
					else
					{
						//Envoi du MP
						$message = render_to_string('::mp_auto/recrutements_refus.html.php', array(
							'pseudo'       => $InfosCandidature['utilisateur_pseudo'],
							'raison'       => $_POST['comm'],
							'pseudo_admin' => $_SESSION['pseudo'],
							'id_admin'     => $_SESSION['id'],
							'nom'          => $InfosCandidature['recrutement_nom'],
							'id'           => $InfosCandidature['recrutement_id'],
						));

						AjouterMPAuto('[Recrutement] Vous avez été refusé', '', $InfosCandidature['utilisateur_id'], $message);
						return redirect(167, 'recrutement-'.$InfosCandidature['recrutement_id'].'.html#candidatures');
					}
				}
				elseif(in_array($_POST['etat'], array(CANDIDATURE_ACCEPTE, CANDIDATURE_REFUSE)) && empty($_POST['comm']))
				{
					return redirect(17, 'repondre-'.$_GET['id'].'.html', MSG_ERROR);
				}
				elseif($_POST['etat'] == CANDIDATURE_ATTENTE_TEST)
				{
					//Upload du tuto si besoin
					$nom_fichier = '';
					if(!empty($_FILES['tuto']) AND $_FILES['tuto']['size'] > 0)
					{
						if (UPLOAD_ERR_OK == $_FILES['tuto']['error'])
						{
							$extension_fichier = mb_strtolower(mb_strrchr($_FILES['tuto']['name'], '.'));

							//Vérification de l'extension.
							if($extension_fichier != '.tuto')
								return redirect(125, 'repondre-'.$_GET['id'].'.html', MSG_ERROR);

							//Déplacement du fichier temporaire vers le dossier des tutos
							$nom_fichier = time().$extension_fichier;
							move_uploaded_file($_FILES['tuto']['tmp_name'], BASEPATH.'/web/tutos/recrutement/originaux/'.$nom_fichier);
						}
						else
							throw new \RuntimeException('Erreur lors de l\'envoi du tutoriel.', 500);
					}

					//Envoi du MP
					$message = render_to_string('::mp_auto/recrutements_test.html.php', array(
						'pseudo'       => $InfosCandidature['utilisateur_pseudo'],
						'pseudo_admin' => $_SESSION['pseudo'],
						'id_admin'     => $_SESSION['id'],
						'nom'          => $InfosCandidature['recrutement_nom'],
						'date'         => $_POST['date_fin'],
						'id'           => $InfosCandidature['recrutement_id'],
						'explicatif'   => (!empty($_POST['explicatif'])) ? '<citation nom="Explication">'.$_POST['explicatif'].'</citation>' : ''
					));

					AjouterMPAuto('[Recrutement] Un test est requis', '', $InfosCandidature['utilisateur_id'], $message);

					TesterCandidature($_GET['id'], $nom_fichier);
					return redirect(168, 'recrutement-'.$InfosCandidature['recrutement_id'].'.html#candidatures');
				}
				//else
				//	return redirect(17, 'repondre-'.$_GET['id'].'.html', MSG_ERROR);
			}

			//Inclusion de la vue
			fil_ariane(array(
				htmlspecialchars($InfosCandidature['recrutement_nom']) => 'recrutement-'.$InfosCandidature['recrutement_id'].'.html',
				'Candidature de '.htmlspecialchars($InfosCandidature['utilisateur_pseudo']) => 'candidature-'.$_GET['id'].'.html',
				'Répondre à la candidature'
			));
			
			return render_to_response(array('InfosCandidature' => $InfosCandidature));
		}
		else
			return redirect(226, 'index.html', MSG_ERROR);
	}
}
