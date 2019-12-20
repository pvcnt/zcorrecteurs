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

namespace Zco\Bundle\IpsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Actions gérant les actions liées à l'analyse et au bannissement des
 * adresses IP.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
	/**
	 * Affiche la liste des adresses IP bannies.
	 */
	public function indexAction()
	{
		\zCorrecteurs::VerifierFormatageUrl();
		\Page::$titre = 'Liste des adresses IP bannies';

		//Si on veut débannir une IP
		if(!empty($_GET['fin']) && verifier('ips_bannir'))
		{
			DebannirIP($_GET['fin']);
			return redirect(3);
		}

		//Si on veut supprimer une IP
		if(!empty($_GET['supprimer']) && verifier('ips_bannir'))
		{
			SupprimerIP($_GET['supprimer']);
			return redirect(4);
		}

		//Inclusion de la vue
		fil_ariane('Liste des adresses IP bannies');
		
		return render_to_response(array(
			'ListerIPs' => ListerIPsBannies(
				(isset($_GET['fini']) && is_numeric($_GET['fini'])) ? $_GET['fini'] : null,
				!empty($_GET['ip']) ? $_GET['ip'] : null
			))
		);
	}

	/**
	 * Analyse une adresse IP en trouvant toutes ses occurences dans la BDD.
	 */
	public function analyserAction()
	{
		\zCorrecteurs::VerifierFormatageUrl();
		\Page::$titre = 'Analyser une adresse IP';

		if (!empty($_GET['ip']))
		{
			$utilisateurs = \Doctrine_Core::getTable('UtilisateurIp')->findByIP($_GET['ip']);
			list($pays, ) = Geolocaliser(ip2long($_GET['ip']));
		}
		else
		{
			$utilisateurs = array();
			$pays = null;
		}

		fil_ariane('Analyser une adresse IP');
		
		return render_to_response(array(
			'utilisateurs' => $utilisateurs,
			'nombre' => count($utilisateurs),
			'pays' => $pays,
		));
	}

	/**
	 * Affiche le formulaire permettant de bannir une adresse IP.
	 */
	public function bannirAction()
	{
		\zCorrecteurs::VerifierFormatageUrl();
		\Page::$titre = 'Bannir une adresse IP';

		//Si on a posté une nouvelle IP à bannir
		if (!empty($_POST['ip']) && is_numeric($_POST['duree']))
		{
			if (BannirIp($_POST['ip'], $_POST['raison'], $_POST['texte'], $_POST['duree']))
			{
				return redirect(2, 'index.html');
			}
			else
			{
				return redirect(5, '', MSG_ERROR, -1);
			}
		}

		fil_ariane('Bannir une nouvelle adresse IP');
		
		return render_to_response(array());
	}

	/**
	 * Affiches les doublons d'IP
	 *
	 * @author Skydreamer
	 */
	public function doublonsAction()
	{
		\zCorrecteurs::VerifierFormatageUrl();
		\Page::$titre = 'Rechercher les doublons d\'adresses IP';

		fil_ariane('Rechercher les doublons d\'adresses IP');
		
		return render_to_response(array(
		    'doublons' => getDoublons()
		));
	}
}
