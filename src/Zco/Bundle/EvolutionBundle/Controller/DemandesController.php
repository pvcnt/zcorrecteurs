<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

namespace Zco\Bundle\EvolutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur se chargeant de l'affichage des anomalies.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DemandesController extends Controller
{
	public function defaultAction()
	{
		\zCorrecteurs::VerifierFormatageUrl(null, true, false, 1);

		//Définition des paramètres de filtrage
		$filtre = !empty($_GET['filtre']) && in_array($_GET['filtre'], array('new', 'open', 'solved', 'new_comment')) ? $_GET['filtre'] : null;
		if(!isset($_GET['filtre'])) $filtre = 'open';
		$params = array();

		if($filtre == 'new')
		{
			$params['admin'] = false;
			$params['etat'] = array(1);
			$params['doublons'] = false;
		}
		elseif($filtre == 'open')
		{
			$params['etat'] = array('not', 4, 5, 7, 8);
		}
		elseif($filtre == 'solved')
			$params['etat'] = array(4, 5, 7, 8);
		elseif($filtre == 'new_comment' && verifier('connecte'))
			$params['lu'] = false;
		if(!empty($_POST['titre']))
			$params['titre'] = $_POST['titre'];
		$params['prive'] = verifier('tracker_voir_prives');
		$orderby = !empty($_POST['orderby']) ? $_POST['orderby'] : null;
		$params['type'] = !empty($_GET['id']) && $_GET['id'] == 2 ? 'tache' : 'bug';
		$type = !empty($_GET['id']) && $_GET['id'] == 2 ? 2 : 1;

		$url = 'demandes-'.$type.'-p%s.html?filtre='.(!is_null($filtre) ? $filtre : '');
		$page = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
		$ListerTickets = ListerTickets($page, $params, $orderby);
		$CompterTickets = CompterTickets($params);
		$CompterTicketsEtat = CompterTicketsEtat(verifier('tracker_voir_prives'), $type == 2 ? 'tache' : 'bug');
		$tableau_pages = liste_pages($page, ceil($CompterTickets / 30), $CompterTickets, 30, $url);

		$colspan = verifier('tracker_voir_assigne') ? 6 : 5;

		//Inclusion de la vue
		fil_ariane('Liste des anomalies');
		$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
		
		return render_to_response(array(
			'url' => $url,
			'ListerTickets' => $ListerTickets,
			'CompterTickets' => $CompterTickets,
			'CompterTicketsEtat' => $CompterTicketsEtat,
			'tableau_pages' => $tableau_pages,
			'colspan' => $colspan,
			'filtre' => $filtre,
			'type' => $type,
		));
	}
}
