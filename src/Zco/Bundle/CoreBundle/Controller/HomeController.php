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
use Symfony\Component\Routing\Annotation\Route;
use Zco\Bundle\ContentBundle\Domain\BlogDAO;
use Zco\Bundle\ContentBundle\Domain\DictationDAO;
use Zco\Bundle\ContentBundle\Entity\QuizManager;

/**
 * Affichage de la page d'accueil du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class HomeController extends Controller
{
    /**
     * @Route(name="zco_home", path="/")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        \Zco\Page::$titre = 'zCorrecteurs.fr - Les réponses à toutes vos questions concernant la langue française !';
        $vars = array();

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
        $quizRepository = $this->get(QuizManager::class);
        $vars['ListerQuizFrequentes'] = $quizRepository->listerParFrequentation();
        $vars['ListerQuizNouveaux'] = $quizRepository->listerRecents();
        $vars['QuizHasard'] = $quizRepository->hasard();

        fil_ariane('Accueil');
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/home.css',
            '@ZcoCoreBundle/Resources/public/css/zcode.css',
            '@ZcoContentBundle/Resources/public/css/dictees.css'
        ]);

        return $this->render('ZcoCoreBundle:Home:index.html.php', $vars);
    }
}
