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

/**
 */
class AnnonceTable extends Doctrine_Table
{
	public function requeteLister()
	{
		return $this->createQuery('a')
					->select('a.*')
					->orderBy('actif DESC, ((date_debut <= NOW()) AND (date_fin IS NULL OR date_fin >= NOW())) DESC, date_debut');
	}
	
	public function lister()
	{
		return $this->requeteLister()->execute();
	}

	public function chercher(array $params = array())
	{
		$annonces = $this->requeteLister()
			->addSelect('c.*, g.*, p.*, d.*')
			->leftJoin('a.Categories c')
			->leftJoin('a.Groupes g')
			->leftJoin('a.Pays p')
			->leftJoin('a.Domaines d')
			->execute();
		
		foreach ($annonces as $i => $annonce)
		{
			if (!$this->estEligible($annonce, $params))
			{
				unset($annonces[$i]);
			}
		}
		
		return $annonces;
	}
	
	public function getDefaultParams(array $params = array())
	{
		if (empty($params['groupes']))
		{
			$groupe  = isset($_SESSION['groupe']) ? $_SESSION['groupe'] : GROUPE_VISITEURS;
			$groupes = isset($_SESSION['groupes_secondaires']) ? $_SESSION['groupes_secondaires'] : array();
			array_unshift($groupes, $groupe);
			
			$params['groupes'] = $groupes;
		}
		
		if (empty($params['categorie']))
		{
			$params['categorie'] = GetIDCategorieCourante();
		}
		
		if (empty($params['pays']))
		{
			$params['pays'] = !empty($_SESSION['pays_id']) ? (int) $_SESSION['pays_id'] : null;
		}
		
		if (empty($params['domaine']))
		{
			$params['domaine'] = $_SERVER['SERVER_NAME'];
		}

		return $params;
	}
	
	public function recuperer(array $params = array(), $id = null)
	{
		//Si on veut forcer l'affichage d'une bannière particulière, on le fait.
		//Dans ce cas on n'incrémente pas le compteur d'affichages, ce sera 
		//généralement pour des tests.
		if (!empty($id))
		{
			if (($annonce = Container::getService('zco_core.cache')->get('annonce_details-'.$id)) === false)
			{
				$annonce = $this->find($id);
			}
			
			$params = $this->getDefaultParams($params);
			if ($annonce && (empty($annonce['Groupes']) || $this->estGroupeEligible($annonce['Groupes'], $params['groupes']) || verifier('annonces_publier')))
			{
				return $this->genererHTML($annonce);
			}
			
			//La bannière n'existe pas, on n'affiche rien en signe de protestation.
			return '';
		}
		
		//Sinon on retrouve la bannière à afficher. Les clés primaires des bons candidats 
		//(i.e. actifs et dont la date de fin n'est pas passée au moment de la génération)
		//sont stockées en cache. Chaque bannière éligible est aussi stockée en cache.
		//On ne peut pas se baser sur la date de début car vu que le cache est éternel, on 
		//risque alors de « rater » la date de démarrage de l'affichage de la bannière.
		$masquees = !empty($_COOKIE['annonces_masquees']) ? explode('|', $_COOKIE['annonces_masquees']) : array();
		$sommePoids = 0;
		if (($pks = Container::getService('zco_core.cache')->get('annonces')) === false)
		{
			//Reconstruction du cache.
			$retour = $this->createQuery('a')
				->select('a.*')
				->where('a.actif = 1')
				->andWhere('a.date_fin >= NOW() OR a.date_fin IS NULL')
				->execute();
			
			$pks = array();
			foreach ($retour as $annonce)
			{
				$pks[] = $annonce['id'];
				$annonce = $annonce->mettreEnCache();
				
				//Dans tous les cas la bannière est mise en cache, mais on 
				//ne l'ajoute aux bannières éligibles que si c'est le cas.
				if ($this->estEligible($annonce, $params) && !in_array($annonce['id'], $masquees))
				{
					$annonce['poidsCumule'] = ($sommePoids += $annonce['poids']);
					$annonces[] = $annonce;
				}
			}
			Container::getService('zco_core.cache')->set('annonces', $pks, 0);
		}
		else
		{
			//Ouf, notre cache est valide, on extrait maintenant les annonces.
			//Chacune dispose de ses propres données, elles aussi en cache.
			$annonces = array();
			foreach ($pks as $pk)
			{
				$annonce = Container::getService('zco_core.cache')->get('annonce_details-'.$pk);
				
				//Il se peut que l'annonce soit toujours référencée dans le 
				//cache mais que son cache ne soit plus valide. Dans ce cas 
				//on remet en cache l'annonce.
				if (!$annonce)
				{
					$annonce = $this->find($pk);
					if ($annonce)
					{
						$annonce = $annonce->mettreEnCache();
					}
				}
				
				//Vérification de l'éligibilité de l'annonce maintenant.
				if ($annonce && $this->estEligible($annonce, $params) && !in_array($annonce['id'], $masquees))
				{
					$annonce['poidsCumule'] = ($sommePoids += $annonce['poids']);
					$annonces[] = $annonce;
				}
			}
		}
		
		if ($sommePoids > 0)
		{
			//Si on n'a trouvé qu'une seule annonce éligible, inutile de perdre 
			//plus de temps.
			if (count($annonces) === 1)
			{
				$annonce = $annonces[0];
			}
			else
			{
				//Sinon on génère un joli nombre aléatoire pour tenir compte des poids 
				//de chaque bannière et on retrouve la bannière en question.
				$rand = mt_rand(0, $sommePoids - 1);
				foreach ($annonces as $annonce)
				{
					if ($rand < $annonce['poidsCumule'])
					{
						break;
					}
				}
			}
			
			//On incrémente le compteur de vues.
			Container::getService('zco_core.cache')->set('annonce_nbv-'.$annonce['id'], Container::getService('zco_core.cache')->get('annonce_nbv-'.$annonce['id'], 0) + 1, 0);
			
			return $this->genererHTML($annonce);
		}
		
		//Visiblement on n'a rien trouvé à afficher, donc on n'affiche rien.
		return '';
	}
	
	public function genererHTML($annonce)
	{
		$html = $annonce['contenu'];
		
		if (strpos($html, '%url%') !== false && strpos($annonce['url_destination'], '%page%') !== false && isset($_GET['_page']))
		{
			$page = $_GET['_page'];
			if (($pos = strpos($page, '?')) !== false)
			{
				$page = substr($page, 0, $pos);
			}
			$ajout = '?_page='.urlencode($page);
		}
		else
		{
			$ajout = '';
		}
		
		$html = str_replace('%fermer%', '/annonces/fermer-'.$annonce['id'].'.html', $html);
		$html = str_replace('%url%', '/annonces/clic-'.$annonce['id'].'.html'.$ajout, $html);
		$html = str_replace('%token%', $_SESSION['token'], $html);

		return $html;
	}
	
	public function estEligible($annonce, array $params)
	{
		if ($annonce instanceof Annonce)
		{
			$annonce = $annonce->mettreEnCache();
		}
		
		$params = $this->getDefaultParams($params);
		
		if (strtotime($annonce['date_debut']) > time())
		{
			return false;
		}
		
		if (!empty($annonce['date_fin']) && strtotime($annonce['date_fin']) < time())
		{
			return false;
		}
		
		if (!empty($annonce['Groupes']) && !$this->estGroupeEligible($annonce['Groupes'], $params['groupes']))
		{
			return false;
		}

		if (!empty($annonce['Categories']) && !in_array($params['categorie'], $annonce['Categories']))
		{
			return false;
		}
		
		if (!empty($annonce['Pays']) && ((empty($params['pays']) && !$annonce['aff_pays_inconnu']) || (!empty($params['pays']) && !in_array($params['pays'], $annonce['Pays']))))
		{
			return false;
		}
		
		if (!empty($annonce['Domaines']) && !in_array($params['domaine'], $annonce['Domaines']))
		{
			return false;
		}
		
		return true;
	}
	
	public function estGroupeEligible(array $groupesAnnonce, array $groupes)
	{
		foreach ($groupes as $groupe)
		{
			if (in_array($groupe, $groupesAnnonce))
			{
				return true;
			}
		}
		
		return false;
	}
}