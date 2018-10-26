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

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\UserBundle\Form\Type\FormLoginType;

/**
 * Observateur chargé des modifications à l'interface du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UiListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            AdminEvents::MENU => 'onFilterAdmin',
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
        $urlGenerator = $this->container->get('router');
        $tab = $event->getRoot()->getChild('Communauté')->getChild('Membres');

        $tasks = $this->container->get('zco_admin.manager')->get('changementsPseudo');
        $tab->addChild('Voir les changements de pseudo en attente', array(
            'label' => 'Il y a ' . $tasks . ' changement' . pluriel($tasks) . ' de pseudo' . pluriel($tasks) . ' en attente',
            'uri' => $urlGenerator->generate('zco_user_admin_newPseudoQueries'),
            'count' => $tasks,
        ))->secure('membres_valider_ch_pseudos');

        $tab->addChild('Sanctionner un membre', array(
            'uri' => $urlGenerator->generate('zco_user_admin_punish'),
        ))->secure('sanctionner');

        $tab->addChild('Rechercher une adresse mail', array(
            'uri' => $urlGenerator->generate('zco_user_admin_searchEmail'),
        ))->secure('rechercher_mail');

        $tab->addChild('Voir les adresses mails bannies', array(
            'uri' => $urlGenerator->generate('zco_user_admin_bannedEmails'),
        ))->secure('bannir_mails');

        $tab->addChild('Afficher les comptes non validés', array(
            'uri' => $urlGenerator->generate('zco_user_admin_unvalidAccounts'),
        ))->secure('gerer_comptes_valides');

        $tab = $event->getRoot()->getChild('Informations')->getChild('Journaux');

        $tab->addChild('Historique des tentatives de connexion ratées', array(
            'uri' => $urlGenerator->generate('zco_user_admin_blocages'),
        ))->secure('lister_blocages');

        $tab = $event->getRoot()->getChild('Communauté')->getChild('Adresses IP');

        $tab->addChild('Liste des adresses IP bannies', array(
            'uri' => $urlGenerator->generate('zco_user_ips_index'),
        ))->secure('ips_bannir');

        $tab->addChild('Analyser une adresse IP', array(
            'uri' => $urlGenerator->generate('zco_user_ips_analyze'),
        ))->secure('ips_analyser');

        $tab->addChild('Afficher les doublons d\'adresses IP', array(
            'uri' => $urlGenerator->generate('zco_user_ips_duplicates'),
        ))->secure('ips_analyser');
    }
}