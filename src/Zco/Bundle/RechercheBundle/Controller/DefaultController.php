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

namespace Zco\Bundle\RechercheBundle\Controller;

use Zco\Bundle\RechercheBundle\Search\SearchInterface;
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
     */
	public function indexAction()
	{
		\zCorrecteurs::VerifierFormatageUrl(null, true, false, 1);

		// Configuration pour les trois actions (avant et après la recherche)
		$CatsForum = ListerEnfants(GetIDCategorie('forum'), true, true);
		$CatsBlog = ListerEnfants(GetIDCategorie('blog'), true, true);
		$CatsTwitter = \Doctrine_Core::getTable('TwitterCompte')->getAll(true);
		\Page::$titre = 'Recherche';
		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoForumBundle/Resources/public/css/forum.css',
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
		));

		$_flags = array();
		$_flags['nb_resultats'] = $resultats = 20;

		// Section du site concernée par la recherche
		$sections = array('forum', 'blog', 'twitter');
		$section = !empty($_GET['section'])
			? $_GET['section']
			: current($sections);
		if (!in_array($section, $sections))
		{
			return redirect(2, 'index.html', MSG_ERROR);
		}
		$_flags['section'] = $section;

		if (empty($_GET['recherche']))
		{
			return render_to_response(compact(
				'CatsForum', 'CatsBlog', 'CatsTwitter', '_flags'
			));
		}
		$_flags['recherche'] = $_GET['recherche'];


		// Création de l'objet pour la recherche
		$className = '\Zco\Bundle\RechercheBundle\Model\\'.ucfirst($section).'Search';
		$Search = new $className();

		// Pagination
		if (isset($_GET['nb_resultats']))
		{
			$resultats = (int)$_GET['nb_resultats'];
		}
		($resultats > 50 || $resultats < 5) && $resultats = 20;
		$page = $_GET['p'];
		$page < 1 && $page = 1;

		$Search->setPage($page, $resultats);
		$_flags['nb_resultats'] = $resultats;


		// Mode de recherche
		$modes = array(
			'tous'   => SearchInterface::MATCH_ALL,
			'un'     => SearchInterface::MATCH_ANY,
			'phrase' => SearchInterface::MATCH_PHRASE
		);
		$mode = isset($_GET['mode']) && isset($modes[$_GET['mode']])
			? $modes[$_GET['mode']]
			: current($modes);
		$Search->getSearcher()->setMatchMode($mode);
		isset($_GET['mode']) && $_flags['mode'] = $_GET['mode'];


		// Critères de recherche généraux
		$addSearchArg = function($Search, $index, $attr)
		{
			if (isset($_GET[$index]) && $_GET[$index] !== '')
			{
				$func = 'set'.ucfirst($attr);
				$Search->$func($_GET[$index]);
				$_flags[$index] = $_GET[$index];
			}
		};
		$addSearchArg($Search, 'categories', 'categories');


		// Restriction de catégorie
		if (!empty($_GET['categories']))
		{
			$Search->setCategories($_GET['categories']);
			$_flags['categories'] = $_GET['categories'];
		}


		// Critères de recherche spécifiques à une section
		if ($section == 'forum')
		{
			$flags = array('ferme', 'resolu', 'postit');
			foreach ($flags as $flg)
			{
				if (isset($_GET[$flg]))
				{
					$_flags[$flg] = (bool)$_GET[$flg];
					$Search->getSearcher()->setFilter(
						'sujet_'.$flg, $_flags[$flg]);
				}
			}
			$_flags['auteur'] = isset($_GET['auteur']) ? $_GET['auteur'] : '';
			$addSearchArg($Search, 'auteur', 'user');
		}
		elseif ($section == 'blog')
		{
			// …
		}
		elseif ($section == 'twitter')
		{
			$_flags['auteur'] = isset($_GET['auteur']) ? $_GET['auteur'] : '';
			$addSearchArg($Search, 'auteur', 'user');
		}

		// Récupération des résultats
		$pages = $Resultats = $CompterResultats = null;
		try
		{
			$Resultats = $Search->getResults($_flags['recherche']);
			$CompterResultats = $Search->countResults();

			$pages = liste_pages($page, ceil($CompterResultats / $_flags['nb_resultats']),
			$CompterResultats, $_flags['nb_resultats'],
				'index-p%s.html?'
				.htmlspecialchars_decode(http_build_query($_flags)));
		}
		catch (\Exception $e)
		{
			trigger_error($e->getMessage(), E_USER_WARNING);
			$_SESSION['erreur'][] = 'Une erreur est survenue pendant la recherche. '
				.' Merci de réessayer dans quelques instants.';
		}

		return render_to_response(compact(
			'CatsForum', 'CatsBlog', 'CatsTwitter', '_flags',
			'pages', 'CompterResultats', 'Resultats'
		));
	}
}

