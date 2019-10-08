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

namespace Zco\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
use Zco\Bundle\DicteesBundle\Domain\DictationDAO;
use Zco\Bundle\ForumBundle\Domain\ForumDAO;
use Zco\Bundle\ForumBundle\Domain\TopicDAO;

/**
 * Affichage de la page d'accueil du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class HomeController extends Controller
{
    public function indexAction()
    {
        $registry = $this->get('zco_core.registry');
        $cache = $this->get('cache');

        \Page::$titre = 'zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !';

        // Bloc « à tout faire »
        $vars = array();
        $vars['quel_bloc'] = $registry->get('bloc_accueil');
        $vars['Informations'] = $registry->get('accueil_informations');
        $vars['QuizSemaine'] = null;
        $vars['SujetSemaine'] = null;
        $vars['BilletSemaine'] = null;
        $vars['BilletHasard'] = null;
        $vars['BilletAuteurs'] = null;
        $vars['Tweets'] = null;
        $vars['Dictee'] = null;

        if ($vars['quel_bloc'] == 'quiz') {
            $vars['QuizSemaine'] = $registry->get('accueil_quiz');
        } elseif ($vars['quel_bloc'] == 'sujet') {
            $vars['SujetSemaine'] = $registry->get('accueil_sujet');
        } elseif ($vars['quel_bloc'] == 'billet') {
            $vars['BilletSemaine'] = $registry->get('accueil_billet');
            $vars['BilletSemaine'] = BlogDAO::InfosBillet($vars['BilletSemaine']['billet_id']);
            $vars['BilletAuteurs'] = $vars['BilletSemaine'];
            $vars['BilletSemaine'] = $vars['BilletSemaine'][0];
        } elseif ($vars['quel_bloc'] == 'billet_hasard') {
            if ($billet = $cache->fetch('billet_hasard')) {
                $vars['BilletHasard'] = BlogDAO::InfosBillet($billet);
                $vars['BilletAuteurs'] = $vars['BilletHasard'];
                $vars['BilletHasard'] = $vars['BilletHasard'][0];
            } else {
                if (!$categories = $registry->get('categories_billet_hasard'))
                    $categories = array();
                $rand = BlogDAO::BilletAleatoire($categories);
                $cache->save('billet_hasard', $rand, 30 * 60); // 30 minutes
                $vars['BilletHasard'] = BlogDAO::InfosBillet($rand);
                $vars['BilletAuteurs'] = $vars['BilletHasard'];
                $vars['BilletHasard'] = $vars['BilletHasard'][0];
            }
        } elseif ($vars['quel_bloc'] == 'dictee') {
            $dictee = $registry->get('dictee_en_avant');
            $vars['Dictee'] = ($dictee) ? ($dictee) : (array());
        }

        // Blog
        list($vars['ListerBillets'], $vars['BilletsAuteurs']) = BlogDAO::ListerBillets(array(
            'etat' => BLOG_VALIDE,
            'lecteurs' => false,
            'futur' => false,
        ), -1);

        // Dictées
        $vars['DicteesAccueil'] = array_slice(DictationDAO::DicteesAccueil(), 0, 2);
        $vars['DicteeHasard'] = DictationDAO::DicteeHasard();
        $vars['DicteesLesPlusJouees'] = array_slice(DictationDAO::DicteesLesPlusJouees(), 0, 2);

        // Quiz
        $quizRepository = $this->get('zco_quiz.manager.quiz');
        $vars['ListerQuizFrequentes'] = $quizRepository->listerParFrequentation();
        $vars['ListerQuizNouveaux'] = $quizRepository->listerRecents();
        $vars['QuizHasard'] = $quizRepository->hasard();

        // Forum
        $vars['StatistiquesForum'] = ForumDAO::RecupererStatistiquesForum();

        // Inclusion de la vue
        fil_ariane('Accueil');
        $resourceManager = $this->get('zco_core.resource_manager');
        $resourceManager->requireResources([
            '@ZcoCoreBundle/Resources/public/css/home.css',
            '@ZcoCoreBundle/Resources/public/css/zcode.css',
            '@ZcoDicteesBundle/Resources/public/css/dictees.css'
        ]);

        return $this->render('ZcoCoreBundle:Home:index.html.php', $vars);
    }
}
