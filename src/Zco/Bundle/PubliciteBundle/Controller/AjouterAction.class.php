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
 * Contrôleur gérant l'ajout d'une nouvelle publicité sur le site.
 * Si l'utilisateur ne dispose pas des droits suffisants, cela sera
 * traité comme une proposition de publicité.
 *
 * @package		zCorrecteurs.fr
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AjouterAction extends PubliciteActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$campagne = Doctrine_Core::getTable('PubliciteCampagne')->find($_GET['id']);
		}
		else
		{
			$campagne = false;
		}

		if (isset($_POST['send']))
		{
			foreach(array('titre', 'emplacement', 'url_cible',
			              'contenu', 'nom') as $champ)
			{
				$_POST[$champ] = isset($_POST[$champ]) ?
					trim($_POST[$champ]) : null;
				if(empty($_POST[$champ]))
					return redirect(17, '', MSG_ERROR);
			}
			if ($campagne == false)
			{
				$campagne = new PubliciteCampagne;
				$campagne['utilisateur_id']  = $_SESSION['id'];
				$campagne['nom']             = $_POST['nom'];
				$campagne['etat']            = 'en_cours';
				$campagne['date_debut']      = $_POST['prog'] == 'periode' ? $_POST['date_debut'] : new Doctrine_Expression('NOW()');
				$campagne['date_fin']        = $_POST['prog'] == 'periode' && !empty($_POST['date_fin']) ? $_POST['date_fin'] : null;
				$campagne->save();
			}

			$publicite = new Publicite;
			$publicite['campagne_id']    = $campagne['id'];
			$publicite['titre']          = $_POST['titre'];
			$publicite['emplacement']    = $_POST['emplacement'];
			$publicite['url_cible']      = $_POST['url_cible'];
			$publicite['contenu']        = $_POST['contenu'];
			$publicite['contenu_js']     = verifier('publicite_js') && isset($_POST['contenu_js']);
			$publicite['actif']          = isset($_POST['actif']) && (verifier('publicite_changer_etat_siens') || verifier('publicite_changer_etat'));
			$publicite['approuve']       = isset($_POST['actif']) && (verifier('publicite_changer_etat_siens') || verifier('publicite_changer_etat')) ? 'approuve' : 'attente';
			$publicite['aff_pays_inconnu'] = !isset($_POST['cibler_pays']) ? isset($_POST['pays_inconnu']) : true;

			//Ciblage par âge.
			if (!isset($_POST['cibler_age']))
			{
				$publicite['age_min']         = isset($_POST['aucun_age_min']) || empty($_POST['age_min']) || $_POST['age_min'] === '-'? 0 : $_POST['age_min'];
				$publicite['age_max']         = isset($_POST['aucun_age_max']) || empty($_POST['age_max']) || $_POST['age_max'] === '-'? 0 : $_POST['age_max'];
				$publicite['aff_age_inconnu'] = isset($_POST['age_inconnu']);
			}
			else
			{
				$publicite['age_min']         = null;
				$publicite['age_max']         = null;
				$publicite['aff_age_inconnu'] = true;
			}
			$publicite->save();

			//Ciblage par pays.
			if (!isset($_POST['cibler_pays']))
			{
				foreach ($_POST['pays'] as $pays)
				{
					$foo = new PublicitePays;
					$foo['publicite_id'] = $publicite['id'];
					$foo['pays_id']      = $pays;
					$foo->save();
					$foo = null;
				}
			}

			//Ciblage par catégorie.
			if (!isset($_POST['cibler_categories']))
			{
				foreach ($_POST['categories'] as $cat)
				{
					$foo = new PubliciteCategorie;
					$foo['publicite_id'] = $publicite['id'];
					$foo['categorie_id'] = $cat;
					$foo['actions']      = !empty($_POST['index_'.$cat]) ? 'index' : null;
					$foo->save();
					$foo = null;
				}
			}

			$this->get('zco_core.cache')->delete('partenaires_'.$publicite['emplacement']);
			
			return redirect($publicite['actif'] ? 1 : 2, 'campagne-'.$campagne['id'].'.html');
		}

		fil_ariane('Nouvelle publicité');
		
		return render_to_response(array(
			'campagne'       => $campagne,
			'nb_membres_age' => Doctrine_Core::getTable('Utilisateur')->compterMembresAge(),
			'categories'     => Doctrine_Core::getTable('Categorie')->getCategoriesCiblables(),
			'pays'           => Doctrine_Core::getTable('Pays')->findAll(),
		));
	}
}
