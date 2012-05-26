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
 * Contrôleur gérant la modification de l'apparence visuelle
 * d'une publicité.
 *
 * @package		zCorrecteurs.fr
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ModifierApparenceAction extends PubliciteActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$publicite = Doctrine_Core::getTable('Publicite')->findOneById($_GET['id']);
			if($publicite === false)
				return redirect(6, 'index.html', MSG_ERROR);

			if(
				verifier('publicite_editer') ||
				($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_siens'))
			)
			{
				Page::$titre = htmlspecialchars($publicite['titre'].' - '.$publicite->Campagne['nom']);

				if (isset($_POST['send']))
				{
					$publicite['titre']      = $_POST['titre'];
					$publicite['url_cible']  = $_POST['url_cible'];
					$publicite['contenu']    = $_POST['contenu'];
					$publicite['contenu_js'] = verifier('publicite_js') && isset($_POST['contenu_js']);
					$publicite->save();

					$this->get('zco_core.cache')->delete('partenaires_'.$publicite['emplacement']);
					
					return redirect(3, 'publicite-'.$publicite['id'].'.html');
				}

				//Inclusion de la vue
				fil_ariane(array(
					htmlspecialchars($publicite->Campagne['nom']) => 'campagne-'.$publicite['campagne_id'].'.html',
					htmlspecialchars($publicite['titre']) => 'publicite-'.$publicite['id'].'.html',
					'Apparence',
				));
				$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
				
				return render_to_response(array(
					'publicite' => $publicite,
				));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(459, 'index.html', MSG_ERROR);
	}
}