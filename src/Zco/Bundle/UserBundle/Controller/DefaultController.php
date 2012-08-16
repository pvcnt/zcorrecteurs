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

namespace Zco\Bundle\UserBundle\Controller;

use Zco\Bundle\UserBundle\Form\Type\NewUsernameType;
use Zco\Bundle\CaptchaBundle\Captcha\Captcha;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant les actions liées aux membres, leur connexion, 
 * leur déconnexion.
 *
 * @author Savageman <savageman@zcorrecteurs.fr>
 * @author Ziame <ziame@zcorrecteurs.fr>
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
	/**
	 * Affiche la liste de tous les membres du site. Permet de filtrer ces 
	 * membres suivant divers critères (groupe, pseudo, etc.).
	 *
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 * @author Ziame <ziame@zcorrecteurs.fr>
	 *
	 * @param  Request $request
	 * @param  integer $page La page à afficher
	 */
	public function indexAction(Request $request, $page = 1)
	{
		$query = array();
		
		//Filtre par pseudo.
		$pseudo = '';
		$type   = 1;
		
		if ($request->query->has('pseudo') && $request->query->get('pseudo') !== '')
		{
			$query['pseudo']       = $request->query->get('pseudo');
			$query['#pseudo_like'] = $request->query->has('type') ? (int) $request->query->get('type') : 1;
			$pseudo = $query['pseudo'];
			$type   = $query['#pseudo_like'];
		}
		
		//Tri des résultats.
		$order   = 'pseudo';
		$orderBy = 'asc';
		
		if ($request->query->has('tri') 
			&& in_array($request->query->get('tri'), array('id', 'pseudo', 'date_inscription', 'date_derniere_visite'))
			|| ($request->query->get('tri') === 'forum_messages' && verifier('voir_nb_messages')))
		{
			$query['#order_by'] = $request->query->get('tri');
			$order = $query['#order_by'];
			if ($request->query->has('ordre') && strtolower($request->query->get('ordre')) === 'desc')
			{
				$query['#order_by'] = '-'.$query['#order_by'];
				$orderBy = 'desc';
			}
		}
		else
		{
			$query['#order_by'] = 'pseudo';
		}
		
		//Filtre par groupe.
		$group          = null;
		$secondaryGroup = array();
		
		if ($request->query->has('groupe') && $request->query->get('groupe') !== '')
		{
			$query['group'] = (int) $request->query->get('groupe');
			$group = $query['group'];
		}
		if ($request->query->has('secondaire'))
		{
			$query['secondary_group'] = array_map('intval', (array) $request->query->get('secondaire'));
			$secondaryGroup = $query['secondary_group'];
		}
		
		//Pagination.
		$paginator = $this->get('knp_paginator');
		$users = $paginator->paginate(\Doctrine_Core::getTable('Utilisateur')->getQuery($query), $page, 30);
		$users->setUsedRoute('zco_user_indexWithPage');
		
		//Paramétrage de la vue.
		fil_ariane(null);
		\Page::$titre = 'Liste des membres';
		\Page::$description = 'Liste complète de tous les membres inscrits sur le site';
		if ($page > 1)
		{
			\Page::$titre .= ' - Page '.$page;
			\Page::$description.= ' - Page '.$page;
		}
		
		return render_to_response('ZcoUserBundle::index.html.php', array(
			'users' => $users,
			'groups' => \Doctrine_Core::getTable('Groupe')->getApplicable(),
			'secondaryGroups' => \Doctrine_Core::getTable('Groupe')->getBySecondary(),
			
			'pseudo' => $pseudo,
			'type' => $type,
			'group' => $group,
			'secondaryGroup' => $secondaryGroup,
			'order' => $order,
			'orderBy' => $orderBy,
		));
	}

	/**
	 * Affiche la liste des personnes connectées sur le site.
	 *
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 * @param  boolean $showAnonymousUsers Montrer les visiteurs non connectés ?
	 */
	public function onlineAction($showAnonymousUsers)
	{
		//Met à jour le nombre de connectés pour rester cohérent.
		$countAll = \Doctrine_Core::getTable('Online')->countAll();
		$this->get('zco_core.cache')->set('nb_connectes', $countAll, 60);
		
		$online         = \Doctrine_Core::getTable('Online')->getAll($showAnonymousUsers);
		$loggedUsers    = 0;
		$anonymousUsers = 0;
		if ($showAnonymousUsers)
		{
			foreach ($online as $user)
			{
				if ($user->isAuthenticated())
				{
					$loggedUsers++;
				}
				else
				{
					$anonymousUsers++;
				}
			}
		}
		else
		{
			$loggedUsers    = count($online);
			$anonymousUsers = $countAll - $loggedUsers;
		}
		
		//Paramétrage de la vue.
		fil_ariane('Connectés');
		\Page::$titre = 'Qui est en ligne ?'.($showAnonymousUsers ? ' - Voir les visiteurs non connectés' : '');
		\Page::$description = 'Quels sont les membres actuellement en ligne ?';
		
		return render_to_response('ZcoUserBundle::online.html.php', array(
			'online' 			 => $online,
			'anonymousUsers'	 => $anonymousUsers,
			'loggedUsers' 		 => $loggedUsers,
			'showAnonymousUsers' => $showAnonymousUsers,
		));
	}
	
	/**
	 * Affiche le profil d'un membre.
	 *
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 * @param  Request $request
	 */
	public function profileAction(Request $request, $id, $slug)
	{
		$user = \Doctrine_Core::getTable('Utilisateur')->getByIdFull($id);
		if (!$user)
		{
			throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
		}
		
		//zCorrecteurs::VerifierFormatageUrl($user->getUsername(), true);
		$vars = array('user' => $user);
		
		$firstChar = iconv('utf-8', 'us-ascii//TRANSLIT', $user->getUsername());
		$firstChar = strtolower($firstChar[0]);
		$art = in_array($firstChar, array('a', 'e', 'i', 'o', 'u', 'y')) ? "'" : 'e ';
		$vars['art'] = $art;

		if (verifier('voir_sanctions'))
		{
			$vars['punishments'] = \Doctrine_Core::getTable('UserPunishment')->getByUserId($user->getId());
		}
		if (verifier('membres_voir_avertos'))
		{
			$vars['warnings'] = \Doctrine_Core::getTable('UserWarning')->getByUserId($user->getId());
		}
		if (verifier('membres_voir_ch_pseudos'))
		{
			$vars['newPseudo'] = \Doctrine_Core::getTable('UserNewUsername')->getByUserId($user->getId());
		}
		if (verifier('voir_historique_groupes') || $user->isTeam())
		{
			require_once __DIR__.'/../../GroupesBundle/modeles/groupes.php';
			$vars['ListerGroupes'] = \ListerChangementGroupeMembre($user->getId());
			if ($user->isTeam() && count($vars['ListerGroupes']))
			{
				for ($i = count($vars['ListerGroupes']) - 1; $i >= 0; --$i)
				{
					if (!$vars['ListerGroupes'][$i]['nouveau_groupe_secondaire'])
					{
						$vars['lastGroupChange'] = $vars['ListerGroupes'][$i]['chg_date'];
						break;
					}
				}
			}
			if ($user->isTeam() && empty($vars['lastGroupChange']))
			{
				$vars['lastGroupChange'] = $user->getRegistrationDate();
			}
        }
        if (verifier('ips_analyser'))
        {
        	$vars['ListerIPs'] = ListerIPsMembre($user->getId());
        }
        $vars['canSendMp'] = $_SESSION['id'] != $user->getId() && verifier('mp_voir') && $user->getId() != ID_COMPTE_AUTO 
        						&& ($_SESSION['MPs'] < verifier('mp_quota') OR verifier('mp_quota') == -1);
        $vars['canSendEmail'] = verifier('rechercher_mail') || $user->isEmailDisplayed();
        $vars['canSeeInfos'] = verifier('membres_voir_ch_pseudos') || verifier('membres_voir_avertos') || verifier('voir_sanctions') 
        						|| verifier('voir_historique_groupes') || verifier('ips_analyser');
        $vars['canAdmin'] = verifier('groupes_changer_membre') || verifier('membres_editer_titre') || verifier('options_editer_profils');
        $vars['own'] = $_SESSION['id'] == $user->getId();

		//Paramétrage de la vue
		fil_ariane(htmlspecialchars($user->getUsername()));
		\Page::$titre = 'Profil d'.$art.htmlspecialchars($user->getUsername());
		\Page::$description = 'Pour en savoir plus sur la personnalité d'.$art.htmlspecialchars($user->getUsername()).' et son activité sur le site';
		
		return render_to_response('ZcoUserBundle::profile.html.php', $vars);
	}
	
	/**
	 * Affiche la carte de géolocalisation des membres de l'équipe.
	 *
	 * @author DJ Fox <djfox@zcorrecteurs.fr>
	 */
	public function localisationAction()
	{
		if (!verifier('voir_adresse'))
		{
			throw new AccessDeniedHttpException;
		}
		
		$markers = \Doctrine_Core::getTable('Utilisateur')->getMarkersForMap($this->get('router'));
        
		//Paramétrage de la vue.
		\Page::$titre = 'Géolocalisation de l\'équipe';
		fil_ariane('Voir la localisation des membres de l\'équipe');
		
		return render_to_response('ZcoUserBundle::localisation.html.php', compact('markers'));
	}

	/**
	 * Modifie le titre d'un membre.
	 *
	 * @author Vanger
	 */
	public function editTitleAction(Request $request, $id)
	{
		if (!($user = \Doctrine_Core::getTable('Utilisateur')->getById($id)))
		{
			throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
		}
		if (!(verifier('membres_editer_titre') || ($_SESSION['id'] == $user->getId() && verifier('membres_editer_propre_titre'))))
		{
			throw new AccessDeniedHttpException;
		}
		
		if ($request->getMethod() === 'POST' && $request->request->has('user_title'))
		{
			$user->setTitle($request->request->get('user_title'));
			$user->save();
			
			return redirect('Le titre a bien été modifié.', 
				$this->generateUrl('zco_user_profile', array('id' => $id, 'slug' => rewrite($user->getUsername()))));
		}
		
		fil_ariane('Modifier le titre');
		\Page::$titre = 'Modifier le titre';
			
		return render_to_response('ZcoUserBundle::editTitle.html.php', array(
			'user' => $user,
		));
	}
	
	/**
	 * Demande un changement de pseudo.
	 *
	 * @param Request $request
	 * @param integer|null $id
	 */
	public function newPseudoAction(Request $request, $id = null)
	{
		if ($id === null)
		{
			$id = $_SESSION['id'];
		}
		
		if (!verifier('connecte'))
		{
			throw new AccessDeniedHttpException;
		}
		if (!($user = \Doctrine_Core::getTable('Utilisateur')->getById($id)))
		{
			throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
		}
		if (($id != $_SESSION['id']) && !verifier('membres_editer_pseudos'))
		{
			throw new AccessDeniedHttpException;
		}
		if ($id == $_SESSION['id'] && \Doctrine_Core::getTable('UserNewUsername')->hasWaitingQuery($user->getId()))
		{
			return redirect(
				'Vous avez déjà une demande changement de pseudonyme en attente.', 
				$this->generateUrl('zco_options_index')
			);
		}
		
		$newUsername = new \UserNewUsername();
		$newUsername->setUser($user);
		$form = $this->get('form.factory')->create(new NewUsernameType(), $newUsername);
		
		//Si l'utilisateur veut s'inscrire
		if ($request->getMethod() === 'POST')
		{
			$form->bindRequest($request);
			if ($form->isValid())
			{
				if ($newUsername->isAutoValidated())
				{
					$newUsername->setAdmin($this->get('zco_user.user')->getEntity());
					$newUsername->save();
					
					$user->getUsername($newUsername->getNewUsername());
					$user->save();
					
					return redirect('Le pseudonyme a bien été changé.', 
						$this->generateUrl('zco_user_profile', array(
							'id' => $user->getId(), 
							'slug' => rewrite($user->getUsername()))));
				}
				
				$newUsername->save();
				
				return redirect(
					'Votre demande de changement de pseudonyme a été enregistrée.', 
					$this->generateUrl('zco_options_index')
				);
			}
		}
		
		//Paramétrage de la vue
		\Page::$titre = 'Demander un changement de pseudo';
		fil_ariane('Demander un changement de pseudo');
		
		return render_to_response('ZcoUserBundle::newPseudo.html.php', array(
			'user' => $user,
			'form' => $form->createView(),
		));
	}

	/**
	 * Affiche la liste des sauvegardes de zCode.
	 *
	 * @param Request $request
	 * @param integer|null $textarea Identifiant HTML d'un élément où récupérer 
	 *                               la sauvegarde
	 */
	public function zformBackupsAction(Request $request, $textarea = null)
	{
		if (!verifier('connecte'))
		{
			throw new AccessDeniedHttpException('Vous devez être connecté pour accéder à cette page.');
		}

		\Page::$titre = 'Sauvegardes automatiques de zCode';
		fil_ariane('Voir mes textes sauvegardés');
		
		return render_to_response('ZcoUserBundle::zformBackups.html.php', array(
			'backups'  => \Doctrine_Core::getTable('ZformBackup')->getByUserId($_SESSION['id']),
			'xhr'      => $request->query->get('xhr', false),
			'textarea' => $textarea,
		));
	}
}