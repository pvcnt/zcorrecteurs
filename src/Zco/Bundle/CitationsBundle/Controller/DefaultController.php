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

namespace Zco\Bundle\CitationsBundle\Controller;

use Zco\Bundle\CoreBundle\Generator\Generator;

/**
 * Contrôleur chargé de la gestion des citations tournant dans
 * l'en-tête du site.
 *
 * @author Zopieux
 */
class DefaultController extends Generator
{
	protected $modelName = 'Citation';

    /**
     * Affichage de la liste des citations.
     */
	public function indexAction()
	{
		return $this->executeList();
	}

    /**
     * Ajout d'une nouvelle citation.
     */
	public function ajouterAction()
	{
		return $this->executeNew();
	}

    /**
     * Modification d'une citation existante.
     */
	public function modifierAction()
	{
		\zCorrecteurs::VerifierFormatageUrl(null, true);
		
		return $this->executeEdit($_GET['id']);
	}

    /**
     * Suppression d'une citation existante.
     */
	public function supprimerAction()
	{
		\zCorrecteurs::VerifierFormatageUrl(null, true);
		
		return $this->executeDelete($_GET['id']);
	}
	
	/**
	 * Renvoie la requête de listage des citations à utiliser.
	 *
	 * @return \Doctrine_Query
	 */
	protected function getListQuery()
	{
		return \Doctrine_Core::getTable('Citation')
			->getRequeteTableau();
	}

    /**
     * Affiche/masque en lot des citations à afficher dans l'en-tête.
     *
     * @param  array $pks Liste des identifiants des citations à modifier
     * @return Response
     */
	protected function batchAutorisations($pks)
	{
		$citations = \Doctrine_Query::create()
			->from($this->modelName)
			->whereIn('id', $pks)
			->execute();
		
		foreach ($citations as $citation)
		{
			$citation['statut'] = !$citation['statut'];
			$citation->save();
		}
		$this->get('zco_core.cache')->delete('header_citations');
		
		return redirect('Les autorisations sur les citations ont bien été modifiées.');
	}

    /**
     * Supprime en lot des citations existantes.
     *
     * @param  array $pks Liste des identifiants des citations à supprimer
     * @return Response
     */
	protected function batchDelete(array $pks)
	{
		$this->get('zco_core.cache')->delete('header_citations');
		
		return parent::batchDelete($pks);
	}
}
