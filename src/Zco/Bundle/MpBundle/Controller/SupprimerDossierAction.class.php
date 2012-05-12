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
