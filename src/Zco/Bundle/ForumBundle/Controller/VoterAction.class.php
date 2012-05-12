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
 * Contrôleur pour le vote (vérification et enregistrement du vote).
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 *         vincent1870 <vincent@zcorrecteurs.fr>
 *         Ziame <ziame@zcorrecteurs.fr>
 */
class VoterAction extends ForumActions
{
	public function execute()
	{
		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/votes.php');
		include(dirname(__FILE__).'/../modeles/sujets.php');

		//Si son choix est vide
		if((empty($_POST['choix']) || !is_numeric($_POST['choix'])) && isset($_POST['voter']) && ! empty($_POST['s']) && is_numeric($_POST['s']))
		{
			$InfosSujet = InfosSujet($_POST['s']);
			return redirect(93, 'sujet-'.$_POST['s'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);
		}
		//Si son choix n'est pas valide
		elseif(isset($_POST['voter']) && !VerifierValiditeChoix($_POST['choix']) && ! empty($_POST['s']) && is_numeric($_POST['s']))
		{
			$InfosSujet = InfosSujet($_POST['s']);
			return redirect(92, 'sujet-'.$_POST['s'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);
		}
		//Si aucun sondage n'a été envoyé
		elseif(empty($_POST['sondage']) || !is_numeric($_POST['sondage']) && ! empty($_POST['s']) && is_numeric($_POST['s']))
		{
			$InfosSujet = InfosSujet($_POST['s']);
			return redirect(94, 'sujet-'.$_POST['s'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);
		}
		//Si aucun sujet n'a été envoyé
		elseif(empty($_POST['s']) || !is_numeric($_POST['s']))
		{
			return redirect(45, '/forum/');
		}

		//S'il n'y a pas d'erreur, on enregistre le vote !
		if(Voter($_POST['sondage'], (isset($_POST['blanc']) ? 0 : $_POST['choix'])))
		{
			$InfosSujet = InfosSujet($_POST['s']);
			return redirect(91, 'sujet-'.$_POST['s'].'-'.rewrite($InfosSujet['sujet_titre']).'.html');
		}
		else
		{
			$InfosSujet = InfosSujet($_POST['s']);
			return redirect(90, 'sujet-'.$_POST['s'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);
		}
	}
}
