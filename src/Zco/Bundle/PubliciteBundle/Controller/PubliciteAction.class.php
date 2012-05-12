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

/**
 * Affichage des informations sur une publicité. Affiche une prévisualisation
 * de la publicité, ainsi que des statistiques, par semaine et globales.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class PubliciteAction extends PubliciteActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$publicite = Doctrine_Core::getTable('Publicite')->findOneById($_GET['id']);
			if ($publicite == false)
				return redirect(6, 'index.html', MSG_ERROR);

			if(
				verifier('publicite_voir') ||
				($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_proposer'))
			)
			{
				Page::$titre = htmlspecialchars($publicite['titre'].' - '.$publicite->Campagne['nom']);
				list($jour, $mois, $annee) = !empty($_GET['week']) ? explode('-', $_GET['week']) : explode('-', date('d-m-Y', date('N') == 1 ? time() : strtotime('previous monday')));
				$stats = Doctrine_Core::getTable('PubliciteStat')->getForWeek($publicite['id'], $jour, $mois, $annee);

				//Inclusion de la vue
				fil_ariane(array(
					htmlspecialchars($publicite->Campagne['nom']) => 'campagne-'.$publicite['campagne_id'].'.html',
					htmlspecialchars($publicite['titre']),
				));
				$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
				
				return render_to_response(array(
					'annee'     => $annee,
					'mois'      => $mois,
					'jour'      => $jour,
					'publicite' => $publicite,
					'stats'     => $stats,
					'weeks'     => Doctrine_Core::getTable('PubliciteStat')->getWeeks(),
					'week'      => $annee.'-'.$mois.'-'.$jour,
				));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(459, 'index.html', MSG_ERROR);
	}
}