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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant les options membre.
 *
 * @author vincent1870, DJ Fox, Vanger, Barbatos
 */
class OptionsActions extends Controller
{
	public function __construct()
	{
		include_once(__DIR__.'/../modeles/options.php');
	}

	/**
	 * Affiche la liste des options disponibles.
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 */
	public function executeIndex()
	{
		zCorrecteurs::VerifierFormatageUrl();

		Page::$titre = 'Accueil de mes options';
		fil_ariane('Accueil de mes options');
		return render_to_response(array('InfosUtilisateur' => InfosUtilisateur($_SESSION['id'])));
	}

	public function executeAjaxEnregistrerCoordonnees()
	{
		if(verifier('modifier_adresse') AND !empty($_POST['point']) AND !empty($_POST['id']) AND is_numeric($_POST['id']))
		{
			$coordonnees = explode(", ", mb_substr($_POST['point'], 1, mb_strlen($_POST['point'])-2));
			ModifierUtilisateur($_POST['id'], array('latitude' => $coordonnees[0], 'longitude' => $coordonnees[1]));
			return new Symfony\Component\HttpFoundation\Response($coordonnees[0].', '.$coordonnees[1]);
		}
		else
		{
			return new Symfony\Component\HttpFoundation\Response('ERREUR');
		}
	}

	/**
	 * Modifie l'avatar en l'envoyant depuis l'ordinateur ou en le liant
	 * depuis une adresse web.
	 * @author DJ Fox <djfox@zcorrecteurs.fr>
	 */
	public function executeModifierAvatar()
	{
		Page::$titre = 'Modifier l\'avatar';

		//Si aucun id n'est spécifié, on édite son profil
		if(empty($_GET['id']) || $_SESSION['id'] == $_GET['id'])
		{
			zCorrecteurs::VerifierFormatageUrl();
			$_GET['id'] = $_SESSION['id'];
		}
		if(!is_numeric($_GET['id']))
			return redirect(122, '/options/', MSG_ERROR);

		$InfosMembre = InfosUtilisateur($_GET['id']);
		if(empty($InfosMembre))
			return redirect(123, '/options/', MSG_ERROR);

		//Si on n'a pas le droit d'éditer ce profil
		if($_SESSION['id'] != $_GET['id'] && !verifier('options_editer_avatars'))
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

		if($_SESSION['id'] != $_GET['id'])
			zCorrecteurs::VerifierFormatageUrl($InfosMembre['utilisateur_pseudo'], true);

		//Si on veut modifier l'avatar
		if(!empty($_POST['submit']))
		{
			//Si on a soumis un fichier au formulaire, on attaque les vérifications :)
			$extensions_autorisees = array('.jpg', '.jpeg', '.png', '.gif');
			$extensions_autorisees2 = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
			$nom_fichier = '';

			//Suppression de l'avatar
			if(isset($_POST['avatar_suppr']))
			{
				if(!unlink(BASEPATH.'/web/uploads/avatars/'.$InfosMembre['utilisateur_avatar']))
				{
					return redirect(0, $_SERVER['REQUEST_URI'], MSG_ERROR);
				}
				ModifierUtilisateur($_GET['id'], array('avatar' => ''));
			}

			//Upload depuis le disque dur
			else
			{
				if(!empty($_FILES['avatar']) AND $_FILES['avatar']['size'] > 0)
				{
					if (UPLOAD_ERR_OK == $_FILES['avatar']['error'])
					{
						$extension_fichier = mb_strtolower(mb_strrchr($_FILES['avatar']['name'], '.'));

						//Vérification de l'extension.
						if(!in_array($extension_fichier, $extensions_autorisees))
							return redirect(125, $_SERVER['REQUEST_URI'], MSG_ERROR);

						//Déplacement du fichier temporaire vers le dossier des avatars
						$infos_image = getimagesize($_FILES['avatar']['tmp_name']);
						$nom_fichier = $_GET['id'].$extension_fichier;
						if (move_uploaded_file($_FILES['avatar']['tmp_name'], BASEPATH.'/web/uploads/avatars/'.$nom_fichier))
						{
							if($nom_fichier != $InfosMembre['utilisateur_avatar'] && !empty($InfosMembre['utilisateur_avatar']))
							{
								//Suppression de l'ancien avatar
								unlink(BASEPATH.'/web/uploads/avatars/'.$InfosMembre['utilisateur_avatar']);
							}
						}
						else
							return redirect(0, $_SERVER['REQUEST_URI'], MSG_ERROR);

						ModifierUtilisateur($_GET['id'], array('avatar' => $nom_fichier));
					}
					else
					{
						return redirect(0, $_SERVER['REQUEST_URI'], MSG_ERROR);
					}
				}

				//Upload par URL
				elseif(!empty($_POST['avatar2']))
				{
					$avatar_contenu = file_get_contents($_POST['avatar2']);
					if(!$avatar_contenu)
					{
						return redirect(127, $_SERVER['REQUEST_URI'], MSG_ERROR);
					}
					else
					{
						//Vérification de l'extension.
						$extension_fichier = mb_strtolower(mb_strrchr($_POST['avatar2'], '.'));
						$infos_image = getimagesize($_POST['avatar2']);
						if(!in_array($extension_fichier, $extensions_autorisees) OR !in_array($infos_image[2], $extensions_autorisees2))
						{
							return redirect(125, $_SERVER['REQUEST_URI'], MSG_ERROR);
						}

						//Ok, on "upload".
						$nom_fichier = $_GET['id'].$extension_fichier;
						if(!file_put_contents(BASEPATH.'/web/uploads/avatars/'.$nom_fichier, $avatar_contenu))
						{
							return redirect(0, $_SERVER['REQUEST_URI'], MSG_ERROR);
						}
						if($nom_fichier != $InfosMembre['utilisateur_avatar'] && !empty($InfosMembre['utilisateur_avatar']))
						{
							//Suppression de l'ancien avatar
							unlink(BASEPATH.'/web/uploads/avatars/'.$InfosMembre['utilisateur_avatar']);
						}
					}

					ModifierUtilisateur($_GET['id'], array('avatar' => $nom_fichier));
				}

				//Redimensionnement de l'avatar si nécessaire
				if(!empty($nom_fichier) && ($infos_image[0] > 100 || $infos_image[1] > 100))
				{
					$this->get('imagine')
						->open(BASEPATH.'/web/uploads/avatars/'.$nom_fichier)
						->thumbnail(new \Imagine\Image\Box(100, 100))
						->save(BASEPATH.'/web/uploads/avatars/'.$nom_fichier);
				}
			}

			if($_GET['id'] == $_SESSION['id'])
				return redirect(244);
			else
				return redirect(244, '/membres/profil-'.$_GET['id'].'.html');
		}

		//Inclusion de la vue
		fil_ariane('Changer l\'avatar');
		
		return render_to_response(array('InfosMembre' => $InfosMembre));
	}

	/**
	 * Modifie l'adresse mail.
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 */
	public function executeModifierMail()
	{
		Page::$titre = 'Modifier l\'adresse mail';

		//Si on veut activer une nouvelle adresse
		if(!empty($_GET['hash']))
		{
			list($mail, $id) = ActiverNouveauMail($_GET['hash']);
			$this->gdGenerateImage(htmlspecialchars($mail), BASEPATH.'/web/uploads/membres/mail/'.$id.'.png');
			return redirect(241, '/');
		}

		//Si aucun id n'est spécifié, on édite son profil
		if(empty($_GET['id']) || $_SESSION['id'] == $_GET['id'])
		{
			zCorrecteurs::VerifierFormatageUrl();
			$_GET['id'] = $_SESSION['id'];
		}
		if(!is_numeric($_GET['id']))
			return redirect(122, '/options/', MSG_ERROR);

		$InfosMembre = InfosUtilisateur($_GET['id']);
		if(empty($InfosMembre))
			return redirect(123, '/options/', MSG_ERROR);

		//Si on n'a pas le droit d'éditer ce profil
		if($_SESSION['id'] != $_GET['id'] && !verifier('options_editer_mails'))
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

		if($_SESSION['id'] != $_GET['id'])
			zCorrecteurs::VerifierFormatageUrl($InfosMembre['utilisateur_pseudo'], true);

		//Si on veut modifier l'adresse
		if(isset($_POST['submit']))
		{
			//Si un champ est manquant
			if(empty($_POST['mail']) || ($_SESSION['id'] == $_GET['id'] && empty($_POST['mot_passe'])))
				return redirect(17, $_SERVER['REQUEST_URI'], MSG_ERROR);

			//Si l'adresse mail n'est pas valide
			if(!VerifierValiditeMail($_POST['mail']))
				return redirect(242, '', MSG_ERROR);

			//Si on a besoin de validation
			if($_SESSION['id'] == $_GET['id'])
			{
				//Vérification du mot de passe
				if(sha1($_POST['mot_passe']) != $InfosMembre['utilisateur_mot_de_passe'])
					return redirect(130, '', MSG_ERROR);

				$hash = sha1(uniqid(rand(), true));
				ModifierUtilisateur($_GET['id'], array('nouvel_email' => $_POST['mail'], 'hash_validation2' => $hash));

				//Envoi du mail
				$message = render_to_string('::mail_auto/nouveau_mail.html.php', array(
					'pseudo'           => $InfosMembre['utilisateur_pseudo'],
					'nouvelle_adresse' => $_POST['mail'],
					'ancienne_adresse' => $InfosMembre['utilisateur_email'],
					'hash'             => $hash,
				));

				send_mail($_POST['mail'], $_POST['mail'], '[zCorrecteurs.fr] Changement d\'adresse mail', $message);
				return redirect(234);
			}
			//Sinon modification par un admin sans validation
			else
			{
				ModifierUtilisateur($_GET['id'], array('email' => $_POST['mail']));
				return redirect(233, '/membres/profil-'.$_GET['id'].'.html');
			}
		}

		//Inclusion de la vue
		fil_ariane('Modifier l\'adresse mail');
		return render_to_response(array('InfosMembre' => $InfosMembre));
	}

	/**
	 * Modifie le mot de passe.
	 * @author DJ Fox, vincent1870
	 */
	public function executeModifierMotDePasse()
	{
		Page::$titre = 'Modifier le mot de passe';

		//Si aucun id n'est spécifié, on édite son profil
		if(empty($_GET['id']) || $_SESSION['id'] == $_GET['id'])
		{
			zCorrecteurs::VerifierFormatageUrl();
			$_GET['id'] = $_SESSION['id'];
		}
		if(!is_numeric($_GET['id']))
			return redirect(122, '/options/', MSG_ERROR);

		$InfosMembre = InfosUtilisateur($_GET['id']);
		if(empty($InfosMembre))
			return redirect(123, '/options/', MSG_ERROR);

		//Si on n'a pas le droit d'éditer ce profil
		if($_SESSION['id'] != $_GET['id'] && !verifier('options_editer_pass'))
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

		if($_SESSION['id'] != $_GET['id'])
			zCorrecteurs::VerifierFormatageUrl($InfosMembre['utilisateur_pseudo'], true);

		//Si rien n'a été soumis
		if(empty($_POST['submit']))
		{
			//Inclusion de la vue
			fil_ariane('Changer le mot de passe');
			
			return render_to_response(array('InfosMembre' => $InfosMembre));
		}
		//Sinon on modifie le profil
		else
		{
			if($_GET['id'] == $_SESSION['id'])
			{
				if(empty($_POST['ancien_mdp']) || empty($_POST['nouveau_mdp']) || empty($_POST['confirm_nouveau_mdp']))
				{
					return redirect(17, $_SERVER['REQUEST_URI'], MSG_ERROR);
				}
				elseif(sha1($_POST['ancien_mdp']) != $InfosMembre['utilisateur_mot_de_passe'])
				{
					return redirect(130, $_SERVER['REQUEST_URI'], MSG_ERROR);
				}
				elseif(strlen($_POST['nouveau_mdp']) < 6)
				{
					return redirect(128, $_SERVER['REQUEST_URI'], MSG_ERROR);
				}
				elseif($_POST['nouveau_mdp'] != $_POST['confirm_nouveau_mdp'])
				{
					return redirect(129, $_SERVER['REQUEST_URI'], MSG_ERROR);
				}
			}
			elseif($_GET['id'] != $_SESSION['id'])
			{
				if(empty($_POST['nouveau_mdp']) || empty($_POST['confirm_nouveau_mdp']))
				{
					return redirect(17, $_SERVER['REQUEST_URI'], MSG_ERROR);
				}
				if(strlen($_POST['nouveau_mdp']) < 6)
				{
					return redirect(128, $_SERVER['REQUEST_URI'], MSG_ERROR);
				}
				elseif($_POST['nouveau_mdp'] != $_POST['confirm_nouveau_mdp'])
				{
					return redirect(129, $_SERVER['REQUEST_URI'], MSG_ERROR);
				}
			}

			ModifierUtilisateur($_GET['id'], array('mot_de_passe' => sha1($_POST['nouveau_mdp'])));

			if($_GET['id'] == $_SESSION['id'])
				return redirect(243);
			else
				return redirect(243, '/membres/profil-'.$_GET['id'].'.html');
		}
	}

	/**
	 * Modifie le profil (signature, biographie, adresses de contact, etc.).
	 * @author DJ Fox, Barbatos
	 */
	public function executeModifierProfil()
	{
		Page::$titre = 'Modifier le profil';

		//Si aucun id n'est spécifié, on édite son profil
		if(empty($_GET['id']) || $_SESSION['id'] == $_GET['id'])
		{
			zCorrecteurs::VerifierFormatageUrl();
			$_GET['id'] = $_SESSION['id'];
		}
		if(!is_numeric($_GET['id']))
			return redirect(122, '/options/', MSG_ERROR);

		$InfosMembre = InfosUtilisateur($_GET['id']);
		if(empty($InfosMembre))
			return redirect(123, '/options/', MSG_ERROR);

		//Si on n'a pas le droit d'éditer ce profil
		if($_SESSION['id'] != $_GET['id'] && !verifier('options_editer_profils'))
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

		if($_SESSION['id'] != $_GET['id'])
			zCorrecteurs::VerifierFormatageUrl($InfosMembre['utilisateur_pseudo'], true);

		//Si rien n'a été soumis
		if(isset($_POST['submit']))
		{
			if(empty($_POST['coordonnees']) AND !empty($_POST['utilisateur_adresse']))
				return redirect(131, $_SERVER['REQUEST_URI'], MSG_ERROR, -1);

			//Modification en BDD
			$params = array(
					'afficher_mail' => isset($_POST['afficher_mail']),
					'afficher_pays' => isset($_POST['afficher_pays']),
					'msn' => $_POST['msn'],
					'aim' => $_POST['aim'],
					'jabber' => $_POST['jabber'],
					'icq' => $_POST['icq'],
					'skype' => $_POST['skype'],
					'profession' => $_POST['profession'],
					'passions' => $_POST['passions'],
					'date_naissance' => $_POST['date_naissance'],
					'biographie' => $_POST['biographie'],
					'signature' => $_POST['signature'],
					'citation' => $_POST['citation'],
					'site_web' => $_POST['site_web'],
					'sexe' => ((verifier('modifier_sexe')) ? $_POST['sexe'] : 0),
					'cle_pgp' => verifier('options_ajouter_cle_pgp') ? $_POST['cle_pgp'] : $InfosMembre['utilisateur_cle_pgp'],
			);
			if(verifier('modifier_adresse'))
				$params['adresse'] = $_POST['utilisateur_adresse'];

			ModifierUtilisateur($_GET['id'], $params);

			//Génération des images pour les adresses
			if(isset($_POST['afficher_mail']))
				$this->gdGenerateImage(htmlspecialchars($InfosMembre['utilisateur_email']), BASEPATH.'/web/uploads/membres/mail/'.$_GET['id'].'.png');
			if(!empty($_POST['msn']))
				$this->gdGenerateImage(htmlspecialchars($_POST['msn']), BASEPATH.'/web/uploads/membres/msn/'.$_GET['id'].'.png');
			if(!empty($_POST['aim']))
				$this->gdGenerateImage(htmlspecialchars($_POST['aim']), BASEPATH.'/web/uploads/membres/aim/'.$_GET['id'].'.png');
			if(!empty($_POST['icq']))
				$this->gdGenerateImage(htmlspecialchars($_POST['icq']), BASEPATH.'/web/uploads/membres/icq/'.$_GET['id'].'.png');
			if(!empty($_POST['jabber']))
				$this->gdGenerateImage(htmlspecialchars($_POST['jabber']), BASEPATH.'/web/uploads/membres/jabber/'.$_GET['id'].'.png');
			if(!empty($_POST['skype']))
				$this->gdGenerateImage(htmlspecialchars($_POST['skype']), BASEPATH.'/web/uploads/membres/skype/'.$_GET['id'].'.png');

			if($_GET['id'] == $_SESSION['id'])
				return redirect(132);
			else
				return redirect(132, '/membres/profil-'.$_GET['id'].'.html');
		}
		$date_naissance = explode('-', $InfosMembre['utilisateur_date_naissance']);
		$date_naissance = $date_naissance[2].'/'.$date_naissance[1].'/'.$date_naissance[0];

		//Inclusion de la vue
		fil_ariane('Modifier le profil');
		
		if ($this->container->getParameter('kernel.environment') === 'prod')
		{
		    $this->get('zco_vitesse.resource_manager')->requireResources(array(
		        'google-maps',
		        '@ZcoUserBundle/Resources/public/js/profil_carte.js',
		    ));
		}
		
		return render_to_response(array(
			'InfosMembre' => $InfosMembre,
			'date_naissance' => $date_naissance,
		));
	}

	/**
	 * Permet d'indiquer une période d'absence ou de la lever.
	 * @author Vanger
	 */
	public function executeGererAbsence()
	{
		Page::$titre = 'Indiquer une période d\'absence';

		// Si aucun ID n'est défini, on prend celui du membre actuel
		if(empty($_GET['id']) || $_SESSION['id'] == $_GET['id'])
		{
			zCorrecteurs::VerifierFormatageUrl();
			$_GET['id'] = $_SESSION['id'];
		}
		// On s'assure que le membre a les droits, sinon il n'a rien à faire là
		if(!($_SESSION['id'] == $_GET['id'] || verifier('options_editer_absence')))
		{
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		// Si l'id n'est pas numérique, on affiche également une erreur
		if(!is_numeric($_GET['id']))
		{
			return redirect(122, '/', MSG_ERROR);
		}
		// On récupère les infos de l'utilisateur et on vérifie qu'il existe
		$InfosMembre = InfosUtilisateur($_GET['id']);
		if(empty($InfosMembre))
		{
			return redirect(123, '/', MSG_ERROR);
		}
		if($_SESSION['id'] != $_GET['id'])
			zCorrecteurs::VerifierFormatageUrl($InfosMembre['utilisateur_pseudo'], true);

		// Si on a soumis le formulaire et que les champs sont remplis
		if(!empty($_POST['submit']) && (isset($_POST['delete_abs']) || isset($_POST['duree_abs'])))
		{
			// Si on supprime une absence
			if(isset($_POST['delete_abs']))
			{
				SupprimerAbsence($_GET['id']);
				return redirect(330, '/options/gerer-absence-'.$_GET['id'].'.html');
			}
			elseif(isset($_POST['duree_abs']))
			{
				// Si on a choisi autre chose qu'indeterminé, le champ temps_abs doit être rempli et numérique
				if($_POST['duree_abs']!=0 && (empty($_POST['temps_abs']) || !is_numeric($_POST['temps_abs'])) && empty($_POST['fin_abs']))
				{
					return redirect(331, '/options/gerer-absence-'.$_GET['id'].'.html', MSG_ERROR);
				}

				$result = AjouterAbsence($_GET['id'], $_POST);
				if($result === 1) // Date non valide
					return redirect(482, '/options/gerer-absence-'.$_GET['id'].'.html', MSG_ERROR);
				else if($result === 2) // Date de fin antérieur à la date de début
					return redirect(483, '/options/gerer-absence-'.$_GET['id'].'.html', MSG_ERROR);
				else if($result === true) // Absence prise en compte
					return redirect(332, '/options/gerer-absence-'.$_GET['id'].'.html');
			}
		}
		// Sinon, on affiche la page
		else
		{
			fil_ariane('Indiquer une période d\'absence');
			
			return render_to_response(array('InfosMembre' => $InfosMembre));
		}
	}

	/**
	 * Permet de modifier les options de navigation d'un membre ou celles par
	 * défaut.
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 */
	public function executeNavigation()
	{
		Page::$titre = 'Modifier les options de navigation';

		if(empty($_GET['id']) || $_SESSION['id'] == $_GET['id'])
		{
			zCorrecteurs::VerifierFormatageUrl();
			$_GET['id'] = $_SESSION['id'];
		}
		if((!empty($_GET['id']) && verifier('options_editer_navigation')) || (!empty($_GET['id']) &&  $_SESSION['id'] == $_GET['id']) || (empty($_GET['id']) && verifier('options_editer_defaut')))
		{
			if(!is_numeric($_GET['id']))
				return redirect(122, '/options/', MSG_ERROR);
			if($_GET['id'] != $_SESSION['id'])
			{
				$InfosMembre = InfosUtilisateur($_GET['id']);
				zCorrecteurs::VerifierFormatageUrl($InfosMembre['utilisateur_pseudo'], true);
			}
			else
				$InfosMembre = RecupererOptionsNavigation($_GET['id']);
			if(!empty($_GET['id']) && empty($InfosMembre))
				return redirect(123, '/options/', MSG_ERROR);
		}
		else
		{
			return redirect(123, '/options/', MSG_ERROR);
		}

		//Si quelque chose a été posté
		if(isset($_POST['submit']))
		{
		    $prefs = array(
		        'activer_rep_rapide' => isset($_POST['activer_rep_rapide']) ? 1 : 0,
		        'afficher_signatures' => isset($_POST['afficher_signatures']) ? 1 : 0,
		        'temps_redirection' => (int) $_POST['temps_redirection'],
		        'afficher_admin_rapide' => (verifier('admin') && isset($_POST['afficher_admin_rapide'])) ? 1 : 0,
        	);
			ModifierOptionsNavigation($_GET['id'], $prefs);
			
			if($_GET['id'] == $_SESSION['id'])
				return redirect(124);
			elseif(!empty($_GET['id']))
				return redirect(124, '/membres/profil-'.$_GET['id'].'.html');
			else
				return redirect(124, 'navigation-0.html');
		}

		//Inclusion de la vue
		fil_ariane('Modifier les options de navigation'.(empty($_GET['id']) ? ' par défaut' : ''));
		
		return render_to_response(array('InfosMembre' => $InfosMembre));
	}

	/**
	 * Affiche la liste des sauvegardes de zCode.
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 */
	public function executeSauvegardesZcode()
	{
		Page::$titre = 'Sauvegardes automatiques de zCode';
		fil_ariane('Voir mes textes sauvegardés');
		
		return render_to_response(array(
			'ListerSauvegardes' => ListerSauvegardesZcode($_SESSION['id']),
			'xhr' => !empty($_GET['xhr']),
		));
	}

	/**
	 * Modification du décalage horaire (timezone)
	 *
	 * @author mwsaz
	 */
	public function executeDecalage()
	{
		Page::$titre = 'Modification du décalage horaire';
		fil_ariane('Décalage horaire');

		$decalages = array(
			'-43200' => '(GMT -12:00) Eniwetok, Kwajalein',
			'-39600' => '(GMT -11:00) Midway Island, Samoa',
			'-36000' => '(GMT -10:00) Hawaii',
			'-32400' => '(GMT -9:00) Alaska',
			'-28800' => '(GMT -8:00) Pacific Time (US &amp; Canada)',
			'-25200' => '(GMT -7:00) Mountain Time (US &amp; Canada)',
			'-21600' => '(GMT -6:00) Central Time (US &amp; Canada), Mexico City',
			'-18000' => '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima',
			'-14400' => '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz',
			'-12600' => '(GMT -3:30) Newfoundland',
			'-10800' => '(GMT -3:00) Brazil, Buenos Aires, Georgetown',
			'-7200'  => '(GMT -2:00) Mid-Atlantic',
			'-3600'  => '(GMT -1:00) Azores, Cape Verde Islands',
			'0'      => '(GMT) Western Europe Time, London, Lisbon, Casablanca',
			'3600'   => '(GMT +1:00) Brussels, Copenhagen, Madrid, Paris',
			'7200'   => '(GMT +2:00) Kaliningrad, South Africa',
			'10800'  => '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
			'12600'  => '(GMT +3:30) Tehran',
			'14400'  => '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
			'16200'  => '(GMT +4:30) Kabul',
			'18000'  => '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
			'19800'  => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi',
			'14950'  => '(GMT +5:45) Kathmandu',
			'21600'  => '(GMT +6:00) Almaty, Dhaka, Colombo',
			'25200'  => '(GMT +7:00) Bangkok, Hanoi, Jakarta',
			'28800'  => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong',
			'32400'  => '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
			'34200'  => '(GMT +9:30) Adelaide, Darwin',
			'36000'  => '(GMT +10:00) Eastern Australia, Guam, Vladivostok',
			'39600'  => '(GMT +11:00) Magadan, Solomon Islands, New Caledonia',
			'43200'  => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka',
		);

		if (isset($_POST['decalage'], $decalages[$_POST['decalage']]))
		{
			if ($r = zCorrecteurs::verifierToken()) return $r;

			$dbh = Doctrine_Manager::connection()->getDbh();
			$dbh->exec('UPDATE zcov2_utilisateurs_preferences SET
				preference_decalage = '.$_POST['decalage'].'
				WHERE preference_id_utilisateur = '.$_SESSION['id']);
			$_SESSION['prefs']['decalage'] = $_POST['decalage'];
			return redirect(5);
		}

		$pref = preference('decalage');

		return render_to_response(compact('decalages', 'pref'));
	}
	
	/**
     * Génère une image à partir d'un texte.
     *
     * @param string  $texte     Le texte à inscrire.
     * @param string  $nom       L'endoit où écrire l'image.
     * @param integer $hauteur   La hauteur (par défaut).
     * @param integer $largeur   La largeur (par défaut).
     */
    private function gdGenerateImage($texte, $nom, $hauteur = null, $largeur = null)
    {
    	if(is_file($nom))
    		unlink($nom);
    	if(is_null($hauteur))
    		$hauteur = 20;
    	if(is_null($largeur))
    		$largeur = strlen($texte) * 7;

    	$image = imagecreate($largeur, $hauteur);
    	imagealphablending($image, true);
    	imagesavealpha($image, true);

    	$blanc = imagecolorallocate($image, 255, 255, 255);
    	$noir = imagecolorallocate($image, 0, 0, 0);
    	imagecolortransparent($image, $blanc);

    	imagestring($image, 2, 5, 3, $texte, $noir);
    	imagepng($image, $nom);
    }
}

