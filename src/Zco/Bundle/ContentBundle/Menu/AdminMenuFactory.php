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

namespace Zco\Bundle\ContentBundle\Menu;

use Symfony\Component\Routing\RouterInterface;
use Zco\Bundle\ContentBundle\Admin\Admin;
use Zco\Bundle\ForumBundle\Admin\ForumAlertsPendingTask;
use Zco\Bundle\UserBundle\Admin\NewUsernamePendingTask;

final class AdminMenuFactory
{
    private $router;
    private $admin;

    /**
     * Constructor.
     * 
     * @param RouterInterface $router
     * @param Admin $admin
     */
    public function __construct(RouterInterface $router, Admin $admin)
    {
        $this->router = $router;
        $this->admin = $admin;
    }

    public function createMenu()
    {
        $menu = new MenuItem('Administration');
        
        if (verifier('cats_editer')) {
            $menu->getChild('Catégories')->addChild('Gérer les catégories', [
                'uri' => $this->router->generate('zco_categories_index'),
            ]);
        }
        if (verifier('citations_modifier')) {
            $menu->getChild('Citations')->addChild('Gérer les citations', [
                'uri' => $this->router->generate('zco_quote_index'),
            ]);
        }
        if (verifier('voir_stats_generales')) {
            $section = $menu->getChild('Statistiques générales');
            $section->addChild('Statistiques générales (GA)', array(
                'uri' => 'https://www.google.com/analytics/reporting/dashboard?id=6978501&scid=1725896',
            ));
            $section->addChild('Statistiques d\'utilisation du quiz', array(
                'uri' => $this->router->generate('zco_quiz_stats'),
            ));
            $section->addChild('Statistiques de popularité des quiz', array(
                'uri' => $this->router->generate('zco_quiz_popularity'),
            ));
        }
        if (verifier('groupes_gerer')) {
            $menu->getChild('Membres')->addChild('Gérer les groupes', array(
                'uri' => $this->router->generate('zco_groups_index'),
            ));
        }
        if (verifier('gerer_breve_accueil')) {
            $menu->getChild('Communication')->addChild('Modifier les annonces de la page d\'accueil', array(
                'uri' => $this->router->generate('zco_home_config'),
            ));
        }
        if (verifier('quiz_ajouter')) {
            $menu->getChild('Quiz')->addChild('Gérer les quiz', array(
                'uri' => $this->router->generate('zco_quiz_admin'),
            ));
        }
        if (verifier('dictees_publier')) {
            $menu->getChild('Quiz')->addChild('Gérer les dictées', array(
                'uri' => $this->router->generate('zco_dictation_admin'),
            ));
        }
        if (verifier('rechercher_mail')) {
            $menu->getChild('Membres')->addChild('Rechercher une adresse mail', array(
                'uri' => $this->router->generate('zco_user_admin_searchEmail'),
            ));
            $menu->getChild('Membres')->addChild('Voir tous les membres', array(
                'uri' => $this->router->generate('zco_user_index'),
            ));
        }
        if (verifier('gerer_comptes_valides')) {
            $menu->getChild('Membres')->addChild('Afficher les comptes non validés', array(
                'uri' => $this->router->generate('zco_user_admin_invalidAccounts'),
            ));
        }

        return $menu;
    }
}