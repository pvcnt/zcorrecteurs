<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

use Zco\Bundle\ForumBundle\Domain\TopicDAO;

/**
 * Contrôleur chargé du changement du statut résolu d'un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ChangerResoluAction extends ForumActions
{
	public function execute()
	{
		//On récupère les infos sur le sujet.
		list($InfosSujet, $InfosForum) = $this->initSujet();

		//Vérification du token.
		if(empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();

		if(
			(
				$_SESSION['id'] == $InfosSujet['sujet_auteur']
				AND
				verifier('resolu_ses_sujets', $InfosSujet['sujet_forum_id'])
			)
			OR
			verifier('resolu_sujets', $InfosSujet['sujet_forum_id'])
		)
		{
            TopicDAO::ChangerResoluSujet($_GET['id'], $InfosSujet['sujet_resolu']);
			return redirect(
			    ($InfosSujet['sujet_resolu'] ? 'Le sujet a bien été marqué comme non résolu.' : 'Le sujet a bien été marqué comme résolu.'),
                'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html'
            );
		}
		else
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	}
}
