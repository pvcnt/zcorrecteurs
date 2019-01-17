<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

namespace Zco\Bundle\PagesBundle\Controller;

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
                $cache->save('billet_hasard', $rand, TEMPS_BILLET_HASARD * 60);
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
            '@ZcoPagesBundle/Resources/public/css/home.css',
            '@ZcoCoreBundle/Resources/public/css/zcode.css',
            '@ZcoDicteesBundle/Resources/public/css/dictees.css'
        ]);

        return $this->render('ZcoPagesBundle:Home:index.html.php', $vars);
    }

    /**
     * Action permettant l'édition des annonces en page d'accueil.
     */
    public function configAction()
    {
        if (!verifier('gerer_breve_accueil')) {
            throw new AccessDeniedHttpException();
        }

        $registry = $this->get('zco_core.registry');

        if (!empty($_POST))
            $this->get('cache')->save('accueil_maj', date('c'), 0);

        //--- Si on veut modifier le bloc ---
        $bloc_accueil = $registry->get('bloc_accueil');
        if (isset($_POST['choix_bloc'])) {
            $registry->set('bloc_accueil', $_POST['choix_bloc']);
            return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));;
        }

        //--- Cas de l'annonce personnalisée ---
        $texte_zform = $registry->get('accueil_informations');
        if (isset($_POST['texte'])) {
            $registry->set('accueil_informations', $_POST['texte']);
            return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
        }

        //Cas du sujet mis en valeur
        $infos_sujet = $registry->get('accueil_sujet');
        if (empty($infos_sujet)) $infos_sujet = array();
        $image_sujet = array_key_exists('image', $infos_sujet) ? $infos_sujet['image'] : '';

        if (!empty($_POST['sujet'])) {
            $choix_sujets = ForumDAO::ListerSujetsTitre($_POST['sujet']);
            if (count($choix_sujets) == 1) {
                $sujet = array(
                    'sujet_id' => $choix_sujets[0]['sujet_id'],
                    'sujet_titre' => $choix_sujets[0]['sujet_titre'],
                    'sujet_sous_titre' => $choix_sujets[0]['sujet_sous_titre'],
                    'cat_id' => $choix_sujets[0]['cat_id'],
                    'cat_nom' => $choix_sujets[0]['cat_nom'],
                    'image' => $image_sujet,
                );
                $registry->set('accueil_sujet', $sujet);
                return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
            }
        }
        if (!empty($_GET['sujet']) && is_numeric($_GET['sujet'])) {
            $sujet = TopicDAO::InfosSujet($_GET['sujet']);
            if (!empty($sujet)) {
                $cat = CategoryDAO::InfosCategorie($sujet['sujet_forum_id']);
                $sujet = array(
                    'sujet_id' => $sujet['sujet_id'],
                    'sujet_titre' => $sujet['sujet_titre'],
                    'sujet_sous_titre' => $sujet['sujet_sous_titre'],
                    'cat_id' => $sujet['sujet_forum_id'],
                    'cat_nom' => $cat['cat_nom'],
                    'image' => $image_sujet,
                );
                $registry->set('accueil_sujet', $sujet);
                return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
            }
        }
        if (isset($_POST['image_sujet'])) {
            $infos_sujet['image'] = $_POST['image_sujet'];
            $registry->set('accueil_sujet', $infos_sujet);
            return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
        }

        //--- Cas du quiz mis en valeur ---
        $infos_quiz = $registry->get('accueil_quiz');
        if (empty($infos_quiz)) $infos_quiz = array();
        $image_quiz = array_key_exists('image', $infos_quiz) ? $infos_quiz['image'] : '';
        $quizRepository = $this->get('zco_quiz.manager.quiz');

        if (!empty($_POST['quiz'])) {
            $choix_quiz = $quizRepository->findByNom($_POST['quiz']);
            if (count($choix_quiz) == 1) {
                $quiz = array(
                    'id' => $choix_quiz[0]['id'],
                    'nom' => $choix_quiz[0]['nom'],
                    'description' => $choix_quiz[0]['description'],
                    'image' => $image_quiz,
                    'Categorie' => array(
                        'id' => $choix_quiz[0]->Categorie['id'],
                        'nom' => $choix_quiz[0]->Categorie['nom'],
                    ),
                );
                $registry->set('accueil_quiz', $quiz);
                return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
            }
        }
        if (!empty($_GET['quiz']) && is_numeric($_GET['quiz'])) {
            $choix_quiz = $quizRepository->get($_GET['quiz']);
            if ($choix_quiz !== false) {
                $quiz = array(
                    'id' => $choix_quiz['id'],
                    'nom' => $choix_quiz['nom'],
                    'description' => $choix_quiz['description'],
                    'image' => $image_quiz,
                    'Categorie' => array(
                        'id' => $choix_quiz->Categorie['id'],
                        'nom' => $choix_quiz->Categorie['nom'],
                    ),
                );
                $registry->set('accueil_quiz', $quiz);
                return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
            }
        }
        if (isset($_POST['image_quiz'])) {
            $infos_quiz['image'] = $_POST['image_quiz'];
            $registry->set('accueil_quiz', $infos_quiz);
            return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
        }

        //--- Cas du blog mis en valeur ---
        $infos_billet = $registry->get('accueil_billet');
        if (empty($infos_billet)) $infos_billet = array();
        $image_billet = array_key_exists('image', $infos_billet) ? $infos_billet['image'] : '';

        if (!empty($_POST['billet'])) {
            $choix_billet = BlogDAO::ChercherBillets($_POST['billet']);
            if (count($choix_billet) == 1) {
                $billet = array(
                    'billet_id' => $choix_billet[0]['blog_id'],
                    'billet_nom' => $choix_billet[0]['version_titre'],
                    'cat_nom' => $choix_billet[0]['cat_nom']
                );
                $registry->set('accueil_billet', $billet);
                return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
            }
        }
        if (!empty($_GET['billet'])) {
            $billet = BlogDAO::InfosBillet($_GET['billet']);
            if (!empty($billet)) {
                $billet = array(
                    'billet_id' => $billet[0]['blog_id'],
                    'billet_nom' => $billet[0]['version_titre'],
                    'cat_nom' => $billet[0]['cat_nom']
                );
                $registry->set('accueil_billet', $billet);
                return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
            }
        }

        $categories = CategoryDAO::ListerEnfants(CategoryDAO::GetIdCategorie('blog'), false);
        $categories_actuelles = $registry->get('categories_billet_hasard');

        if (isset($_POST['categories'])) {
            $registry->set('categories_billet_hasard', $_POST['categories']);

            return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
        }

        if (isset($_GET['supprimer_cache'])) {
            $this->get('cache')->delete('billet_hasard');

            return redirect('Le billet au hasard a bien été régénéré.', $this->generateUrl('zco_home_config'));
        }


        // Twitter
        $accueil_tweets = $registry->get('accueil_tweets');
        if (isset($_POST['tweets'])) {
            $nb = (int)$_POST['tweets'];
            $nb < 1 && $nb = 1;
            $nb > 10 && $nb = 10;

            $registry->set('accueil_tweets', $nb);
            return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
        }

        // Dictée
        $listDictees = array();
        if (isset($_POST['dictee'])) {
            $listDictees = DictationDAO::searchDictees($_POST['dictee']);
            if (sizeof($listDictees) == 1) {
                $dictee = DictationDAO::Dictee($listDictees[0]['id']);
                $registry->set('dictee_en_avant', $dictee);
                return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
            }
        }

        if (!empty($_GET['dictee']) && is_numeric($_GET['dictee'])) {
            settype($_GET['dictee'], 'int');
            $dictee = DictationDAO::Dictee($_GET['dictee']);
            $registry->set('dictee_en_avant', $dictee);
            return redirect('Le contenu du bloc à tout faire a bien été changé.', $this->generateUrl('zco_home_config'));
        }

        $selectDictee = $registry->get('dictee_en_avant');
        if (!$selectDictee) {
            $selectDictee = null;
        }

        fil_ariane('Modifier les annonces');

        return $this->render('ZcoPagesBundle:Home:config.html.php', compact(
            'bloc_accueil',
            'texte_zform',
            'choix_quiz',
            'infos_quiz',
            'image_quiz',
            'infos_sujet',
            'image_sujet',
            'infos_billet',
            'image_billet',
            'choix_billet',
            'categories',
            'categories_actuelles',
            'accueil_tweets',
            'selectDictee',
            'listDictees'
        ));
    }
}
