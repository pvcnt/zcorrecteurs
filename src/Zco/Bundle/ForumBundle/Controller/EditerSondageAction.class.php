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
 * Contrôleur pour l'édition d'un sondage.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditerSondageAction extends ForumActions
{
	public function execute()
	{
		include(dirname(__FILE__).'/../modeles/sondages.php');

		if(empty($_GET['id']) || !is_numeric($_GET['id']))
			return redirect(94, '/forum/', MSG_ERROR);

		// Pas le droit
		$InfosSondage = InfosSondage($_GET['id']);
		if (!$InfosSondage)
		{
			return redirect(95, '/forum/', MSG_ERROR);
		}
		
		zCorrecteurs::VerifierFormatageUrl($InfosSondage['sujet_titre'], true);
		Page::$titre = $InfosSondage['sondage_question'].' - Modification d\'un sondage';
		$ListerQuestions = ListerQuestions($_GET['id']);
		
		//Mise à jour de la position sur le site.
		\Doctrine_Core::getTable('Online')->updateUserPosition($_SESSION['id'], 'ZcoForumBundle:sujet', $InfosSondage['sujet_id']);

		if(empty($_POST['send']) || $_POST['send'] != 'Modifier')
		{
			fil_ariane($InfosSondage['cat_id'], array(
				htmlspecialchars($InfosSondage['sujet_titre']) => 'sujet-'.$InfosSondage['sujet_id'].'-'.rewrite($InfosSondage['sujet_titre']).'.html',
				'Modification du sondage <em>'.htmlspecialchars($InfosSondage['sondage_question']).'</em>'
			));
			
			return render_to_response(array(
				'InfosSondage' => $InfosSondage,
				'ListerQuestions' => $ListerQuestions,
			));
		}
		else // Formulaire envoyé
		{
			$url = 'editer-sondage-'.$_GET['id'].'-'
				.rewrite($InfosSondage['sondage_question'])
				.'.html';

			// Question vide
			if(empty($_POST['question']))
				return redirect(100, $url, MSG_ERROR);

			// Nettoyage des réponses
			$reponses = isset($_POST['reponses']) ? $_POST['reponses'] : array();
			foreach($reponses as $k => &$v)
			{
				$v = trim($v);
				if($v == '')
					unset($reponses[$k]);
			}

			// Moins de deux réponses
			if(count($reponses) < 2)
				return redirect(99, $url, MSG_ERROR);

			// Enregistrement du sondage modifié
			ModifierSondage($InfosSondage, $ListerQuestions, $reponses);
			return redirect(96, 'sujet-'.$InfosSondage['sujet_id'].'-'
				.rewrite($InfosSondage['sujet_titre']).'.html');
		}
	}
}
