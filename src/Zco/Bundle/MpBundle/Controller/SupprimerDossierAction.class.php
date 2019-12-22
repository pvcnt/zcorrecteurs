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
 * Contrôleur gérant la suppression d'un dossier de MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class SupprimerDossierAction extends Controller
{
	public function execute()
	{
		if(isset($_POST['annuler']))
		{
			return new Symfony\Component\HttpFoundation\RedirectResponse('index.html');
		}
		zCorrecteurs::VerifierFormatageUrl(null, true);
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/dossiers.php');

		if(!empty($_GET['id']) AND is_numeric($_GET['id']))
		{
			$DossierExiste = DossierExiste();
			if($DossierExiste)
			{
				if(!isset($_POST['confirmation']))
				{
					//Inclusion de la vue
					fil_ariane('Supprimer un dossier');
					Page::$titre = $DossierExiste['mp_dossier_titre'].' - Suppression du dossier - '.Page::$titre;
					
					return render_to_response(array('DossierExiste' => $DossierExiste));
				}
				else
				{
					SupprimerDossier();
					return redirect(260, 'index.html');
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
