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
class DetailMessagesAction extends ForumActions
{
	public function execute()
	{
		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/sujets.php');
		include(dirname(__FILE__).'/../modeles/membres.php');
        include_once(__DIR__.'/../../UserBundle/modeles/utilisateurs.php');

		$InfosUtilisateur = InfosUtilisateur($_GET['id']);
		if(empty($InfosUtilisateur))
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();

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
