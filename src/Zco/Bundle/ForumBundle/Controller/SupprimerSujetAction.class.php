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
 * Contrôleur pour la suppression d'un sujet.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class SupprimerSujetAction extends ForumActions
{
	public function execute()
	{
		list($InfosSujet, $InfosForum) = $this->initSujet();
		if ($InfosSujet instanceof Response)
			return $InfosSujet;
		include(dirname(__FILE__).'/../modeles/moderation.php');

		if(verifier('suppr_sujets', $InfosSujet['sujet_forum_id']))
		{
			zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true);
			if(isset($_POST['confirmer']))
			{
				Supprimer($InfosSujet['sujet_id'], $InfosSujet['sujet_forum_id'], $InfosSujet['sujet_corbeille']);
				return redirect(60, 'forum-'.$InfosSujet['sujet_forum_id'].'-'.rewrite($InfosForum['cat_nom']).'.html');
			}
			elseif(isset($_POST['annuler']))
			{
				return new Symfony\Component\HttpFoundation\RedirectResponse('sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html');
			}

			//Inclusion de la vue
			fil_ariane($InfosSujet['sujet_forum_id'], array(
				htmlspecialchars($InfosSujet['sujet_titre']) => 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html',
				'Supprimer le sujet'
			));
			return render_to_response(array(
				'InfosSujet' => $InfosSujet,
				'InfosForum' => $InfosForum,
			));
		}
		else
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	}
}
