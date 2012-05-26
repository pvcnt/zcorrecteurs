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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant l'ajout d'un nouveau MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class NouveauAction extends Controller
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		include(__DIR__.'/../modeles/participants.php');
		include(__DIR__.'/../modeles/ecrire.php');

		$MPTotal = $_SESSION['MPs'];

		if($MPTotal < verifier('mp_quota') OR verifier('mp_quota') == -1)
		{
			if(isset($_POST['titre']))
			{
				$_POST['titre'] = trim($_POST['titre']);
			}
			if(isset($_POST['destinataire']))
			{
				$_POST['destinataire'] = trim($_POST['destinataire']);
			}
			if(isset($_POST['destinataires']))
			{
				$_POST['destinataires'] = trim($_POST['destinataires']);
			}
			if(isset($_POST['texte']))
			{
				$_POST['texte'] = trim($_POST['texte']);
			}
			if(empty($_POST['titre']) OR (empty($_POST['pseudo']) AND empty($_POST['destinataires'])) OR empty($_POST['texte']))
			{
				if(!empty($_GET['id']) AND is_numeric($_GET['id']))
				{
					if($_GET['id'] != $_SESSION['id'])
					{
						$Pseudo = InfosUtilisateur($_GET['id']);
                                                if(!$Pseudo['utilisateur_id'])
                                                    return redirect(266, '', MSG_ERROR);
						$_POST['destinataires'] = $Pseudo['utilisateur_pseudo']."\n";
					}
                                        else
                                            return redirect(1, '', MSG_ERROR);

				}
				include(__DIR__.'/../modeles/dossiers.php');

				//Inclusion de la vue
				fil_ariane('Ajouter un nouveau message privé');
				Page::$titre = 'Ajout d\'un nouveau MP - '.Page::$titre;
				
				return render_to_response(array('ListerDossiers' => ListerDossiers()));
			}
			else
			{
				$_POST['faux_participants'] = array();
				$_POST['participants'] = array();
				if(!empty($_POST['destinataires']))
				{
					$_POST['faux_participants'] = explode("\n", $_POST['destinataires']);
				}
				if(!empty($_POST['pseudo']))
				{
					$_POST['faux_participants'][] = $_POST['pseudo'];
				}

				//Fonction MAGIQUE pour supprimer les éventuels doublons (exemple, un type met
				//trois fois le pseudo DJ Fox dans les participants :-° )
				$_POST['faux_participants'] = array_unique($_POST['faux_participants']);

				//On supprime les éventuelles cellules vides
				//On en profite pour compter le nombre de participants sélectionnés et pour vérifier que chaque participant existe.
				//On enlève aussi de la liste le pseudo du type qui poste (s'il est assez con pour vouloir s'envoyer un message à lui-même).
				$NombreParticipants = 0;
				foreach($_POST['faux_participants'] as $valeur)
				{
					$valeur = preg_replace("(\r\n|\n|\r)",'',$valeur);
					if(!empty($valeur) AND $valeur != $_SESSION['pseudo'])
					{
						$user_exists = VerifierParticipantExiste(htmlspecialchars($valeur));
						if(empty($user_exists))
						{
							return redirect(279, '', MSG_ERROR);
						}
						$_POST['participants'][] = $user_exists;
						$NombreParticipants++;
					}
				}
				$NombreParticipants++; //On compte aussi le créateur du MP.

				if(isset($_POST['crypter']) && $NombreParticipants != 2)
					return redirect(293, '', MSG_ERROR);

				//On vérifie qu'il y a au moins un participant en plus du créateur.
				if($NombreParticipants < 2)
				{
					return redirect(277, '', MSG_ERROR);
				}

				//On vérifie si l'utilisateur dépasse ou non la limite du nombre de participants
				if(verifier('mp_nb_participants_max') != -1 && $NombreParticipants > verifier('mp_nb_participants_max'))
				{
					return redirect(277, '', MSG_ERROR);
				}

				//On ajoute le MP en BDD
				$NouveauMPID = AjouterMP();
				if($NouveauMPID === false)
					return redirect(292, '', MSG_ERROR);

				//On vide les caches de tous les participants
				$current_participant = 0;
				foreach($_POST['participants'] as $valeur)
				{
					if($current_participant != $valeur)
					{
						$current_participant = $valeur;
						$this->get('zco_core.cache')->set('MPnonLu'.$valeur, true, strtotime('+1 hour'));
					}
				}

				//On vide le cache du créateur
				unset($_SESSION['MPs']);
				unset($_SESSION['MPsnonLus']);
				return redirect(278, 'lire-'.$NouveauMPID.'.html');
			}
		}
		else
		{
			return redirect(276, 'index.html', MSG_ERROR);
		}
	}
}
