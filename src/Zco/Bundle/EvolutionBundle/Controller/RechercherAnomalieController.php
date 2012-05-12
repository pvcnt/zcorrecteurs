<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\EvolutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur se chargeant de la recherche d'anomalie.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class RechercherAnomalieController extends Controller
{
	public function defaultAction()
	{
		\Page::$titre = 'Recherche avancée';

		if(isset($_GET['submit']))
		{
			//Définition des paramètres de filtrage
			if(!empty($_GET['cat']))
			{
				$params['categorie'] = array();
				foreach($_GET['cat'] as $cle => $valeur)
					$params['categorie'][] = $cle;
			}
			if(!empty($_GET['priorite']))
			{
				$params['priorite'] = array();
				foreach($_GET['priorite'] as $cle => $valeur)
					$params['priorite'][] = $cle;
			}
			if(!empty($_GET['etat']))
			{
				$params['etat'] = array();
				foreach($_GET['etat'] as $cle => $valeur)
					$params['etat'][] = $cle;
			}
			if(!empty($_GET['titre']))
			{
				$params['titre'] = $_GET['titre'];
				if(!empty($_GET['rechercher_description']) &&
				   $_GET['rechercher_description'] == 'on')
				{
					$params['description'] = $_GET['titre'];
				}
			}
			$types = array(null, 'bug', 'tache');
			$params['type'] = isset($_GET['type'], $types[$_GET['type']])
				? $types[$_GET['type']]
				: current($types);
			if(verifier('tracker_voir_assigne'))
			{
				if(isset($_GET['admin']) && $_GET['admin'] == 0)
					$params['admin'] = false;
				elseif(!empty($_GET['admin']) && $_GET['admin'] == 1 && !empty($_GET['admin_pseudo']))
				{
					if(($infos = InfosUtilisateur($_GET['admin_pseudo'])) != false)
						$params['admin'] = $infos['utilisateur_id'];
					else
						return redirect(123, '', MSG_ERROR, -1);
				}
			}
			$params['prive'] = verifier('tracker_voir_prives');
			$orderby = !empty($_GET['orderby']) ? $_GET['orderby'] : null;

			$url = 'rechercher-anomalie-p%s.html?';
			foreach($_GET as $cle => $valeur)
			{
				if(!in_array($cle, array('page', 'act', 'p')))
					if(is_array($valeur))
					{
						foreach($valeur as $c => $v)
							$url .= $cle.'['.$c.']='.$v.'&';
					}
					else
						$url .= $cle.'='.$valeur.'&';
			}
			$filtre = 'recherche';
			$page = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
			$ListerTickets = ListerTickets($page, $params, $orderby);
			$CompterTickets = CompterTickets($params);
			$CompterTicketsEtat = CompterTicketsEtat(verifier('tracker_voir_prives'));
			$tableau_pages = liste_pages($page, ceil($CompterTickets / 30), $CompterTickets, 30, $url);

			$colspan = verifier('tracker_voir_assigne') ? 6 : 5;
			if(verifier('tracker_voir_prives')) $colspan++;

			//Inclusion de la vue
			fil_ariane(array('Résultats de la recherche'));
			$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
			
			return render_to_response('ZcoEvolutionBundle::demandes.html.php', array(
				'ListerTickets' => $ListerTickets,
				'CompterTickets' => $CompterTickets,
				'CompterTicketsEtat' => $CompterTicketsEtat,
				'filtre' => $filtre,
				'colspan' => $colspan,
				'url' => $url,
				'type' => 1,
				'tableau_pages' => $tableau_pages,
			));
		}
		else
		{
			fil_ariane(array('Liste des anomalies' => 'anomalies.html', 'Rechercher parmi les anomalies'));
			
			return render_to_response(array(
				'ListerCategories' => ListerCategories(),
			));
		}
	}
}
