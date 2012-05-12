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

namespace Zco\Bundle\UserBundle\User;

use Zco\Bundle\UserBundle\UserEvents;
use Zco\Bundle\UserBundle\Exception\LoginException;
use Zco\Bundle\UserBundle\Exception\ValueException;
use Zco\Bundle\UserBundle\Event\CheckValueEvent;
use Zco\Bundle\UserBundle\Event\LoginEvent;
use Zco\Bundle\UserBundle\Event\FilterLoginEvent;
use Zco\Bundle\UserBundle\Event\FormLoginEvent;
use Zco\Bundle\UserBundle\Event\EnvLoginEvent;
use Zco\Bundle\UserBundle\Event\FilterQueryEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Classe de gestion de l'utilisateur. Offre diverses opérations de gestion du 
 * cycle de vie d'un visiteur (connexion, déconnexion) et d'accès aux informations
 * principales. Délègue une partie des fonctionnalités à des observateurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>é
 */
class User
{
	const AUTHENTICATED_ANONYMOUSLY = 0;
	const AUTHENTICATED_REMEMBERED = 1;
	const AUTHENTICATED_FULLY = 2;
	
	protected $entityId;
	protected $entity;
	protected $dispatcher;
	protected $pendingState;
	protected $state;
	
	/**
	 * Constructeur.
	 * 
	 * @param EventDispatcherInterface $dispatcher
	 */
	public function __construct(EventDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
		$this->state      = self::AUTHENTICATED_ANONYMOUSLY;
	}
	
	/**
	 * Vérifie si l'utilisateur est authentifié à un certain niveau.
	 *
	 * @param  integer $state Niveau d'authentification à vérifier
	 * @return boolean
	 */
	public function isAuthenticated($state = self::AUTHENTICATED_REMEMBERED)
	{
		return $this->state >= $state;
	}
	
	/**
	 * Vérifie que le mot de passe corresponde bien à celui de l'utilisateur.
	 * Seul le mot de passe doit être vérifié par les observateurs, en aucun 
	 * cas la conformité du compte (validé, non banni, etc.), cette méthode 
	 * pouvant être appelée dans d'autres contextes que celui d'authentification.
	 *
	 * @param  string $password Le mot de passe à vérifier
	 * @param  \Utilisateur|null $user L'utilisateur correspondant si non authentifié
	 * @return boolean
	 */
	public function checkPassword($password, \Utilisateur $user = null)
	{
		if (($isAcceptable = $this->checkValue('checkPassword', UserEvents::CHECK_PASSWORD, $password, $user)) !== null)
		{
			return $isAcceptable;
		}
		
		return $user->getPassword() && sha1($password) === $user->getPassword();
	}
	
	/**
	 * Vérifie que le mot de passe soit acceptable, dans le sens où c'est un 
	 * « bon » mot de passe. La vérification de base vérifie que sa longueur 
	 * soit suffisante (6 caractères).
	 *
	 * @param  string $password Le mot de passe à valider
	 * @param  \Utilisateur|null $user L'utilisateur correspondant si non authentifié
	 * @return boolean
	 * @throws ValueException Si le mot de passe n'est pas acceptable
	 */
	public function validatePassword($password, \Utilisateur $user = null)
	{
		if (($isAcceptable = $this->checkValue('validatePassword', UserEvents::VALIDATE_PASSWORD, $password, $user)) !== null)
		{
			return $isAcceptable;
		}
		
		if (strlen($password) < 6)
		{
			throw new ValueException('Le mot de passe est trop court.');
		}
		
		return true;
	}
	
	/**
	 * Vérifie que le nom d'utilisateur soit acceptable, dans le sens où il est 
	 * autorisé. La vérification de base s'assure qu'il ne soit pas déjà utilisé 
	 * pour un autre compte.
	 *
	 * @param  string $username Le nom d'utilisateur à valider
	 * @param  \Utilisateur|null $user L'utilisateur correspondant si non authentifié
	 * @return boolean
	 * @throws ValueException Si le nom d'utilisateur n'est pas acceptable
	 */
	public function validateUsername($username, \Utilisateur $user = null)
	{
		if (($isAcceptable = $this->checkValue('validateUsername', UserEvents::VALIDATE_USERNAME, $username, $user)) !== null)
		{
			return $isAcceptable;
		}
		
		if (\Doctrine_Core::getTable('Utilisateur')->countByPseudo($username) > 0)
		{
			throw new ValueException('Le pseudonyme est déjà utilisé.');
		}
		
		return true;
	}
	
	/**
	 * Vérifie que l'adresse courriel soit acceptable, dans le sens où elle est 
	 * autorisée. La vérification de base s'assure qu'elle ne soit pas déjà 
	 * utilisée et d'une forme compatible avec une adresse réelle.
	 *
	 * @param  string $email Le courriel à valider
	 * @param  \Utilisateur|null $user L'utilisateur correspondant si non authentifié
	 * @return boolean
	 * @throws ValueException Si l'adresse courriel n'est pas acceptable
	 */
	public function validateEmail($email, \Utilisateur $user = null)
	{
		if (($isAcceptable = $this->checkValue('validateEmail', UserEvents::VALIDATE_EMAIL, $email, $user)) !== null)
		{
			return $isAcceptable;
		}
		
		if (\Doctrine_Core::getTable('Utilisateur')->countByEmail($email) > 0)
		{
			throw new ValueException('L\'adresse courriel est déjà utilisée.');
		}
		if (!preg_match('`^[a-z0-9+._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$`', $email))
		{
			throw new ValueException('L\'adresse courriel est invalide.');
		}
		
		return true;
	}
	
	/**
	 * Démarre un processus de connexion par formulaire. L'objectif ici est 
	 * de renvoyer l'entité correspondant à l'utilisateur qui souhaite se 
	 * connecter d'après les informations reçues du formulaire. La vérification 
	 * du mot de passe est aussi faite ici.
	 *
	 * @param  array $data Les données reçues du formulaire
	 * @param  Request $request La requête correspondante
	 * @return \Utilisateur L'entité désirant se connecter
	 * @throws LoginException Si les informations fournies ne sont pas valides
	 */
	public function attemptFormLogin(array $data, Request $request)
	{
		$event = new FormLoginEvent($request, $data);
		$this->dispatcher->dispatch(UserEvents::FORM_LOGIN, $event);
		
		//1) Le login a été annulé, on arrête tout.
		if ($event->isAborted())
		{
			throw new LoginException($event->getErrorMessage());
		}
		
		//2) Aucun utilisateur n'a été reconnu, il n'existe vraisemblablement pas.
		if (!($user = $event->getUser()))
		{
			throw new LoginException('Mauvais couple pseudo/mot de passe.');
		}
		
		//3) L'utilisateur existe mais le mot de passe est mauvais.
		if (!$this->checkPassword($data['password'], $user))
		{
			throw new LoginException('Mauvais couple pseudo/mot de passe.');
		}
		
		$this->pendingState = self::AUTHENTICATED_FULLY;
		
		return $user;
	}
	
	/**
	 * Démarre un processus de connexion depuis les données fournies par 
	 * l'environnement courant (requête, session, serveur). L'objectif ici 
	 * est de renvoyer l'entité correspondant à l'utilisateur qui souhaite se 
	 * connecter d'après les informations disponibles. La vérification des 
	 * autorisations nécessaires pour se connecter doit être faite par les 
	 * observateurs, bien qu'il ne s'agisse généralement pas d'une vérification 
	 * directe du mot de passe.
	 *
	 * Note : contrairement à self::attemptFormLogin(), une connexion par 
	 * l'environnement n'a pas à échouer bruyamment par une exception. Il 
	 * s'agit d'un processus d'arrière-plan transparent pour l'utilisateur.
	 *
	 * @param  Request $request La requête correspondante
	 * @return \Utilisateur|boolean L'entité désirant se connecter, true si 
	 *         l'utilisateur a été reconnu par sa session, false si on ne 
	 *         peut rien faire.
	 */
	public function attemptEnvLogin(Request $request)
	{
		if (!empty($_SESSION['id']) && !empty($_SESSION['state']) && $_SESSION['id'] > 0 && $_SESSION['state'] > 0)
		{
			$userId = (int) $_SESSION['id'];
			$state  = (int) $_SESSION['state'];
		}
		else
		{
			$userId = null;
			$state  = self::AUTHENTICATED_ANONYMOUSLY;
		}
		
		$event = new EnvLoginEvent($request, $userId, $state);
		$this->dispatcher->dispatch(UserEvents::ENV_LOGIN, $event);
		
		//1) Le login a été annulé, on arrête tout.
		if ($event->isAborted())
		{
			return false;
		}
		
		//2) Un utilisateur a été identifié d'après l'environnement, c'est lui 
		//que l'on va tenter de connecter.
		if ($user = $event->getUser())
		{
			$this->pendingState = $event->getState();
			
			return $user;
		}
		
		//3) On n'a pas annulé le processus et pas retourné d'autre utilisateur, 
		//on poursuit donc avec l'utilisateur détecté le cas échéant.
		if ($state > self::AUTHENTICATED_ANONYMOUSLY)
		{
			$this->entityId = $userId;
			$this->state    = $state;
			
			return true;
		}
		
		//4) Impossible de connecter l'utilisateur depuis l'environnement.
		return false;
	}
	
	/**
	 * Connecte le visiteur à un compte utilisateur donné. Celui-ci provient 
	 * généralement d'un appel précédent à self::attemptFormLogin() ou 
	 * self::attemptEnvLogin(). Toutes les vérifications d'autorisation doivent 
	 * avoir été faites à ce stage, les observateurs PRE_LOGIN doivent uniquement 
	 * faire des vérifications sur la validité du compte utilisateur.
	 *
	 * @param  \Utilisateur $user L'entité à connecter
	 * @param  boolean $remember Se souvenir de l'utilisateur ?
	 * @param  integer|null État final de l'utilisateur
	 * @throws LoginException Si le compte n'est pas autorisé à se connecter
	 */
	public function login(\Utilisateur $user, $remember = false, $state = null)
	{
		//Propagation de l'événement PRE_LOGIN. Celui-ci peut encore interrompre
		//le processus. Il est conçu pour vérifier des informations comme la 
		//conformité du compte (compte validé, non banni, etc.).
		$event = new FilterLoginEvent($user, $remember, $state);
		$this->dispatcher->dispatch(UserEvents::PRE_LOGIN, $event);
		if ($event->isAborted())
		{
			throw new LoginException($event->getErrorMessage());
		}
		
		if ($this->pendingState !== null)
		{
			$this->state = $this->pendingState;
			$this->pendingState = null;
		}
		else
		{
			$this->state = ($state !== null) ? $state : self::AUTHENTICATED_FULLY;
		}
		
		$this->entityId = $event->getUser()->getId();
		$this->reloadGroups(); //Recharge aussi l'entité.
		
		if (!$this->entity)
		{
			return;
		}
		
		$_SESSION['pseudo'] = $this->entity->getUsername();
		$_SESSION['age']	= $this->entity->getAge();
		$_SESSION['prefs']  = array();
		unset($_SESSION['last_ip']);
		
		//Ces informations sont nécessaires pour rétablir l'état de l'objet 
		//lors des futures requêtes.
		$_SESSION['id']		= $this->entityId;
		$_SESSION['state']  = $this->state;
		
		//On informe maintenant tous les observateurs que le processus de 
		//connexion s'est déroulé avec succès.
		$event = new LoginEvent($this->entity, $event->isRemember(), $this->state);
		$this->dispatcher->dispatch(UserEvents::POST_LOGIN, $event);
	}
	
	/**
	 * Déconnecte l'utilisateur de sa session. Le processus peut éventuellement 
	 * être interrompu par un observateur.
	 *
	 * @param  boolean $internal Si la source est interne les événements 
	 *         ne seront pas propagés
	 */
	public function logout($internal = false)
	{
		if (!$internal)
		{
			$remember = !empty($_COOKIE['user_id']) && !empty($_COOKIE['violon']);
			$entity   = $this->getEntity();
			$state    = $this->state;
			
			$event = new LoginEvent($entity, $state, $remember);
			$this->dispatcher->dispatch(UserEvents::PRE_LOGOUT, $event);
		}
		
		//Fermeture de la session en cours.
		$this->entity = null;
		$_SESSION   = array();
		session_destroy();
		
		//Destruction des cookies.
		setcookie('violon', '', strtotime("-1 year"), '/');
		setcookie('user_id', '', strtotime("-1 year"), '/');
		setcookie('pseudo', '', strtotime("-1 year"), '/');
		$_COOKIE['violon']  = '';
		$_COOKIE['user_id'] = '';
		$_COOKIE['pseudo']   = '';

		//Redémarrage de la session.
		session_start();
		
		if (!$internal)
		{
			//On informe maintenant tous les observateurs que le processus de 
			//déconnexion s'est déroulé avec succès.
			$event = new LoginEvent($entity, $remember, $state);
			$this->dispatcher->dispatch(UserEvents::POST_LOGOUT, $event);
		}
	}
	
	/**
	 * Recharge les informations concernant les appartenances aux groupes 
	 * pour l'utilisateur connecté.
	 *
	 * @return boolean
	 */
	public function reloadGroups()
	{
		//On recharge avant tout l'entité, au cas où le groupe ait changé.
		$this->reloadEntity();
		if ($user = $this->getEntity())
		{
			$_SESSION['groupe'] 		     = $user['groupe_id'];
			$_SESSION['groupes_secondaires'] = \Doctrine_Core::getTable('SecondaryGroup')->getByUserId($user['id']);
			
			return true;
		}
		else
		{
			$_SESSION['groupe']              = GROUPE_VISITEURS;
			$_SESSION['groupes_secondaires'] = array();
		}
		
		$_SESSION['refresh_droits'] = time();
		
		return false;
	}
	
	/**
	 * Retourne l'entité Doctrine associée à l'utilisateur courant.
	 *
	 * @return \Utilisateur|null L'entité ou null si non connecté
	 */
	public function getEntity()
	{
		if (!$this->entity && verifier('connecte'))
		{
			$this->reloadEntity();
		}
		
		return $this->entity;
	}
	
	protected function checkValue($method, $eventName, $value, \Utilisateur $user = null)
	{
		if (!$user)
		{
			$user = $this->getEntity();
		}
		
		$event = new CheckValueEvent($value, $user);
		$this->dispatcher->dispatch($eventName, $event);
		if (($isAcceptable = $event->isAcceptable()) === false)
		{
			throw new ValueException($event->getErrorMessage());
		}
		
		return $isAcceptable;
	}

	/**
	 * Recharge l'entité Doctrine associée au compte de l'utilisateur courant 
	 * depuis la base de données.
	 *
	 * @throws \RuntimeException Si l'utilisateur n'existe pas/plus
	 */
	protected function reloadEntity()
	{
		$this->entity = \Doctrine_Core::getTable('Utilisateur')->getById($this->entityId) ?: null;
		
		//Si l'utilisateur n'existe pas/plus dans la base de données.
		if (!$this->entity)
		{
			$this->logout(/* internal */ true);
		}
	}
}
