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

namespace Zco\Bundle\AuteursBundle\Controller;

use Zco\Bundle\CoreBundle\Generator\Generator;

/**
 * Gestion des auteurs.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class DefaultController extends Generator
{
	protected $modelName = 'Auteur';

	public function indexAction()
	{
		return $this->executeList();
	}

	public function ajouterAction()
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
				$Auteur = new \Auteur();
				foreach ($vars as $column => $value)
					$Auteur[$column] = $value;
				$Auteur['utilisateur_id'] = $_SESSION['id'];
				$Auteur->save();
			}
		}

		fil_ariane('Ajouter');
		
		return render_to_response('ZcoAuteursBundle::ajouter-mini.html.php', compact('Auteur'));
	}

	public function modifierAction()
	{
        \zCorrecteurs::VerifierFormatageUrl(null, true);
		return $this->executeEdit($_GET['id']);
	}

	public function supprimerAction()
	{
        \zCorrecteurs::VerifierFormatageUrl(null, true);
		return $this->executeDelete($_GET['id']);
	}

	// Ressources liées à un auteur
	public function auteurAction()
	{
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$Auteur = \Doctrine_Core::getTable('Auteur')->find($_GET['id']);
			if($Auteur === false)
				return redirect(1, 'index.html', MSG_ERROR, -1);

			\Page::$titre = htmlspecialchars($Auteur);
            \zCorrecteurs::VerifierFormatageUrl($Auteur->__toString(), true);
			
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
