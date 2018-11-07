<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur gérant l'ajout d'un dossier de MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class AjouterDossierAction extends Controller
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		include(__DIR__.'/../modeles/dossiers.php');
		
		if(isset($_POST['dossier_nom']))
		{
			$_POST['dossier_nom'] = trim($_POST['dossier_nom']);
		}
		if(!isset($_POST['dossier_nom']) OR empty($_POST['dossier_nom']))
		{
			//Inclusion de la vue
			fil_ariane('Ajouter un dossier');
			Page::$titre = 'Ajout d\'un dossier - '.Page::$titre;
			
			return $this->render('ZcoMpBundle::ajouterDossier.html.php');
		}
		else
		{
			//On ajoute le dossier
			if(!empty($_POST['dossier_nom']))
			{
				AjouterDossier(htmlspecialchars($_POST['dossier_nom']));
				return redirect('Le dossier a bien été ajouté.', 'index.html');
			}
		}
	}
}
