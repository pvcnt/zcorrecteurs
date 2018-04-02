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

namespace Zco\Bundle\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zco\Bundle\SearchBundle\Search\Searchable\BlogSearchable;
use Zco\Bundle\SearchBundle\Search\Searchable\ForumSearchable;
use Zco\Bundle\SearchBundle\Search\Searchable\TwitterSearchable;
use Zco\Bundle\SearchBundle\Search\SearchableInterface;
use Zco\Bundle\SearchBundle\Search\SearchQuery;
use Zco\Bundle\SearchBundle\Search\SearchQueryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant la recherche sur le site.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
    /**
     * Affichage du formulaire complet de recherche et des résultats.
     *
     * @param string $section
     * @param int $page
     * @param Request $request HTTP request.
     * @return Response
     */
    public function indexAction($section, $page, Request $request)
    {
        // Configuration pour les trois actions (avant et après la recherche)
        $CatsForum = ListerEnfants(GetIDCategorie('forum'), true, true);
        $CatsBlog = ListerEnfants(GetIDCategorie('blog'), true, true);
        \Page::$titre = 'Recherche';
        $this->get('zco_vitesse.resource_manager')->requireResources(array(
            '@ZcoForumBundle/Resources/public/css/forum.css',
            '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            '@ZcoCoreBundle/Resources/public/css/zcode.css',
        ));

        $_flags = array();

        // Section du site concernée par la recherche
        $section = $section ?: 'forum';
        if ('forum' === $section) {
            $searchable = new ForumSearchable();
        } elseif ('blog' === $section) {
            $searchable = new BlogSearchable();
        } elseif ('twitter' === $section) {
            $searchable = new TwitterSearchable();
        } else {
            return redirect(
                'Votre catégorie de recherche est invalide.',
                $this->generateUrl('zco_search_index'),
                MSG_ERROR
            );
        }

        if (!$request->query->has('recherche')) {
            return render_to_response('ZcoSearchBundle::index.html.php', compact(
                'CatsForum', 'CatsBlog', '_flags'
            ));
        }

        $query = new SearchQuery();
        $_flags['recherche'] = $request->query->get('recherche');
        $query->setSearch($_flags['recherche']);

        // Pagination.
        $_flags['nb_resultats'] = $resultats = (int)$request->query->get('nb_resultats', 20);
        $resultats = ($resultats <= 50 && $resultats >= 5) ? $resultats : 20;
        $page = max(1, $page);
        $_flags['nb_resultats'] = $resultats;
        $query->setPage($page, $resultats);

        // Mode de recherche.
        $modes = [
            'tous' => SearchQuery::MATCH_ALL,
            'un' => SearchQuery::MATCH_ANY,
            'phrase' => SearchQuery::MATCH_PHRASE
        ];
        $mode = $request->query->get('mode', current($modes));
        $mode = isset($modes[$mode]) ? $mode : current($modes);
        $_flags['mode'] = $mode;
        $query->setMatchMode($mode);

        // Restriction de catégorie.
        if ($request->query->has('categories')) {
            $categoryIds = $request->query->get('categories') ?: [];
            $query->setCategories($categoryIds);
            $_flags['categories'] = $categoryIds;
        }

        // Critères de recherche spécifiques à une section.
        if ($section == 'forum') {
            $flags = array('ferme', 'resolu', 'postit');
            foreach ($flags as $flg) {
                if ($request->query->has($flg)) {
                    $_flags[$flg] = (bool)$request->query->get($flg);
                    if ($_flags[$flg]) {
                        $query->includeFlag('sujet_' . $flg);
                    } else {
                        $query->excludeFlag('sujet_' . $flg);
                    }
                }
            }
            $_flags['auteur'] = $request->query->get('auteur', '');
            if ($_flags['auteur']) {
                $query->setAuthor($_flags['auteur']);
            }
        } elseif ($section == 'blog') {
            // …
        } elseif ($section == 'twitter') {
            $_flags['auteur'] = $request->query->get('auteur', '');
            if ($_flags['auteur']) {
                $query->setAuthor($_flags['auteur']);
            }
        }

        // Récupération des résultats
        $pages = $Resultats = $CompterResultats = null;
        try {
            $res = $this->get('zco_search.search_service')->execute($query, $searchable);
            $Resultats = $res->getResults();
            $CompterResultats = $res->getTotalCount();

            //TODO: fix pagination here.
            $url = str_replace('91919191', '%s', $this->generateUrl('zco_search_index', array_merge(['section' => $section, 'page' => 91919191], $_flags)));
            $pages = liste_pages($page, ceil($CompterResultats / $_flags['nb_resultats']), $CompterResultats, $_flags['nb_resultats'], $url);
        } catch (\Exception $e) {
            $this->get('logger')->warn($e->getMessage());
            $_SESSION['erreur'][] = 'Une erreur est survenue pendant la recherche. Merci de réessayer dans quelques instants.';
        }

        return render_to_response('ZcoSearchBundle::index.html.php', compact(
            'CatsForum', 'CatsBlog', '_flags',
            'pages', 'CompterResultats', 'Resultats', 'section'
        ));
    }
}

