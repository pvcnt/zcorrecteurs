<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

namespace Zco\Bundle\IpsBundle\EventListener;

use Zco\Bundle\CoreBundle\CoreEvents;
use Zco\Bundle\CoreBundle\Event\CronEvent;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Observateur principal pour le module d'adresses IP.
 */
class EventListener extends ContainerAware implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			KernelEvents::REQUEST  => 'onKernelRequest',
			AdminEvents::MENU      => 'onFilterAdmin',
			CoreEvents::DAILY_CRON => 'onDailyCron',
		);
	}
	
	
	/**
	 * Observe "core.kernel.request". Vérifie que l'adresse IP de 
	 * l'utilisateur ne soit pas dans la liste des adresses bannies. 
	 * Si tel est le cas, termine l'application en affichant une page 
	 * de bannissement.
	 *
	 * @param GetResponseEvent $event
	 */
	public function onKernelRequest(GetResponseEvent $event)
	{
		$cache = $this->container->get('zco_core.cache');
		$ip    = ip2long($this->container->get('request')->getClientIp(true));
		$ips   = $cache->get('ips_bannies');
		if ($ips === false)
		{
			$ips = array();
			$dbh = \Doctrine_Manager::connection()->getDbh();
			$stmt = $dbh->prepare("SELECT ip_ip FROM zcov2_ips_bannies WHERE ip_fini = 0");
			$stmt->execute();
			$retour = $stmt->fetchAll();
			$stmt->closeCursor();
			if (!empty($retour))
			{
				foreach ($retour as $cle => $valeur)
				{
					$ips[] = $valeur['ip_ip'];
				}
			}
			$cache->set('ips_bannies', $ips, 0);
		}

		if (in_array($ip, $ips))
		{
			// On récupére la raison et la durée du bannissement
			$dbh = \Doctrine_Manager::connection()->getDbh();
			$stmt = $dbh->prepare("SELECT ip_raison, ip_duree_restante, ip_date " .
					"FROM zcov2_ips_bannies " .
					"WHERE ip_ip = :ip");
			$stmt->bindParam(':ip', $ip);
			$stmt->execute();
			$retour = $stmt->fetch(\PDO::FETCH_ASSOC);

			if (!empty($retour))
			{
				$Raison = $retour['ip_raison'];
				$Duree = $retour['ip_duree_restante'];
				$Debut = $retour['ip_date'];

				$_SESSION = array();
				session_destroy();
				
				$event->setResponse(new Response(render_to_string('ZcoIpsBundle::banni.html.php', compact('Debut', 'Duree', 'Raison'))));
			}
		}
	}
	
	/**
	 * Ajoute des liens dans le panneau d'administration.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterAdmin(FilterMenuEvent $event)
	{
		$tab = $event
		    ->getRoot()
		    ->getChild('Communauté')
		    ->getChild('Adresses IP');

		$tab->addChild('Liste des adresses IP bannies', array(
			'uri' => '/ips/',
		))->secure('ips_voir_bannies');
		
	    $tab->addChild('Analyser une adresse IP', array(
			'uri' => '/ips/analyser.html',
		))->secure('ips_analyser');
		
		$tab->addChild('Afficher les doublons d\'adresses IP', array(
			'uri' => '/ips/doublons.html',
		))->secure('ips_analyser');
	}

	/**
	 * Actions à exécuter chaque jour.
	 *
	 * @param CronEvent $event
	 */
	public function onDailyCron(CronEvent $event)
	{
		$dbh = \Doctrine_Manager::connection()->getDbh();

		//Mise à jour des bannissements d'adresses IP.
		$stmt = $dbh->prepare("UPDATE zcov2_ips_bannies
		SET ip_duree_restante = ip_duree_restante - 1
		WHERE ip_fini = 0 AND ip_duree_restante > 0 AND (ip_date + INTERVAL (ip_duree - ip_duree_restante) DAY < NOW())");
		$stmt->execute();
		$stmt->closeCursor();

		$stmt = $dbh->prepare("UPDATE zcov2_ips_bannies
		SET ip_fini = 1
		WHERE ip_duree > 0 AND ip_duree_restante = 0");
		$stmt->execute();
		$stmt->closeCursor();

		$this->container->get('zco_core.cache')->delete('ips_bannies');
	}
}