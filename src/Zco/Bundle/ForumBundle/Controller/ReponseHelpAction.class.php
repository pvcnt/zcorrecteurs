<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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
use Zco\Bundle\ForumBundle\Controller\BaseController;

/**
 * Contrôleur chargé du changement du statut ayant aidé ou non d'une
 * réponse à un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ReponseHelpAction extends BaseController
{
	public function execute()
	{
		//On récupère les infos sur le sujet.
		include(dirname(__FILE__).'/../modeles/moderation.php');
		include(dirname(__FILE__).'/../modeles/messages.php');
		list($InfosSujet, $InfosForum) = $this->initSujet();
		if ($InfosSujet instanceof Response)
			return $InfosSujet;

		zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true, true);

		//Vérification du token.
		if(empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		if(	(	$_SESSION['id'] == $InfosSujet['sujet_auteur'] &&
				verifier('indiquer_ses_messages_aide', $InfosSujet['sujet_forum_id'])
			) ||
			verifier('indiquer_messages_aide', $InfosSujet['sujet_forum_id'])
		)
		{
			if(empty($_GET['id2']) || !is_numeric($_GET['id2']))
				return redirect(44, 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);
			elseif(!VerifierValiditeMessage($_GET['id2']))
				return redirect(46, 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);


			ChangerHelp($_GET['id2'], $_GET['help_souhaite']);
			return redirect(($_GET['help_souhaite'] ? 292 : 293), 'sujet-'.$_GET['id'].'-'.$_GET['id2'].'-'.rewrite($InfosSujet['sujet_titre']).'.html');
		}
		else
		{
			return redirect(70, 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);
		}
	}
}
