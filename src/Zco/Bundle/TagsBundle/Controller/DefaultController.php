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

namespace Zco\Bundle\TagsBundle\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;
use Zco\Bundle\CoreBundle\Generator\Generator;

/**
 * Actions gérant l'affichage de la liste des mots-clés et des détails.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class DefaultController extends Generator
{
	protected $modelName = 'Tag';
	
	/**
	 * Affichage de la liste des mots-clés disponibles.
	 */
	public function indexAction()
	{
		return $this->executeList();
	}

    /**
     * Ajout d'un nouveau mot-clé.
     */
	public function ajouterAction()
	{
		return $this->executeNew();
	}

    /**
     * Modification d'un mot-clé existant.
     */
	public function modifierAction()
	{
		\zCorrecteurs::VerifierFormatageUrl(null, true);
		
		return $this->executeEdit($_GET['id']);
	}

    /**
     * Suppression d'un mot-clé existant.
     */
	public function supprimerAction()
	{
		\zCorrecteurs::VerifierFormatageUrl(null, true);
		
		return $this->executeDelete($_GET['id']);
	}

	/**
	 * Affichage des ressources liées à un tag.
	 */
	public function tagAction()
	{
		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$Tag = \Doctrine_Core::getTable('Tag')->find($_GET['id']);
			if ($Tag === false)
			{
				return redirect(1, 'index.html', MSG_ERROR, -1);
			}

			\Page::$titre = htmlspecialchars($Tag['nom']);
            \zCorrecteurs::VerifierFormatageUrl($Tag['nom'], true);
			
			//Inclusion de la vue
			fil_ariane(array(
				htmlspecialchars($Tag->nom) => 'tag-'.$Tag['id'].'-'.rewrite($Tag['nom']).'.html',
				'Voir les ressources liées'
			));
			
			return render_to_response(array(
				'Tag' => $Tag,
				'Ressources' => $Tag->listerRessourcesLiees(),
			));
		}
		else
			return redirect(1, 'index.html', MSG_ERROR, -1);
	}

    /**
     * Retour de la liste des tags disponibles en JSON.
     */
	public function ajaxListeAction()
	{
		if ($this->get('request')->getMethod() != 'POST' || empty($_POST))
		{
			throw new AccessDeniedHttpException();
		}

		$options['nom'] = current($_POST)
			? trim(current($_POST))
			: null;
		$options['couleur'] = !(bool)$options['nom'];

		$q = \Doctrine_Query::create()
			->select('t.nom')
			->from('Tag t')
			->orderBy('t.nom ASC');

		if ($options['couleur'])
		{
			$q->addWhere('t.couleur <> ?', '');
		}
		if ($options['nom'])
		{
			$debut = str_replace(array('%', '_'),
			                     array('%%', '\\_'),
			                     $options['nom']);
			$q->addWhere('t.nom LIKE ?', $debut.'%');
		}

		$retour = array();
		foreach ($q->execute(array(), \Doctrine_Core::HYDRATE_SCALAR) as $t)
		{
			$retour[] = $t['t_nom'];
		}
		
		$response = new Response();
		$response->headers->set('Content-type', 'application/json');
		$response->setContent(json_encode($retour));
		
		return $response;
	}

/*	public function executeAjaxAjouter()
	{
		if ($this->get('request')->getMethod() != 'POST'
		 || empty($_POST['nom']))
		{
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		$_POST['nom'] = trim($_POST['nom']);

		$existing = Doctrine_Core::getTable('Tag')->findOneByNom($_POST['nom']);
		if ($existing)
			return new Symfony\Component\HttpFoundation\Response((string)$existing->id);

		$Tag = new Tag;
		$Tag->utilisateur_id = $_SESSION['id'];
		$Tag->couleur = '';
		$Tag->nom = $_POST['nom'];
		$Tag->save();

		return new Symfony\Component\HttpFoundation\Response((string)$Tag->id);
	}
*/
}
