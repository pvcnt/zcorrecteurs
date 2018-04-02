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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\CoreBundle\Generator\Generator;

/**
 * Gestion des auteurs.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class AuteursActions extends Generator
{
	protected $modelName = 'Auteur';

	public function executeIndex()
	{
	    if (!verifier('auteurs_modifier')) {
	        throw new AccessDeniedHttpException();
        }
		return $this->executeList();
	}

	public function executeAjouter()
	{
        if (!verifier('auteurs_modifier')) {
            throw new AccessDeniedHttpException();
        }

		if (empty($_GET['id']))
			return $this->executeNew();

		$Auteur = null;

		$vars = array('prenom', 'nom', 'autres', 'description');
		if (check_post_vars($vars))
		{
			$vars = array_trim($_POST, $vars);

			if ($vars['nom'] != '')
			{
				$Auteur = new Auteur();
				foreach ($vars as $column => $value)
					$Auteur[$column] = $value;
				$Auteur['utilisateur_id'] = $_SESSION['id'];
				$Auteur->save();
			}
		}

		fil_ariane('Ajouter');
		
		return render_to_response('ZcoAuteursBundle::ajouter-mini.html.php', compact('Auteur'));
	}

	public function executeModifier()
	{
        if (!verifier('auteurs_modifier')) {
            throw new AccessDeniedHttpException();
        }
		return $this->executeEdit($_GET['id']);
	}

	public function executeSupprimer()
	{
        if (!verifier('auteurs_modifier')) {
            throw new AccessDeniedHttpException();
        }
		return $this->executeDelete($_GET['id']);
	}

	// Ressources liées à un auteur
	public function executeAuteur()
	{
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$Auteur = Doctrine_Core::getTable('Auteur')->find($_GET['id']);
			if($Auteur === false)
				throw new NotFoundHttpException();

			Page::$titre = htmlspecialchars($Auteur);
			zCorrecteurs::VerifierFormatageUrl($Auteur->__toString(), true);
			
			//Inclusion de la vue
			fil_ariane(array(
				htmlspecialchars($Auteur) => 'auteur-'.$Auteur['id'].'-'
				.rewrite($Auteur->__toString()).'.html',
				'Voir les ressources liées'
			));
			
			return render_to_response(array(
				'Auteur' => $Auteur,
				'Ressources' => $Auteur->listerRessourcesLiees(),
			));
		}
		else
			throw new NotFoundHttpException();
	}
}
