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

use Zco\Bundle\UserBundle\UserEvents;
use Zco\Bundle\UserBundle\Event\LoginEvent;
use Zco\Bundle\UserBundle\Event\FilterLoginEvent;
use Zco\Bundle\UserBundle\Event\FormLoginEvent;
use Zco\Bundle\UserBundle\Event\EnvLoginEvent;
use Zco\Bundle\UserBundle\Event\CheckPasswordEvent;
use Zco\Bundle\UserBundle\User\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Observateur lié intégrations les actions de connexion et déconnexion au 
 * reste du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class LoginListener extends ContainerAware implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			UserEvents::FORM_LOGIN  => 'onFormLogin',
			UserEvents::ENV_LOGIN   => 'onEnvLogin',
			UserEvents::PRE_LOGIN   => 'onPreLogin',
			UserEvents::POST_LOGIN  => 'onPostLogin',
			UserEvents::POST_LOGOUT => 'onPostLogout',
		);
	}
	
	/**
	 * Tente de connecter l'utilisateur grâce aux informations stockées dans 
	 * ses cookies (présents s'il a choisi la connexion automatique).
	 *
	 * @param EnvLoginEvent $event
	 */
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
	
	/**
	 * Tente de connecter l'utilisateur suite à une soumission de formulaire.
	 *
	 * @param FormLoginEvent $event
	 */
	public function onFormLogin(FormLoginEvent $event)
	{
		$data = $event->getData();
		if (($user = \Doctrine_Core::getTable('Utilisateur')->getOneByPseudo($data['pseudo'])) !== false)
		{
			$event->setUser($user);
		}
		
		$tentatives = \Doctrine_Core::getTable('Tentative')->countByIp(ip2long($event->getRequest()->getClientIp()));
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
	
	/**
	 * Vérifie que l'utilisateur souhaitant se connecter ne soit pas banni et 
	 * que son compte ait bien été activé.
	 * 
	 * @param FilterLoginEvent $event
	 */
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
	
	/**
	 * Supprime toutes les tentatives de connexion ratées et dépose les cookies 
	 * nécessaires à une future connexion automatique après une connexion 
	 * réalisée avec succès.
	 * 
	 * @param LoginEvent $event
	 */
	public function onPostLogin(LoginEvent $event)
	{
		\Doctrine_Core::getTable('Tentative')->deleteByUserIdAndIp(
			$event->getUser()->getId(), ip2long($this->container->get('request')->getClientIp()));
		
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
	
	/**
	 * Génère une clé qui sera stockée dans les cookies du visiteur afin de 
	 * se souvenir de lui lors de sa prochaine visite et prouver son identité.
	 *
	 * @param  Utilisateur $user
	 * @return string
	 */
	private function generateRememberKey(\Utilisateur $user)
	{
		$browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		
		return sha1($browser.$user->getUsername().$user->getPassword().'ezgnmlwxsainymktiwuv');
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
}
}*/