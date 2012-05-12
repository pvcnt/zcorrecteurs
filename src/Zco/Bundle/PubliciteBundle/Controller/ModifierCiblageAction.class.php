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
 * Contrôleur gérant la modification du ciblage d'une publicité
 * en cours d'affichage.
 *
 * @package		zCorrecteurs.
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ModifierCiblageAction extends PubliciteActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$publicite = Doctrine_Core::getTable('Publicite')->findOneById($_GET['id']);
			if ($publicite == false)
				return redirect(6, 'index.html', MSG_ERROR);

			if (
				verifier('publicite_editer_ciblage') ||
				($publicite->Campagne['utilisateur_id'] == $_SESSION['id'] && verifier('publicite_editer_ciblage_siens'))
			)
			{
				Page::$titre = htmlspecialchars($publicite['titre'].' - '.$publicite->Campagne['nom']);

				$pays = $cats = array();
				foreach ($publicite->Pays as $p)
				{
					$pays[] = $p['id'];
				}
				foreach ($publicite->Categories as $cat)
				{
					$cats[$cat['categorie_id']] = !empty($cat['actions']) ? (array)explode(',', $cat['actions']) : array();
				}

				if (isset($_POST['send']))
				{
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
						foreach ($_POST['pays'] as $p)
						{
							if (!in_array($p, $pays))
							{
								$foo = new PublicitePays;
								$foo['publicite_id'] = $publicite['id'];
								$foo['pays_id']      = $p;
								$foo->save();
								$foo = null;
							}
							unset($pays[$p]);
						}
						if (!empty($pays))
						{
							Doctrine_Query::create()
								->delete('PublicitePays')
								->where('publicite_id = ?', $publicite['id'])
								->andWhereIn('pays_id', $pays)
								->execute();
						}
					}

					//Ciblage par catégorie.
					if (!isset($_POST['cibler_categories']))
					{
						foreach ($_POST['categories'] as $cat)
						{
							if (!isset($cats[$cat]))
							{
								$foo = new PubliciteCategorie;
								$foo['publicite_id'] = $publicite['id'];
								$foo['categorie_id'] = $cat;
								$foo['actions']      = !empty($_POST['index_'.$cat]) ? 'index' : null;
								$foo->save();
								$foo = null;
							}
							elseif (!empty($cats[$cat]) && empty($_POST['index_'.$cat]))
							{
								Doctrine_Query::create()
									->update('PubliciteCategorie')
									->set('actions', new Doctrine_Expression('NULL'))
									->where('publicite_id = ?', $publicite['id'])
									->andWhere('categorie_id = ?', $cat)
									->execute();
							}
							elseif (empty($cats[$cat]) && !empty($_POST['index_'.$cat]))
							{
								Doctrine_Query::create()
									->update('PubliciteCategorie')
									->set('actions', '?', 'index')
									->where('publicite_id = ?', $publicite['id'])
									->andWhere('categorie_id = ?', $cat)
									->execute();
							}
							unset($cats[$cat]);
						}
						if (!empty($cats))
						{
							Doctrine_Query::create()
								->delete('PubliciteCategorie')
								->where('publicite_id = ?', $publicite['id'])
								->andWhereIn('categorie_id', array_keys($cats))
								->execute();
						}
					}

					$publicite->mettreEnCache();
					return redirect(4, 'publicite-'.$publicite['id'].'.html');
				}

				//Inclusion de la vue
				fil_ariane(array(
					htmlspecialchars($publicite->Campagne['nom']) => 'campagne-'.$publicite['campagne_id'].'.html',
					htmlspecialchars($publicite['titre']) => 'publicite-'.$publicite['id'].'.html',
					'Ciblage',
				));
				$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
				
				return render_to_response(array(
					'publicite' => $publicite,
					'categories' => Doctrine_Core::getTable('Categorie')->getCategoriesCiblables(),
					'pays'       => Doctrine_Core::getTable('Pays')->findAll(),
					'nb_membres_age' => Doctrine_Core::getTable('Utilisateur')->compterMembresAge(),
					'attr_pays'  => $pays,
					'attr_cats'  => $cats,
					'cibler_categories' => count($publicite->Categories) > 0,
					'cibler_pays'       => count($publicite->Pays) > 0,
					'cibler_age'        => !empty($publicite['age_min']) || !empty($publicite['age_max']),
					'cibler_age_min'    => !empty($publicite['age_min']),
					'cibler_age_max'    => !empty($publicite['age_max']),
				));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(459, 'index.html', MSG_ERROR);
	}
}