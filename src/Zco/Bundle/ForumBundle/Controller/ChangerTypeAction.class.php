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

use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur chargé du changement du statut annonce d'un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ChangerTypeAction extends ForumActions
{
	public function execute()
	{
		//On récupère les infos sur le sujet.
		list($InfosSujet, $InfosForum) = $this->initSujet();
		include(dirname(__FILE__).'/../modeles/moderation.php');

		zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true);

		//Vérification du token.
		if(empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

		if(verifier('epingler_sujets', $InfosSujet['sujet_forum_id']))
		{
			ChangerTypeSujet($_GET['id'], $InfosSujet['sujet_annonce']);
			return redirect(
			    ($InfosSujet['sujet_annonce'] ? 'Le sujet a bien été désépinglé.' : 'Le sujet a bien été épinglé.'),
                'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html'
            );
		}
		else
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	}
}
