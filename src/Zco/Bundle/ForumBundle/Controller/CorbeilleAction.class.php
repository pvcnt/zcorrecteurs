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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur chargé du changement de la mise en corbeille ou de la
 * restauration d'un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CorbeilleAction extends ForumActions
{
	public function execute()
	{
		//On récupère les infos sur le sujet.
		list($InfosSujet, $InfosForum) = $this->initSujet();
		include(dirname(__FILE__).'/../modeles/moderation.php');
		include(dirname(__FILE__).'/../modeles/messages.php');

		//Vérification du token.
		if(empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
			throw new AccessDeniedHttpException();

		if(!empty($_GET['id2']) && $_GET['id2'] == 1)
		{
			if(verifier('corbeille_sujets', $InfosSujet['sujet_forum_id']))
			{
				Corbeille($InfosSujet['sujet_id'], $InfosSujet['sujet_forum_id']);
				return redirect('Le sujet a bien été mis en corbeille.', 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html');
			}
			else
                throw new AccessDeniedHttpException();
		}
		else
		{
			if(verifier('corbeille_sujets', $InfosSujet['sujet_forum_id']))
			{
				Restaurer($InfosSujet['sujet_id'], $InfosSujet['sujet_forum_id']);
				return redirect('Le sujet a bien été mis en restauré.', 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html');
			}
			else
                throw new AccessDeniedHttpException();
		}
	}
}
