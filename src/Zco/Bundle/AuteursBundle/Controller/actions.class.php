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
		return $this->executeList();
	}

	public function executeAjouter()
	{
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
		zCorrecteurs::VerifierFormatageUrl(null, true);
		return $this->executeEdit($_GET['id']);
	}

	public function executeSupprimer()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		return $this->executeDelete($_GET['id']);
	}

	// Ressources liées à un auteur
	public function executeAuteur()
	{
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$Auteur = Doctrine_Core::getTable('Auteur')->find($_GET['id']);
			if($Auteur === false)
				return redirect(1, 'index.html', MSG_ERROR, -1);

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
			return redirect(1, 'index.html', MSG_ERROR, -1);
	}
}
