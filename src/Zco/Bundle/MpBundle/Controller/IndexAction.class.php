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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur gérant l'affichage de la liste de tous les MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class IndexAction extends Controller
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		if (isset($_POST['annuler']))
		{
			return new RedirectResponse('index.html');
		}

		//Inclusion des modèles
		include(__DIR__.'/../modeles/liste_mp.php');
		include(__DIR__.'/../modeles/dossiers.php');
		include(__DIR__.'/../modeles/participants.php');

		$ListerDossiers = ListerDossiers();

		//Calcul du nombre de messages dans le dossier accueil
		$MPTotal = $_SESSION['MPs'];
		$MPDansDesDossiers = 0;
		foreach($ListerDossiers as &$dossier)
		{
			$MPDansDesDossiers += $dossier['nombre_dans_dossier'];
			if($dossier['mp_dossier_id'] == $_GET['id'])
				$nbMp = $dossier['nombre_dans_dossier'];
		}
		$MPDansAccueil = $MPTotal - $MPDansDesDossiers;

		if($_GET['id'] === '')
		{
			$url = 'index-p%s.html';
			$nbMp = $MPTotal;
		}
		elseif($_GET['id'] === '0')
		{
			$url = 'index-0-p%s.html';
			$nbMp = $MPDansAccueil;
		}
		else
			$url = 'index-'.$_GET['id'].'-p%s.html';

		if(!isset($nbMp))
			return redirect(257, 'index.html', MSG_ERROR);

		$recherche = !empty($_POST['recherche_mp']) ? $_POST['recherche_mp'] : null;
		$nbMpParPage = 30;
		$NombreDePages = ceil($nbMp / $nbMpParPage);
		zCorrecteurs::VerifierFormatageUrl(null, true, false, $NombreDePages);
		$page = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : $NombreDePages;
		list($ListerMP, $Participants) = ListerMP($recherche, $page, $NombreDePages);

		// Réindexer le tableau des MP pour avoir l'ID comme clé
		$MP = array();
		foreach ($ListerMP as $m)
			$MP[$m['mp_id']] = $m;
		$ListerMP = $MP;
		unset ($MP);

		// Intégration des participants
		foreach ($Participants as $Lparticipant)
			$ListerMP[$Lparticipant[0]['mp_participant_mp_id']]['_participants'] = $Lparticipant;
		unset ($Participants);

		$ListePages = liste_pages($page, $NombreDePages, $MPTotal, $nbMpParPage, $url);

		if($ListerMP)
		{
			if(isset($_POST['action']) && isset($_POST['MP']))
			{
				include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/action_etendue_plusieurs_mp.php');
				if($_POST['action'] == 'ouvrir' && verifier('mp_fermer'))
				{
					OuvrirMP($_POST['MP']);
					return redirect(52, 'index.html');
				}
				elseif($_POST['action'] == 'fermer' && verifier('mp_fermer'))
				{
					FermerMP($_POST['MP']);
					return redirect(52, 'index.html');
				}
				elseif($_POST['action'] == 'lus')
				{
					RendreMPLus($_POST['MP'], $ListerMP);
					unset($_SESSION['MPsnonLus']);
					return redirect(52, 'index.html');
				}
				elseif($_POST['action'] == 'nonlus')
				{
					RendreMPNonLus($_POST['MP']);
					unset($_SESSION['MPsnonLus']);
					return redirect(52, 'index.html');
				}
				elseif($_POST['action'] == 'deplacer' && isset($_POST['deplacer_lieu']) && $ListerDossiers)
				{
					DeplacerMP($_POST['MP'], $_POST['deplacer_lieu']);
					return redirect(52, 'index.html');
				}
				elseif($_POST['action'] == 'supprimer')
				{
					if(isset($_POST['confirmation']) && $_POST['confirmation'] == 'Oui')
					{
						SupprimerMultipleMP($_POST['MP']);
						unset($_SESSION['MPs']);
						unset($_SESSION['MPsnonLus']);
						return redirect(52, 'index.html');
					}
					else
					{
						fil_ariane('Supprimer plusieurs MP');
						return render_to_response('ZcoMpBundle::supprimerMpMassif.html.php');
					}
				}
			}
			else
			{
				foreach($ListerMP as &$MP)
				{
					//Appel de la fonction lu / non-lu pour le rond de couleur
					//de gauche (utilisateur actuel)
					$EnvoiDesInfos = array(
					'mp_lunonlu_utilisateur_id' => $_SESSION['id'],
					'mp_lunonlu_mp_id' => $MP['mp_id'],
					'mp_lunonlu_message_id' => $MP['mp_lunonlu_actuel_dernier_message_lu'],
					'mp_dernier_message_id' => $MP['mp_dernier_message_id']
					);
					$MP['_lu'] = LuNonlu($EnvoiDesInfos);

					// Liste des pages
					$nbMessagesParPage = 20;
					$MP['_pages'] = liste_pages(-1,
						ceil(($MP['mp_reponses'] + 1) / 20),
						$MP['mp_reponses'] + 1,
						20,
						'lire-'.$MP['mp_id'].'-p%s.html'
					);

					foreach($MP['_participants'] as &$participant)
					{
						//Appel de la fonction lu / non-lu pour le rond de couleur à gauche de chaque participant
						$EnvoiDesInfos = array(
						'mp_lunonlu_utilisateur_id' => $participant['mp_participant_id'],
						'mp_lunonlu_mp_id' => $MP['mp_id'],
						'mp_lunonlu_message_id' => $participant['mp_lunonlu_participant_message_id'],
						'mp_dernier_message_id' => $MP['mp_dernier_message_id']
						);
						$participant['_lu'] = LuNonlu($EnvoiDesInfos);
					}
				}
			}
		}
		if(!isset($_POST['action']) && !isset($_POST['MP']))
		{
			//Inclusion de la vue
			fil_ariane('Accueil des MP');
			Page::$titre = 'Accueil des MP';
			$this->get('zco_vitesse.resource_manager')->requireResources(array(
			    '@ZcoForumBundle/Resources/public/css/forum.css',
			    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
			    '@ZcoCoreBundle/Resources/public/js/messages.js',
			));
    		
			return render_to_response(array(
				'ListerDossiers' => $ListerDossiers,
				'MPDansDesDossiers' => $MPDansDesDossiers,
				'MPDansAccueil' => $MPDansAccueil,
				'ListePages' => $ListePages,
				'ListerMP' => $ListerMP,
				'recherche' => $recherche,
			));
		}
	}
}
