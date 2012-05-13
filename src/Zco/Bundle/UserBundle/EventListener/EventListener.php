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

namespace Zco\Bundle\UserBundle\EventListener;

use Zco\Bundle\InformationsBundle\Event\FilterSitemapEvent;
use Zco\Bundle\InformationsBundle\InformationsEvents;
use Zco\Bundle\UserBundle\UserEvents;
use Zco\Bundle\UserBundle\Exception\LoginException;
use Zco\Bundle\UserBundle\Event\CheckValueEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Observateur chargé des événements du kernel.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener extends ContainerAware implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			KernelEvents::REQUEST    	=> array('onKernelRequest', 127),
			KernelEvents::CONTROLLER 	=> 'onKernelController',
			UserEvents::VALIDATE_EMAIL  => 'onValidateEmail',
			InformationsEvents::SITEMAP => 'onFilterSitemap',
		);
	}
		
	/**
	 * Met à jour les permissions en temps réel si cela a été demandé, 
	 * s'occupe de la connexion automatique avec les cookies
	 * ainsi que de la vérification du bannissement d'un membre.
	 *
	 * @param GetResponseEvent $event
	 */
	public function onKernelRequest(GetResponseEvent $event)
	{
		if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType())
		{
			return;
		}
		
		//Définit certaines variables importantes de session si ce n'est pas 
		//encore le cas.
		if (empty($_SESSION['token']))
		{
		    $_SESSION['token'] = md5(uniqid(rand(), true));
		}
		if (!isset($_SESSION['erreur']))
		{
		    $_SESSION['erreur'] = array();
		}
		if (!isset($_SESSION['message']))
		{
		    $_SESSION['message'] = array();
		}
		
		$user = $this->container->get('zco_user.user');
		
		//Mise à jour temps réel des groupes associés au compte de 
		//l'utilisateur actuellement connecté.
		if (
			$user->isAuthenticated()
			&& isset($_SESSION['refresh_droits']) 
			&& $this->container->get('zco_core.cache')->get('dernier_refresh_droits') >= $_SESSION['refresh_droits']
		)
		{
			$user->reloadGroups();
		}
		
		//Tentative de connexion depuis l'environnement courant. Normalement seul
		//->login() peut générer une LoginException, mais on préfère encadrer le 
		//tout par un try{ } en cas de listener mal écrit.
		try
		{
			if (($userEntity = $user->attemptEnvLogin($event->getRequest())) instanceof \Utilisateur)
			{
				$user->login($userEntity);
			}
		}
		catch (LoginException $e)
		{
			//Ne rien faire, la connexion par l'environnement a simplement échoué.
		}
		
		//Si le membre n'est toujours pas connecté on lui attribue de force 
		//les attributs habituellement liés au compte.
		if (!isset($_SESSION['groupe']) || !isset($_SESSION['id']))
		{
			$_SESSION['groupe'] = GROUPE_VISITEURS;
			$_SESSION['id'] = RecupererIdVisiteur();
			$_SESSION['refresh_droits'] = time();
		}
		
		//Permet de stocker dans les logs Apache le pseudo de chaque membre.
		//On pose un cookie car on ne peut que récupérer un cookie avec Apache.
		if (isset($_SESSION['pseudo']) AND !isset($_COOKIE['pseudo']))
		{
			setcookie('pseudo', $_SESSION['pseudo'], strtotime('+1 day'), '/');
		}
	}
	
	/**
	 * Bannit certaines adresses courriel interdites via l'interface graphique 
	 * correspondante.
	 *
	 * @param CheckValueEvent $event
	 */
	public function onValidateEmail(CheckValueEvent $event)
	{
		if (\Doctrine_Core::getTable('BannedEmail')->isBanned($event->getValue()))
		{
			$event->reject('Cette adresse courriel n\'est pas autorisée.');
		}
	}
	
	/**
	 * Met à jour la position courante du visiteur sur le site (a besoin 
	 * pour cela que le contrôleur ait déjà été choisi) ainsi que sa 
	 * dernière adresse IP.
	 *
	 * @param FilterControllerEvent $event
	 */
	public function onKernelController(FilterControllerEvent $event)
	{
		$request = $this->container->get('request');
		if (//Requête asynchrone type Ajax
			!$request->isXmlHttpRequest()
			//Chargement d'une page non HMTL (flux, Javascript, etc.)
			&& $request->attributes->get('_format', 'html') === 'html'
			//Route interne (type profiler)
			&& substr($request->attributes->get('_route'), 0, 1) !== '_')
		{
			$this->refreshSession();
		}
		
		//Mise à jour du nombre de connectés (cache invalide ou 0 connecté, 
		//ce qui n'est pas possible => utilisation directe de !).
		$cache = $this->container->get('zco_core.cache');
		if (!$cache->get('nb_connectes'))
		{
			$cache->set('nb_connectes', \Doctrine_Core::getTable('Online')->countAll(), 60);
		}
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		$router = $this->container->get('router');
		$event->addLink($router->generate('zco_user_session_register', array(), true), array(
			'changefreq' => 'monthly',
			'priority'	 => '0.5',
		));
		$event->addLink($router->generate('zco_user_session_login', array(), true), array(
			'changefreq' => 'monthly',
			'priority'	 => '0.5',
		));
		$event->addLink($router->generate('zco_user_online', array(), true), array(
			'changefreq' => 'daily',
			'priority'	 => '0.5',
		));
		$event->addLink($router->generate('zco_user_index', array(), true), array(
			'changefreq' => 'daily',
			'priority'	 => '0.5',
		));
	}
	
	private function refreshSession()
	{
		$dbh = \Doctrine_Manager::connection()->getDbh();
		$request = $this->container->get('request');
		
		$ip = ip2long($request->getClientIp(true));
		$cat = GetIDCategorieCourante();
		$id1 = !empty($_GET['id']) ? $_GET['id'] : 0;
		$id2 = !empty($_GET['id2']) ? $_GET['id2'] : 0;
		$id = $_SESSION['id'];
		
		//Si la dernière IP diffère, on la met à jour (en cas de membre connecté uniquement)
		if (!isset($_SESSION['last_ip']) || $_SESSION['last_ip'] != $ip)
		{
			if (isset($_SESSION['last_ip']))
			{
				$ip_to_delete = $_SESSION['last_ip'];
			}
			else
			{
				$ip_to_delete = $ip;
			}

			//Suppression de sa ligne de visiteur dans la table des connectés
			\Doctrine_Core::getTable('Online')->deleteByIp($ip_to_delete);
			
			if (verifier('connecte'))
			{
				//Géolocalisation
				list($pays, ,$idPays) = Geolocaliser($ip);
				$_SESSION['pays'] = $pays;
				if (!empty($idPays))
				{
				    $_SESSION['pays_id'] = $idPays;
				}

				//Mise à jour de la table des membres
				$stmt = $dbh->prepare("UPDATE zcov2_utilisateurs " .
						"SET utilisateur_date_derniere_visite = NOW(), utilisateur_ip = :ip, utilisateur_localisation = :pays " .
						"WHERE utilisateur_id = :id");
				$stmt->bindParam(':id', $_SESSION['id']);
				$stmt->bindParam(':ip', $ip);
				$stmt->bindParam(':pays', $pays);
				$stmt->execute();
				$stmt->closeCursor();

				//Insertion de la nouvelle ip (ou mise à jour de sa date d'utilisation)
				$proxy = ip2long($request->getClientIp(false));
				$proxy = $proxy === $ip ? null : $proxy;
				$stmt = $dbh->prepare("
				INSERT INTO zcov2_utilisateurs_ips(ip_id_utilisateur, ip_ip, ip_proxy, ip_date_debut, ip_date_last, ip_localisation)
				VALUES(:id, :ip, :proxy, NOW(), NOW(), :pays)
				ON DUPLICATE KEY UPDATE ip_date_last = NOW()");
				$stmt->bindParam(':id', $_SESSION['id']);
				$stmt->bindParam(':ip', $ip);
				$stmt->bindParam(':proxy', $proxy);
				$stmt->bindParam(':pays', $pays);
				$stmt->execute();
				$stmt->closeCursor();
			}
			

			$_SESSION['last_ip'] = $ip;
		}
		
		//On met à jour la table des sessions.
		$stmt = $dbh->prepare('UPDATE '.$this->container->getParameter('database.prefix').'connectes '
			.'SET connecte_ip = :ip, connecte_derniere_action = NOW(), '
			.'connecte_id_categorie = :cat, connecte_user_agent = :agent, '
			.'connecte_nom_action = \'\' '
			.'WHERE connecte_id_utilisateur = :u');
		$stmt->bindValue(':ip', $ip, \PDO::PARAM_INT);
		$stmt->bindValue(':u', $id, \PDO::PARAM_INT);
		$stmt->bindValue(':cat', $cat, \PDO::PARAM_INT);
		$stmt->bindValue(':agent', $request->server->get('HTTP_USER_AGENT'));
		$stmt->execute();
		if (!$stmt->rowCount())
		{
			$stmt->closeCursor();
			
			$stmt = $dbh->prepare('INSERT INTO '.$this->container->getParameter('database.prefix').'connectes(connecte_ip, '
				.'connecte_id_utilisateur, connecte_debut, connecte_derniere_action, '
				.'connecte_id_categorie, connecte_user_agent) '
				.'VALUES(:ip, :u, NOW(), NOW(), :cat, :agent)');
			$stmt->bindParam(':ip', $ip);
			$stmt->bindParam(':u', $id);
			$stmt->bindParam(':cat', $cat);
			$stmt->bindValue(':agent', $request->server->get('HTTP_USER_AGENT'));
			$stmt->execute();
		}
		$stmt->closeCursor();
	}
}