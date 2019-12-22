<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

/**
 * Contrôleur gérant le renommage d'un dossier de MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class RenommerDossierAction extends Controller
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/dossiers.php');

		if(!empty($_GET['id']) AND is_numeric($_GET['id']))
		{
			$DossierExiste = DossierExiste();
			if($DossierExiste)
			{
				if(isset($_POST['dossier_nom']))
				{
					$_POST['dossier_nom'] = trim($_POST['dossier_nom']);
				}
				if(!isset($_POST['dossier_nom']) OR empty($_POST['dossier_nom']))
				{
					//Inclusion de la vue
					fil_ariane('Renommer un dossier');
					Page::$titre = $DossierExiste['mp_dossier_titre'].' - Renommer un dossier - '.Page::$titre;
					
					return render_to_response(array('DossierExiste' => $DossierExiste));
				}
				else
				{
					RenommerDossier(htmlspecialchars($_POST['dossier_nom']));
					return redirect(259, 'index.html');
				}
			}
			else
			{
				return redirect(257, 'index.html', MSG_ERROR);
			}
		}
		else
		{
			return redirect(257, 'index.html', MSG_ERROR);
		}
	}
}
