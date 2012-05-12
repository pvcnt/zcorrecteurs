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

use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Observateur chargé des modifications à l'interface du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UiListener extends ContainerAware implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'zco_core.filter_menu.speedbarre'       => 'onFilterSpeedbarre',
			'zco_core.filter_menu.speedbarre_right' => 'onFilterSpeedbarreRight',
			'zco_core.filter_menu.left_menu'        => 'onFilterLeftMenu',
			AdminEvents::MENU                       => 'onFilterAdmin',
		);
	}
		
	/**
	 * Ajoute une section dédiée à la gestion des membres dans l'accueil 
	 * de l'administration.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterAdmin(FilterMenuEvent $event)
	{
		$tab = $event
			->getRoot()
			->getChild('Communauté')
			->getChild('Membres');
		
		$tasks  = $this->container->get('zco_admin.manager')->get('changementsPseudo');
		$router = $this->container->get('router');
		
		$tab->addChild('Voir les changements de pseudo en attente', array(
			'label' => 'Il y a ' . $tasks . ' changement' . pluriel($tasks) . ' de pseudo' . pluriel($tasks) . ' en attente',
			'uri' => $router->generate('zco_user_admin_newPseudoQueries'),
			'count' => $tasks,
		))->secure('membres_valider_ch_pseudos');
	
		$tab->addChild('Modifier le pourcentage d\'un membre', array(
			'uri' => $router->generate('zco_user_admin_warn'),
		))->secure('membres_avertir');
	
		$tab->addChild('Sanctionner un membre', array(
			'uri' => $router->generate('zco_user_admin_punish'),
		))->secure('sanctionner');
	
		$tab->addChild('Rechercher une adresse mail', array(
			'uri' => $router->generate('zco_user_admin_searchEmail'),
		))->secure('rechercher_mail');
	
		$tab->addChild('Voir les adresses mails bannies', array(
			'uri' => $router->generate('zco_user_admin_bannedEmails'),
		))->secure('bannir_mails');
	
		$tab->addChild('Afficher les comptes non validés', array(
			'uri' => $router->generate('zco_user_admin_unvalidAccounts'),
		))->secure('gerer_comptes_valides');
	
		$tab = $event
			->getRoot()
			->getChild('Informations')
			->getChild('Journaux');
				
		$tab->addChild('Historique des tentatives de connexion ratées', array(
			'uri' => $router->generate('zco_user_admin_blocages'),
		))->secure('lister_blocages');
	}
	
	/**
	 * Ajoute à droite de la barre de navigation le menu du profil.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterSpeedbarreRight(FilterMenuEvent $event)
	{
		if (verifier('connecte') && $event->getTemplate() === 'bootstrap')
		{
			$event->getRoot()->addChild(
				'Mon compte', array(
					'label' => 'Mon compte <b class="caret"></b>',
					'uri' => '#',
					'attributes' => array(
						'class' => 'dropdown',
					),
					'linkAttributes' => array(
						'class' => 'dropdown-toggle',
						'data-toggle' => 'dropdown',
					),
					'childrenAttributes' => array(
						'class' => 'dropdown-menu',
					),
					'weight' => 100,
			));
			$event->getRoot()->getChild('Mon compte')->addChild('Pseudo', array(
				'uri'	=> $this->container->get('router')->generate('zco_user_profile', array('id' => $_SESSION['id'], 'slug' => rewrite($_SESSION['pseudo']))),
				'label'  => htmlspecialchars($_SESSION['pseudo']),
				'weight' => 10,
				'linkAttributes' => array(
					'rel'   => 'Vous êtes actuellement connecté en tant que '.htmlspecialchars($_SESSION['pseudo']).'.', 
					'title' => 'Mon pseudo',
				),
			));
			$event->getRoot()->getChild('Mon compte')->addChild('divider-after-profile', array(
				'label' => '',
				'attributes' => array('class' => 'divider'),
				'weight' => 11,
			));
			$event->getRoot()->getChild('Mon compte')->addChild('divider-before-logout', array(
				'label' => '',
				'attributes' => array('class' => 'divider'),
				'weight' => 99,
			));
			$event->getRoot()->getChild('Mon compte')->addChild('Déconnexion', array(
				'uri' => $this->container->get('router')->generate('zco_user_session_logout', array('token' => $_SESSION['token'])),
				'weight' => 100,
			));
		}
	}
	
	/**
	 * Ajoute dans la barre de navigation des liens pour se connecter et s'inscrire.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterSpeedbarre(FilterMenuEvent $event)
	{
		if (!verifier('connecte'))
		{
			$event
				->getRoot()
				->addChild('Connexion', array(
					'uri'	=> $this->container->get('router')->generate('zco_user_session_login'),
					'weight' => 50,
					'linkAttributes' => array(
						'title' => 'Renseignez votre nom d\'utilisateur et votre mot de passe pour vous connecter',
					),
				));
		
			$event
				->getRoot()
				->addChild('Créer un compte', array(
					'uri'	=> $this->container->get('router')->generate('zco_user_session_register'),
					'weight' => 60,
					'linkAttributes' => array(
						'title' => 'Inscrivez-vous pour profiter de toutes les fonctions du site !',
					),
				));
		}
	}
	
	/**
	 * Ajoute dans le menu latéral tous les liens liés à l'affichage des 
	 * listes de membres du site et de leurs profils.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterLeftMenu(FilterMenuEvent $event)
	{
		if (verifier('connecte') && $event->getTemplate() === 'legacy')
		{
			$event->getRoot()->getChild('Mon compte')->addChild('Pseudo', array(
				'uri'	=> $this->container->get('router')->generate('zco_user_profile', array('id' => $_SESSION['id'], 'slug' => rewrite($_SESSION['pseudo']))),
				'label'  => htmlspecialchars($_SESSION['pseudo']),
				'weight' => 0,
				'linkAttributes' => array(
					'rel'   => 'Vous êtes actuellement connecté en tant que '.htmlspecialchars($_SESSION['pseudo']).'.', 
					'title' => 'Mon pseudo',
				),
			));
			
			$event->getRoot()->getChild('Mon compte')->addChild('Déconnexion', array(
				'uri'	 => $this->container->get('router')->generate('zco_user_session_logout', array('token' => $_SESSION['token'])),
				'weight' => 40,
				'linkAttributes' => array(
					'rel'   => 'Cliquez ici pour vous déconnecter.', 
					'title' => 'Déconnexion',
				),
			));
		}
		
		$event->getRoot()->getChild('Communauté')->addChild('Membres', array(
			'uri'	=> $this->container->get('router')->generate('zco_user_index'),
			'weight' => 40,
			'linkAttributes' => array(
				'rel'   => 'Découvrez la liste des membres de ce site.', 
				'title' => 'Membres',
			),
		));
		
		$connectes = $this->container->get('zco_core.cache')->get('nb_connectes');
		$event->getRoot()->getChild('Communauté')->addChild('Connectés', array(
			'uri'	=> $this->container->get('router')->generate('zco_user_online'),
			'weight' => 50,
			'label' => $connectes.' connecté'.pluriel($connectes),
			'linkAttributes' => array(
				'rel'   => 'Quels sont les membres actuellement connectés sur le site ?', 
				'title' => 'Connectés',
			),
		));
		
		/*if (!verifier('connecte'))
		{
			// Sinon formulaire de connexion rapide si la personne n'est pas connectée.
			$event->getRoot()->getChild('Mon compte')->setHtml('<form id="sidebarLogin" method="post" class="connexion" action="'.$this->container->get('router')->generate('zco_user_session_login').'">', 'prefix');
			$event->getRoot()->getChild('Mon compte')->setHtml('</form>', 'suffix');
			
			$event->getRoot()->getChild('Mon compte')->addChild('Nom d\'utilisateur')
				 ->setHtml('<input type="text" name="utilisateur" id="sideLogin" />');
		
			$event->getRoot()->getChild('Mon compte')->addChild('Mot de passe')
				 ->setHtml('<input type="password" name="mot_de_passe" id="sidePassword" />');
		
			$event->getRoot()->getChild('Mon compte')->addChild('Connexion automatique')
				 ->setHtml('<input name="connexion_auto" id="sideConnexion_auto" type="checkbox" checked="checked" size="1" style="width: auto; display: inline;"/> Automatique');
		
			$event->getRoot()->getChild('Mon compte')->addChild('Se connecter')
				 ->setHtml('<input id="sideSubmit" class="btn" type="submit" value="Se connecter" />');
		}*/
	}
}