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

/**
 * Contrôleur gérant l'affichage des tutoriels en cours de correction
 * ou corrigés.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CorrectionsAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, false, false, 1);
		Page::$titre .= ' - Voir les tutoriels';

		//Si on demande à voir les corrections d'un membre
		$InfosMembre = null;
		$id = null;
		$pseudo = null;
		if(!empty($_GET['zco']) || !empty($_POST['zco']))
		{
			$InfosMembre = InfosUtilisateur(!empty($_GET['zco']) ? $_GET['zco'] : $_POST['zco']);
			if(empty($InfosMembre))
				return redirect(123, '', MSG_ERROR);
			$id = $InfosMembre['utilisateur_id'];
		}

		//Si on demande à voir les soumissions d'un membre
		$id2 = null;
		$InfosMembre2 = null;
		if(!empty($_GET['auteur']) || !empty($_POST['auteur']))
		{
			$InfosMembre2 = InfosUtilisateur(!empty($_GET['auteur']) ? $_GET['auteur'] : $_POST['auteur']);
			$id2 = $InfosMembre2['utilisateur_id'];
			$pseudo = !empty($_GET['auteur']) ? $_GET['auteur'] : $_POST['auteur'];
		}

		//On liste les soumissions
		if(isset($_GET['is_zcorrecting']) && $_GET['is_zcorrecting'] == 1)
			$Etat = CORRECTION;
		elseif(isset($_GET['zcorrected']) && $_GET['zcorrected'] == 1)
			$Etat = TERMINE_CORRIGE;
		elseif(isset($_POST['etat']))
			$Etat = (int)$_POST['etat'];
		else
			$Etat = ALL;

		$page = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
		$ListerSoumissions = ListerSoumissionsCorrigees($page, $Etat, $id, $id2, $pseudo);
		$CompterSoumissions = CompterSoumissionsCorrigees($Etat, $id, $id2, $pseudo);
		$ListePages = liste_pages($page, ceil($CompterSoumissions / 30), $CompterSoumissions, 30, 'corrections-p%s.html'.(!is_null($id) ? '?zco='.$InfosMembre['utilisateur_pseudo'] : ''));

		//Inclusion de la vue
		fil_ariane('Voir les tutoriels');
		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoForumBundle/Resources/public/css/forum.css',
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
		));
		
		return render_to_response(array(
			'ListerSoumissions' => $ListerSoumissions,
			'CompterSoumissions' => $CompterSoumissions,
			'ListePages' => $ListePages,
			'Etat' => $Etat,
			'id' => $id,
			'InfosMembre' => $InfosMembre,
			'InfosMembre2' => $InfosMembre2,
			'id' => $id,
			'id2' => $id2,
			'pseudo' => $pseudo,
		));
	}
}
