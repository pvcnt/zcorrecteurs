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
class DetailMessagesAction extends ForumActions
{
	public function execute()
	{
		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/sujets.php');
		include(dirname(__FILE__).'/../modeles/membres.php');

		$InfosUtilisateur = InfosUtilisateur($_GET['id']);
		if(empty($InfosUtilisateur))
			return redirect(123, '/forum/', MSG_ERROR);

		zCorrecteurs::VerifierFormatageUrl($InfosUtilisateur['utilisateur_pseudo'], true);
		Page::$titre .= ' - Détail de l\'activité de '.$InfosUtilisateur['utilisateur_pseudo'].' sur les forums';
		Page::$description = 'Obtenez un aperçu de l\'activité de '.htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']).' sur les forums de zCorrecteurs.fr';

		//Inclusion de la vue
		fil_ariane('Détail de l\'activité de '.$InfosUtilisateur['utilisateur_pseudo']);
		
		return render_to_response(array(
			'DetailMessages' => MessagesParForum(),
			'InfosUtilisateur' => $InfosUtilisateur,
		));
	}
}
