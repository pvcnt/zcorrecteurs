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

namespace Zco\Bundle\AdminBundle\Controller;

use Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\AdminBundle\Menu\AdminRenderer;
use Zco\Bundle\AdminBundle\Menu\MenuItem;
use Zco\Bundle\BlogBundle\Admin\ArticlesPendingTask;
use Zco\Bundle\DicteesBundle\Admin\DictationsPendingTask;
use Zco\Bundle\ForumBundle\Admin\ForumAlertsPendingTask;
use Zco\Bundle\MpBundle\Admin\PmAlertsPendingTask;
use Zco\Bundle\RecrutementBundle\Admin\ApplicationsPendingTask;
use Zco\Bundle\UserBundle\Admin\NewUsernamePendingTask;

/**
 * Contrôleur gérant l'accueil de l'administration pour les membres de l'équipe.
 * Gère la division de l'espace en onglets, avec dans chaque onglet des blocs, en
 * fonction des droits des utilisateurs.
 *
 * @author Zopieux
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @author mwsaz <mwsaz.fr>
 */
class DefaultController extends Controller
{
    public function indexAction()
    {
        if (!verifier('admin')) {
            throw new AccessDeniedHttpException();
        }

        Page::$titre = 'Accueil de l\'administration';

        $this->get('zco.admin')->refreshAll();

        $menu = $this->createMenu();
        $renderer = new AdminRenderer();

        return render_to_response('ZcoAdminBundle::index.html.php', [
            'admin' => $renderer->render($menu),
        ]);
    }

    private function createMenu()
    {
        $menu = new MenuItem('Administration');
        $router = $this->get('router');
        $admin = $this->container->get('zco.admin');

        if (verifier('cats_editer')) {
            $menu->getChild('Catégories')->addChild('Gérer les catégories', [
                'uri' => '/categories/',
            ]);
        }
        if (verifier('dictees_publier')) {
            $count = $admin->get(DictationsPendingTask::class);
            $menu->getChild('Dictées')->addChild('Voir les dictées proposées', [
                'label' => 'Il y a ' . $count . ' dictée' . pluriel($count) . ' proposée' . pluriel($count),
                'uri' => '/dictees/propositions.html',
                'count' => $count,
            ]);
        }
        if (verifier('citations_modifier')) {
            $menu->getChild('Citations')->addChild('Gérer les citations', [
                'uri' => $router->generate('zco_quote_index'),
            ]);
        }
        if (verifier('voir_alertes')) {
            $count = $admin->get(ForumAlertsPendingTask::class);
            $menu->getChild('Forums')->addChild('Voir les alertes non résolues', [
                'label' => 'Il y a ' . $count . ' alerte non résolue' . pluriel($count),
                'uri' => '/forum/alertes.html',
                'count' => $count,
            ]);
        }
        if (verifier('mettre_sujets_coup_coeur')) {
            $menu->getChild('Forums')->addChild('Gérer les sujets en coup de cœur', [
                'uri' => '/forum/sujets-coups-coeur.html',
            ]);
        }
        if (verifier('voir_stats_generales')) {
            $section = $menu->getChild('Statistiques générales');
            $section->addChild('Statistiques générales (GA)', array(
                'uri' => 'https://www.google.com/analytics/reporting/dashboard?id=6978501&scid=1725896',
            ));
            $section->addChild('Statistiques Alexa (classement du site)', array(
                'uri' => $router->generate('zco_stats_alexa'),
            ));
            $section->addChild('Statistiques d\'inscription', array(
                'uri' => $router->generate('zco_stats_registration'),
            ));
            $section->addChild('Statistiques de géolocalisation', array(
                'uri' => $router->generate('zco_stats_location'),
            ));
            $section->addChild('Âge des membres', array(
                'uri' => $router->generate('zco_stats_ages'),
            ));
            $section->addChild('Statistiques temporelles du forum', [
                'uri' => '/forum/statistiques-temporelles.html',
            ]);
            $section->addChild('Statistiques d\'utilisation du quiz', array(
                'uri' => $router->generate('zco_quiz_stats'),
            ));
            $section->addChild('Statistiques de popularité des quiz', array(
                'uri' => $router->generate('zco_quiz_popularity'),
            ));
        }
        if (verifier('groupes_gerer')) {
            $menu->getChild('Groupes')->addChild('Gérer les groupes', array(
                'uri' => '/groupes/',
            ));
        }
        if (verifier('groupes_changer_droits')) {
            $menu->getChild('Groupes')->addChild('Éditer les droits d\'un groupe', array(
                'uri' => '/groupes/droits.html',
            ));
        }
        if (verifier('droits_gerer')) {
            $menu->getChild('Groupes')->addChild('Gérer les droits', array(
                'uri' => '/groupes/gestion-droits.html',
            ));
        }
        if (verifier('groupes_changer_membre')) {
            $menu->getChild('Groupes')->addChild('Recharger le cache des droits et les groupes', array(
                'uri' => '/groupes/recharger-droits.html',
            ));
            $menu->getChild('Groupes')->addChild('Changer un membre de groupe', array(
                'uri' => '/groupes/changer-membre-groupe.html',
            ));
        }
        if (verifier('groupes_changer_membre')) {
            $menu->getChild('Journaux')->addChild('Historique des changements de groupe', array(
                'uri' => '/groupes/historique-groupes.html',
            ));
        }
        if (verifier('mp_alertes')) {
            $count = $admin->get(PmAlertsPendingTask::class);
            $menu->getChild('Messagerie privée')->addChild('Voir les alertes non résolues', array(
                'label' => 'Il y a ' . $count . ' alerte' . pluriel($count) . ' non résolue' . pluriel($count),
                'uri' => '/mp/alertes.html' . ($count ? '?solved=0' : ''),
                'count' => $count,
            ));
        }
        if (verifier('gerer_breve_accueil')) {
            $menu->getChild('Communication')->addChild('Modifier les annonces de la page d\'accueil', array(
                'uri' => $router->generate('zco_home_config'),
            ));
        }
        if (verifier('quiz_ajouter') || verifier('quiz_editer') || verifier('quiz_supprimer')) {
            $menu->getChild('Quiz')->addChild('Gérer les quiz', array(
                'uri' => $router->generate('zco_quiz_admin'),
            ));
        }
        if (verifier('recrutements_voir_candidatures')) {
            $count = $admin->get(ApplicationsPendingTask::class);
            $menu->getChild('Recrutements')->addChild('Voir les candidatures en attente', array(
                'label' => 'Il y a ' . $count . ' candidature' . pluriel($count) . ' en attente',
                'uri' => '/recrutement/gestion.html',
                'count' => $count,
            ));
        }
        if (verifier('blog_voir_billets_proposes')) {
            $count = $admin->get(ArticlesPendingTask::class);
            $menu->getChild('Blog')->addChild('Voir les billets proposés', array(
                'label' => 'Il y a ' . $count . ' billet' . pluriel($count) . ' proposé' . pluriel($count),
                'uri' => '/blog/propositions.html',
                'count' => $count,
            ));
        }
        if (verifier('blog_voir_billets_redaction')) {
            $menu->getChild('Blog')->addChild('Voir les billets en cours de rédaction', array(
                'uri' => '/blog/brouillons.html',
            ));
        }
        if (verifier('blog_voir_refus')) {
            $menu->getChild('Blog')->addChild('Voir les billets refusés', array(
                'uri' => '/blog/refus.html',
            ));
        }
        if (verifier('blog_supprimer') || verifier('blog_editer_valide')) {
            $menu->getChild('Blog')->addChild('Voir les billets en ligne', array(
                'uri' => '/blog/gestion.html'
            ));
        }

        if (verifier('membres_valider_ch_pseudos')) {
            $count = $admin->get(NewUsernamePendingTask::class);
            $menu->getChild('Membres')->addChild('Voir les changements de pseudo en attente', array(
                'label' => 'Il y a ' . $count . ' changement' . pluriel($count) . ' de pseudo' . pluriel($count) . ' en attente',
                'uri' => $router->generate('zco_user_admin_newPseudoQueries'),
                'count' => $count,
            ));
        }
        if (verifier('sanctionner')) {
            $menu->getChild('Membres')->addChild('Sanctionner un membre', array(
                'uri' => $router->generate('zco_user_admin_punish'),
            ));
        }
        if (verifier('rechercher_mail')) {
            $menu->getChild('Membres')->addChild('Rechercher une adresse mail', array(
                'uri' => $router->generate('zco_user_admin_searchEmail'),
            ));
        }
        if (verifier('bannir_mails')) {
            $menu->getChild('Membres')->addChild('Voir les adresses mails bannies', array(
                'uri' => $router->generate('zco_user_admin_bannedEmails'),
            ));
        }
        if (verifier('gerer_comptes_valides')) {
            $menu->getChild('Membres')->addChild('Afficher les comptes non validés', array(
                'uri' => $router->generate('zco_user_admin_invalidAccounts'),
            ));
        }
        if (verifier('lister_blocages')) {
            $menu->getChild('Journaux')->addChild('Historique des tentatives de connexion ratées', array(
                'uri' => $router->generate('zco_user_admin_blocages'),
            ));
        }
        if (verifier('ips_bannir')) {
            $menu->getChild('Adresses IP')->addChild('Liste des adresses IP bannies', array(
                'uri' => $router->generate('zco_user_ips_index'),
            ));
        }
        if (verifier('ips_analyser')) {
            $menu->getChild('Adresses IP')->addChild('Analyser une adresse IP', array(
                'uri' => $router->generate('zco_user_ips_analyze'),
            ));
            $menu->getChild('Adresses IP')->addChild('Afficher les doublons d\'adresses IP', array(
                'uri' => $router->generate('zco_user_ips_duplicates'),
            ));
        }

        return $menu;
    }
}
