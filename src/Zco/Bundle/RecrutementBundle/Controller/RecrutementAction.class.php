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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant l'affichage de toutes les informations relatives à un
 * recrutement et de la liste des candidatures.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>, Vanger
 */
class RecrutementAction extends Controller
{
	public function execute()
	{
		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(228, '/recrutement/', MSG_ERROR);
		}

		$recrutement = Doctrine_Core::getTable('Recrutement')->recuperer($_GET['id']);
		if (!$recrutement || ($recrutement['etat'] == \Recrutement::CACHE && !verifier('recrutements_editer') && !verifier('recrutements_voir_candidatures')))
		{
			return redirect(229, '/recrutement/', MSG_ERROR);
		}

		\zCorrecteurs::VerifierFormatageUrl($recrutement['nom'], true);
		\Page::$titre = htmlspecialchars($recrutement['nom']);
		
		//Insertion d'une description de la page.
		$description = strip_tags($recrutement['texte']);
		if (mb_strlen($description) > 240)
		{
			\Page::$description = htmlspecialchars(mb_substr($description, 0, mb_strpos($description, ' ', (mb_strlen($description) > 250 ? 240 : 250))));
		}
		else
		{
			\Page::$description = htmlspecialchars($description);
		}

		$recrutement->incrementerNbLus();
		
		fil_ariane(htmlspecialchars($recrutement['nom']));
		
		return render_to_response(array(
			'maCandidature' => verifier('connecte') ? Doctrine_Core::getTable('RecrutementCandidature')->recupererRecrutementUtilisateur($recrutement['id'], $_SESSION['id']) : null,
			'candidatures' => verifier('recrutements_voir_candidatures') ? Doctrine_Core::getTable('RecrutementCandidature')->listerRecrutement($recrutement['id'], !empty($_GET['tri']) ? $_GET['tri'] : 'etat') : null,
			'recrutement' => $recrutement,
		));
	}
}
