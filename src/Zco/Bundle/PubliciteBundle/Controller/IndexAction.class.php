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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Page affichant la liste de toutes les campagnes de publicité créées
 * sur le site, ainsi qu'un graphique de statistiques par campagne.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class IndexAction extends Controller
{
	public function execute()
	{
		$campagnes = Doctrine_Core::getTable('PubliciteCampagne')->listAll(
			(isset($_GET['all']) && verifier('publicite_voir') ? null : $_SESSION['id']),
			!empty($_GET['etat']) ? $_GET['etat'] : array('en_cours', 'pause', 'termine')
		);

		$total_aff = $total_clic = 0;
		foreach ($campagnes as $campagne)
		{
			$total_aff     += $campagne['nb_affichages'];
			$total_clic    += $campagne['nb_clics'];
		}
		$total_taux = $total_aff > 0 ? $total_clic*100 / $total_aff : 0;

		$query_string = (verifier('publicite_voir') && isset($_GET['all'])) ? array('all=1') : array();
		if (!empty($_GET['etat']))
		{
			foreach ($_GET['etat'] as $etat)
			{
				$query_string[] = 'etat[]='.$etat;
			}
		}
		$query_string = !empty($query_string) ? '&'.implode('&', $query_string) : '';
		fil_ariane(null);
		
		return render_to_response(array(
			'campagnes'     => $campagnes,
			'total_aff'     => $total_aff,
			'total_clic'    => $total_clic,
			'total_taux'    => $total_taux,
			'query_string'  => $query_string,
			'couleurs'      => array('rouge', 'vertf', 'bleu', 'noir', 'orange', 'violet', 'gris'),
		));
	}
}