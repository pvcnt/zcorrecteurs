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
 */
class PubliciteTable extends Doctrine_Table
{
	/**
	 * Retourne la liste des publicités éligibles pour l'affichage
	 * sur un emplacement donné. Met en cache la liste des clés
	 * primaires des publicités en place à cet emplacement, ainsi
	 * que chaque publicité individuellement.
	 *
	 * @param string $emplacement
	 * @param boolean $maj_affichages		Mettre à jour les affichages sur cette zone ?
	 * @return array
	 */
	public function getFor($emplacement, $maj_affichages = true)
	{
		if (($pks = Container::getService('zco_core.cache')->get('pub-'.$emplacement)) === false)
		{
			//Permier tri des publicités, on ne prend que celles actives pour la zone donnée.
			$publicites = $this->createQuery('p')
				->select('p.nb_clics, p.nb_affichages, p.titre, p.contenu, p.contenu_js, '.
					'p.url_cible, p.age_min, p.age_max, p.aff_pays_inconnu, p.aff_age_inconnu, '.
					'pp.id, pp.nom, pp.code, pc.categorie_id, pc.publicite_id, pc.actions')
				->leftJoin('p.Campagne c')
				->leftJoin('p.Pays pp')
				->where('p.actif = 1')
				->andWhere('p.approuve = ?', 'approuve')
				->andWhere('c.etat = ?', 'en_cours')
				->andWhere('p.emplacement = ?', $emplacement)
				->andWhere('(c.date_debut <= NOW() OR c.date_debut IS NULL) AND (c.date_fin >= NOW() OR c.date_fin IS NULL)')
				->execute();

			//On met en cache la liste des ids des publicités,
			//ainsi que chaque publicité individuellement.
			$pks = array();
			foreach ($publicites as $i => $pub)
			{
				$pks[] = $pub['id'];
				$publicites[$i] = $pub->mettreEnCache();
			}
			Container::getService('zco_core.cache')->set('pub-'.$emplacement, $pks, 0);
		}
		else
		{
			$publicites = array();
			foreach ($pks as $pk)
			{
				$pub = Container::getService('zco_core.cache')->get('pub_details-'.$pk);
				if (!empty($pub))
				{
					$publicites[] = $pub;
				}
			}
		}

		//Maintenant on ne sélectionne que les publicités éligibles.
		foreach ($publicites as $i => $pub)
		{
			//Ciblage par âge.
			if (!empty($pub['age_min']) || !empty($pub['age_max']))
			{
				if (empty($_SESSION['age']) && !$pub['aff_age_inconnu'])
				{
					unset($publicites[$i]);
					break;
				}
				if (!empty($_SESSION['age']) && !empty($pub['age_min']) && $_SESSION['age'] < $pub['age_min'])
				{
					unset($publicites[$i]);
					break;
				}
				if (!empty($_SESSION['age']) && !empty($pub['age_max']) && $_SESSION['age'] > $pub['age_max'])
				{
					unset($publicites[$i]);
					break;
				}
			}

			//Ciblage par pays.
			if (count($pub['Pays']) > 0)
			{
				if (empty($_SESSION['pays']) && !$pub['aff_pays_inconnu'])
				{
					unset($publicites[$i]);
					break;
				}
				if (!in_array($_SESSION['pays'], $pub['Pays']))
				{
					unset($publicites[$i]);
					break;
				}
			}

			//Ciblage par catégorie.
                        $request = Container::getService('request');
			if ($pub['aff_accueil'] && $request->attributes->get('_module') != 'accueil')
			{
				unset($publicites[$i]);
				break;
			}

			//Mise à jour des vues si nécessaire.
			if (isset($publicites[$i]) && $maj_affichages === true)
			{
				Container::getService('zco_core.cache')->set('pub_nbv-'.$pub['id'], Container::getService('zco_core.cache')->get('pub_nbv-'.$pub['id'], 0) + 1, 0);
			}
		}

		return !empty($publicites) ? $publicites : false;
	}

	/**
	 * Retourne les publicités pour un affichage dans le menu.
	 *
	 * @see self::getFor()
	 * @return array
	 */
	public function getForMenu()
	{
		return $this->getFor('menu');
	}

	/**
	 * Retourne les publicités pour un affichage dans le pied de page.
	 *
	 * @see self::getFor()
	 * @return array
	 */
	public function getForPied()
	{
		return $this->getFor('pied');
	}

	/**
	 * Retourne les publicités en attente de validation
	 * par les administrateurs.
	 *
	 * @return Doctrine_Collection
	 */
	public function getPropositions()
	{
		return $this->createQuery('p')
			->select('p.*, c.*, u.utilisateur_id, u.utilisateur_pseudo')
			->leftJoin('p.Campagne c')
			->leftJoin('c.Utilisateur u')
			->where('p.approuve = ?', 'attente')
			->execute();
	}

	public function findOneById($id)
	{
		return $this->createQuery('p')
			->select('p.*, c.*, pc.*, cat.cat_id, cat.cat_nom, pp.*')
			->leftJoin('p.Pays pp')
			->leftJoin('p.Campagne c')
			->where('p.id = ?', $id)
			->fetchOne();
	}
}