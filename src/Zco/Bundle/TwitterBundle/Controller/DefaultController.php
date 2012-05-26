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

namespace Zco\Bundle\TwitterBundle\Controller;

use Zco\Bundle\CoreBundle\Paginator\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Intégration des comptes Twitter au site.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
	/**
	 * Affichage de la liste des tweets postés sur les différents comptes 
	 * liés au site.
	 *
	 * @param integer $page
	 */
	public function indexAction($page = 1)
	{
		fil_ariane(null);
		\Page::$titre = 'Liste des tweets';
		if ($page > 1)
		{
			\Page::$titre .= ' - Page '.$page;
		}
		
		$query     = \Doctrine_Core::getTable('TwitterTweet')->getByAccountQuery(null);
		$paginator = $this->get('knp_paginator');
		$tweets    = $paginator->paginate($query, $page, 15);
		$tweets->setUsedRoute('zco_twitter_index');
		
		return render_to_response('ZcoTwitterBundle::index.html.php', array(
			'tweets'   => $tweets,
			'mentions' => $this->get('zco_admin.manager')->get('mentions'),
		));
	}
	
	/**
	 * Affichage de la liste des comptes Twitter liés au site.
	 *
	 * @param Request $request
	 */
	public function accountsAction(Request $request)
	{
		if (!verifier('twitter_comptes'))
		{
			throw new AccessDeniedHttpException();
		}
		
		if ($request->getMethod() === 'POST' && $request->request->has('default_account'))
		{
			\Doctrine_Core::getTable('TwitterCompte')->setDefaultAccount(
				$request->request->get('default_account')
			);
			
			return redirect('Le compte par défaut a été modifié.', 
				$this->generateUrl('zco_twitter_accounts'));
		}
		
		\Page::$titre = 'Gérer les comptes';
		fil_ariane('Comptes');
		
		return render_to_response('ZcoTwitterBundle::accounts.html.php', array(
			'accounts' => \Doctrine_Core::getTable('TwitterCompte')->getAll(),
		));
	}

	/**
	 * Lier un nouveau compte Twitter au site.
	 *
	 * @param Request $request
	 */
	public function addAccountAction(Request $request)
	{
		if (!verifier('twitter_comptes'))
		{
			throw new AccessDeniedHttpException();
		}
		
		if ($request->query->has('token') && $request->query->get('token') == $_SESSION['token'])
		{
			if (\Doctrine_Core::getTable('TwitterCompte')->add())
			{
				return redirect('Le compte a bien été ajouté.', 
					$this->generateUrl('zco_twitter_accounts'));
			}
			
			return redirect('Une erreur s\'est produite pendant l\'ajout du compte.', 
				$this->generateUrl('zco_twitter_addAccount'), 
				MSG_ERROR);
		}
		
		\Page::$titre = 'Ajouter un compte Twitter';
		fil_ariane(array(
			'Comptes' => $this->generateUrl('zco_twitter_accounts'),
			'Ajouter un compte'
		));
		unset($_SESSION['oauth_requestToken']);
		
		return render_to_response('ZcoTwitterBundle::addAccount.html.php');
	}

	/**
	 * Supprimer un compte Twitter lié au site ainsi que tous les 
	 * tweets associés.
	 *
	 * @param Request $request
	 * @param integer $id
	 */
	public function deleteAccountAction(Request $request, $id)
	{
		if (!verifier('twitter_comptes'))
		{
			throw new AccessDeniedHttpException();
		}
		
		if (!$account = \Doctrine_Core::getTable('TwitterCompte')->find($id))
		{
			return redirect('Ce compte n\'existe pas.', $this->generateUrl('zco_twitter_accounts'), MSG_ERROR);
		}

		if ($request->request->has('confirm'))
		{
			if ($r = \zCorrecteurs::verifierToken()) return $r;
			\Doctrine_Core::getTable('TwitterTweet')->deleteByAccount($account);
			\Doctrine_Core::getTable('TwitterMention')->deleteByAccount($account);
			$account->delete();
			
			return redirect('Le compte a été supprimé.', $this->generateUrl('zco_twitter_accounts'));
		}
		
		\Page::$titre = 'Supprimer un compte Twitter';
		fil_ariane(array(
			'Comptes' => $this->generateUrl('zco_twitter_accounts'),
			'Supprimer un compte'
		));
		
		return render_to_response('ZcoTwitterBundle::deleteAccount.html.php', compact('account'));
	}

	/**
	 * Poster un nouveau tweet sur un des comptes liés au site.
	 *
	 * @param Request $request
	 * @param integer|null $mention
	 */
	public function newTweetAction(Request $request, $mention = null)
	{
		if (!verifier('twitter_tweeter'))
		{
			throw new AccessDeniedHttpException();
		}
		
		$accounts = \Doctrine_Core::getTable('TwitterCompte')->getAll(true);
		if ($mention && !($mention = \Doctrine_Core::getTable('TwitterMention')->find($mention)))
		{
			return redirect('Cette mention n\'existe pas.', 
				$this->generateUrl('zco_twitter_mentions'), 
				MSG_ERROR);
		}

		if ($request->request->has('compte') && $request->request->has('tweet'))
		{
			if ($r = \zCorrecteurs::verifierToken()) return $r;
			
			//Existence du compte.
			if (!isset($accounts[$request->request->get('compte')]))
			{
				return redirect('Ce compte n\'existe pas.', $url, MSG_ERROR);
			}
			$account = $accounts[$request->request->get('compte')];
			
			//Longueur du tweet.
			$tweet  = trim($request->request->get('tweet'));
			if ($tweet === '')
			{
				return redirect('Vous ne pouvez pas poster de tweet vide.', $url, MSG_ERROR);
			}
			if (mb_strlen($tweet) > 140)
			{
				return redirect('Le tweet est trop long (140 caractères maximum).', $url, MSG_ERROR);
			}
			
			$url = $this->generateUrl('zco_twitter_newTweet', array('id' => ($mention ? $mention['id'] : null)));
			if (verifier('twitter_procuration') 
				&& $request->request->has('pseudo') 
				&& $pseudo = $request->request->get('pseudo'))
			{
				$pseudo = trim($pseudo);
				$InfosUtilisateur = InfosUtilisateur($pseudo);
				if (empty($InfosUtilisateur))
				{
					return redirect('Aucun membre portant ce pseudo n\'a été trouvé.', $url, MSG_ERROR);
				}
				$auteurID = $InfosUtilisateur['utilisateur_id'];
			}
			else
			{
				$auteurID = $_SESSION['id'];
			}

			if (\Doctrine_Core::getTable('TwitterTweet')->add(
				$tweet, $auteurID, $account, $mention)
			)
			{
				$this->get('zco_core.cache')->delete('accueil_derniersTweets');
				if(isset($_SERVER['HTTP_REFERER']) &&
				   strpos($_SERVER['HTTP_REFERER'], $mentionsUrl = $this->generateUrl('zco_twitter_mentions')) !== false
				)
				{
					return redirect('Le tweet a été ajouté.', $mentionsUrl);
				}
				
				return redirect('Le tweet a été ajouté.', $this->generateUrl('zco_twitter_index'));
			}
			
			return redirect('Une erreur s\'est produite pendant l\'ajout du tweet.', 
				$this->generateUrl('zco_twitter_newTweet'), MSG_ERROR);
		}
		
		\Page::$titre = 'Nouveau tweet';
		
		return render_to_response('ZcoTwitterBundle::newTweet.html.php', compact('accounts', 'mention'));
	}

	/**
	 * Voir la liste des mentions adressées sur Twitter aux différents comptes 
	 * liés au site.
	 */
	public function mentionsAction($page = 1)
	{
		if (!verifier('twitter_tweeter'))
		{
			throw new AccessDeniedHttpException();
		}
		
		$account = \Doctrine_Core::getTable('TwitterCompte')->getDefaultAccount();
		$paginator = $this->get('knp_paginator');
		if ($query = \Doctrine_Core::getTable('TwitterMention')->getByAccountQuery($account))
		{
			\Doctrine_Core::getTable('TwitterMention')->setReadByAccount($account);
		}
		else
		{
			$query = array();
		}
		
		$mentions = $paginator->paginate($query, $page, 15);
		$mentions->setUsedRoute('zco_twitter_mentions');
		
		fil_ariane('Mentions');
		\Page::$titre = 'Mentions Twitter';
		if ($page > 1)
		{
			\Page::$titre .= ' - Page '.$page;
		}
		
		return render_to_response('ZcoTwitterBundle::mentions.html.php', compact('mentions', 'account'));
	}
}
