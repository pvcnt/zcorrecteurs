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
 * Contrôleur gérant le déplacement d'un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DeplacerAction extends ForumActions
{
	public function execute()
	{
		//On récupère les infos sur le sujet.
		list($InfosSujet, $InfosForum) = $this->initSujet();
		if ($InfosSujet instanceof Response)
			return $InfosSujet;
		include(dirname(__FILE__).'/../modeles/moderation.php');

		zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true);

		if(verifier('deplacer_sujets', $InfosSujet['sujet_forum_id']))
		{
			//Forum cible non envoyé.
			if(empty($_POST['forum_cible']) || !is_numeric($_POST['forum_cible']))
				return redirect(49, 'sujet-'.$_GET['id'].'.html', MSG_ERROR);

			//Si on n'a pas le droit de voir un des deux forums.
			elseif(!verifier('voir_sujets', $InfosSujet['sujet_forum_id']) || !verifier('voir_sujets', $_POST['forum_cible']))
				return redirect(50, 'sujet-'.$_GET['id'].'.html', MSG_ERROR);

			//Si forum source et cible sont identiques.
			elseif($InfosSujet['sujet_forum_id'] == $_POST['forum_cible'])
				return redirect(75, 'sujet-'.$_GET['id'].'.html', MSG_ERROR);

			//Si sujet en corbeille.
			elseif($InfosSujet['sujet_corbeille'])
				return redirect(70, 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);


			DeplacerSujet($_GET['id'], $InfosSujet['sujet_forum_id'], $_POST['forum_cible']);
			return redirect(55, 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html');
		}
		else
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	}
}
