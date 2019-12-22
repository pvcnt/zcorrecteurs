<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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
 * Contrôleur pour la suppression d'un sondage.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SupprimerSondageAction extends ForumActions
{
	public function execute()
	{
		//Inclusion du modèle
		include(dirname(__FILE__).'/../modeles/sondages.php');

		//Si aucun sondage n'a été envoyé
		if(empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(94, '/forum/', MSG_ERROR);
		}

		//Si on n'a pas le droit de le voir
		$InfosSondage = InfosSondage($_GET['id']);
		if(!$InfosSondage)
		{
			return redirect(95, '/forum/', MSG_ERROR);
		}

		if(verifier('supprimer_sondages', $InfosSondage['cat_id']))
		{
			//On supprime le sondage
			SupprimerSondage($_GET['id']);
			return redirect(97, 'sujet-'.$InfosSondage['sujet_id'].'-'.rewrite($InfosSondage['sujet_titre']).'.html');
		}
		else
		{
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
	}
}
