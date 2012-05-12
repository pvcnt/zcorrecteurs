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

/**
 * Contrôleur pour le marquage d'un sujet ponctuel comme étant non-lu
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class MarquerSujetNonLuAction extends ForumActions
{
	public function execute()
	{
            	//Vérification du token.
		if(empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/sujets.php');
		include(dirname(__FILE__).'/../modeles/membres.php');

		//Si on n'a pas envoyé de sujet
		if(empty($_GET['id']) || !is_numeric($_GET['id']))
			return redirect(45, '/forum/', MSG_ERROR);

		$InfosSujet = InfosSujet($_GET['id']);
		if(empty($InfosSujet) || !verifier('voir_sujets', $InfosSujet['sujet_forum_id']))
			return redirect(47, '/forum/', MSG_ERROR);

                if(!$InfosSujet['lunonlu_utilisateur_id'])
                        return redirect(354, 'forum-'.$InfosSujet['sujet_forum_id'].'.html', MSG_ERROR);

                zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true);

                MarquerSujetLu($_GET['id'], false);
                return redirect(356, 'forum-'.$InfosSujet['sujet_forum_id'].'.html');
        }
}
