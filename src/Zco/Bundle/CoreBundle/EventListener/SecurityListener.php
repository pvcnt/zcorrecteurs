<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

namespace Zco\Bundle\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Effectue les vérifications liées à la sécurité de chaque action. Les 
 * paramètres sont définis par chaque module dans un fichier security.yml 
 * qui sera analysé ici.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SecurityListener extends ContainerAware implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 31),
            /* Le RouterListener par défaut a une priorité de 32, on se place juste avant */
        );
    }
    
	/**
	 * Vérifie que l'utilisateur actuel soit autorisé à accéder à la page 
	 * demandée. Ce système repose sur une configuration définie dans un fichier 
	 * security.yml devant se trouver avec les autres fichiers de configuration 
	 * du bundle.
	 *
	 * @param GetResponseEvent $event
	 */
	public function onKernelRequest(GetResponseEvent $event)
	{
	    if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType())
	    {
            return;
        }
        
        $request = $event->getRequest();
        
        //Si on n'est pas sur une page gérée par un bundle utilisant l'ancien 
		//mécanisme de routage et de sécurité, rien ne sert de continuer.
	    if (!$request->attributes->has('_bundle'))
	    {
	        return;
	    }
	    
	    $bundle = $this->container->get('kernel')->getBundle($request->attributes->get('_bundle'));
	    $action = $request->attributes->get('_action');
	    
	    //Si aucun fichier définissant les règles de sécurité n'existe, on peut 
	    //arrêter la vérification immédiatement.
		if (!is_file($bundle->getPath().'/Resources/config/security.yml'))
		{
		    return;
	    }
	    
		\Config::load('security');
		$config = \Config::get('security');
		$access = \Util::arrayDeepMerge(
			array(
				'login_required' => false,
				'anonymous_required' => false,
				'credentials' => array(),
				'deny' => false
			),
			isset($config['all']) ? $config['all'] : array(),
			isset($config[$action]) ? $config[$action] : array()
		);

		if ($access['login_required'] && !verifier('connecte'))
		{
			$event->setResponse(redirect('Vous devez être inscrit et connecté pour accéder à cette page.', 
				$this->container->get('router')->generate('zco_user_session_login'), MSG_ERROR));
		}
		elseif ($access['anonymous_required'] && verifier('connecte'))
		{
			$event->setResponse(redirect(
				'Vous ne pouvez pas accéder à cette page car vous êtes déjà connecté.', 
				'/', MSG_ERROR));
		}
		elseif (!empty($access['credentials']) && !verifier_array($access['credentials']))
		{
			throw new AccessDeniedHttpException('Accès interdit : droit insuffisants.');
		}
		elseif ($access['deny'])
		{
		    throw new AccessDeniedHttpException('Accès interdit : module désactivé.');
		}
	}
}
