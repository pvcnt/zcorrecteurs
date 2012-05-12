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

namespace Zco\Bundle\UserBundle\EventListener;

use Zco\Bundle\UserBundle\UserEvents;
use Zco\Bundle\UserBundle\Event\LoginEvent;
use Zco\Bundle\UserBundle\Event\FilterLoginEvent;
use Zco\Bundle\UserBundle\Event\FormLoginEvent;
use Zco\Bundle\UserBundle\Event\EnvLoginEvent;
use Zco\Bundle\UserBundle\Event\CheckPasswordEvent;
use Zco\Bundle\UserBundle\User\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class LoginListener extends ContainerAware implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			UserEvents::FORM_LOGIN => 'onFormLogin',
			UserEvents::ENV_LOGIN => 'onEnvLogin',
			UserEvents::PRE_LOGIN => 'onPreLogin',
			UserEvents::POST_LOGIN => 'onPostLogin',
			UserEvents::POST_LOGOUT => 'onPostLogout',
		);
	}
	
	public function onEnvLogin(EnvLoginEvent $event)
	{
		if ($event->getState() > User::AUTHENTICATED_ANONYMOUSLY)
		{
			return;
		}
		
		$request = $event->getRequest();
		if (!$request->cookies->has('user_id') || !$request->cookies->has('violon'))
		{
			return;
		}
		
		$user = \Doctrine_Core::getTable('Utilisateur')->getById($event->getRequest()->cookies->get('user_id'));
		if ($user && $request->cookies->get('violon') === $this->generateRememberKey($user))
		{
			$event->setUser($user, User::AUTHENTICATED_REMEMBERED);
		}
	}
	
	public function onFormLogin(FormLoginEvent $event)
	{
		$data = $event->getData();
		if (($user = \Doctrine_Core::getTable('Utilisateur')->getOneByPseudo($data['pseudo'])) !== false)
		{
			$event->setUser($user);
		}
		
		$tentatives = \Doctrine_Core::getTable('Tentative')->countByIp(
			ip2long($event->getRequest()->getClientIp()));
		if ($tentatives >= 5)
		{
			$this->captcha = true;
			$this->blocage = true;
		}
		elseif ($tentatives >= 3)
		{
			$this->captcha = true;
			$this->blocage = false;
		}
		else
		{
			$this->captcha = false;
			$this->blocage = false;
		}
	}
	
	public function onPreLogin(FilterLoginEvent $event)
	{
		if (!verifier('connexion', 0, $event->getUser()->getGroupId()))
		{
			$event->abort('Vous êtes banni du site et ne pouvez pas conséquent plus vous connecter à votre compte.');
		}
		if (!$event->getUser()->isAccountValid())
		{
			$event->abort('Votre compte est pour l\'instant inactif. Vous avez reçu un courriel comportant un lien de validation du compte.');
		}
	}
	
	public function onPostLogin(LoginEvent $event)
	{
		//Supprime toutes les tentatives de connexion ratées pour cet utilisateur.
		\Doctrine_Core::getTable('Tentative')->deleteByUserIdAndIp(
			$event->getUser()->getId(), ip2long($this->container->get('request')->getClientIp()));
		
		//Dépose les cookies nécessaires à une future connexion automatique.
		if ($event->isRemember())
		{
			setcookie('violon', $this->generateRememberKey($event->getUser()), strtotime("+1 year"), '/');
			setcookie('user_id', $event->getUser()->getId(), strtotime("+1 year"), '/');
		}
	}
	
	/**
	 * Supprime l'utilisateur de la table des connectés.
	 *
	 * @param LoginEvent $event
	 */
	public function onPostLogout(LoginEvent $event)
	{
		\Doctrine_Core::getTable('Online')->deleteByUserId($event->getUser()->getId());
	}
	
	private function generateRememberKey(\Utilisateur $user)
	{
		$browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		
		return sha1($browser.$user['pseudo'].$user['mot_de_passe'].'ezgnmlwxsainymktiwuv');
	}
}


/*$blocages = Doctrine_Query::create()
	->from('Tentative t')
	->leftJoin('t.Utilisateur u')
	->addWhere('t.user = ?', $user->id)
	->addWhere('t.ip = ?', ip2long(User::getIp()))
	->addWhere('t.blocage = 1')
	->addWhere('DATE_SUB(CURDATE(), INTERVAL 1 DAY) <= t.date')
	->count();

if($blocages != 0)
{
	return redirect(12, '/', MSG_ERROR);
}

if ($captcha && !Captcha::verifier($_POST['captcha']))
{
	return redirect('Le code que vous avez entré est invalide.', 
		$this->generateUrl('zco_user_session_login'), MSG_ERROR);
}
if (false !== (list($id, $groupe, $user_pseudo, $user_mdp, $age) = Connexion($_POST['utilisateur'], sha1($_POST['mot_de_passe']))))
{
}
else
{
	unset($_SESSION['id'], $_SESSION['pseudo'],
		$_SESSION['groupe'], $_SESSION['groupes_secondaires'],
		$_SESSION['refresh_droits']);
	$user = Doctrine_Query::create()
		->from('Utilisateur u')
		->where('u.pseudo = ?', $_POST['utilisateur'])
		->fetchOne();
	$tentative = new Tentative;
	$tentative->ip = ip2long(User::getIp());
	$tentative->user = $user->id;
	if($blocage)
	{
		$tentative->blocage = 1;
		$participants = array($user->id);
		AjouterMPAuto('Tentative de connexion', '', $participants,
			"Bonjour,\n\nQuelqu'un a tenté de se connecter à votre compte.\n"
			."Son IP est " . User::getIp() . "\n\nCordialement,\nzGardien.");
	}
	$tentative->save();
	
	return redirect('Couple pseudonyme/mot de passe inexistant !', 
		$this->generateUrl('zco_user_session_login'), MSG_ERROR);
}
}*/