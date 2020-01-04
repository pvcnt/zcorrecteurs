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

use Zco\Bundle\ForumBundle\Controller\BaseController;

/**
 * Contrôleur gérant la division d'un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DiviserAction extends BaseController
{
	public function execute()
	{
		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/sujets.php');
		include(dirname(__FILE__).'/../modeles/forums.php');
		include(dirname(__FILE__).'/../modeles/moderation.php');
		include(dirname(__FILE__).'/../modeles/categories.php');

		if(empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(45, '/forum/', MSG_ERROR);
		}
		else
		{
			$InfosSujet = InfosSujet($_GET['id']);
			if(!$InfosSujet)
			{
				return redirect(47, '/forum/', MSG_ERROR);
			}
			zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true);
		}

		Page::$titre = $InfosSujet['sujet_titre'].' - Diviser le sujet';
		
		//Mise à jour de la position sur le site.
		\Doctrine_Core::getTable('Online')->updateUserPosition($_SESSION['id'], 'ZcoForumBundle:sujet', $_GET['id']);

		if(verifier('diviser_sujets', $InfosSujet['sujet_forum_id']))
		{
			//Si on veut diviser le sujet
			if(isset($_POST['submit']))
			{
				//Si des champs sont vides
				if(empty($_POST['titre']) || empty($_POST['msg']) || empty($_POST['forum']) || !is_numeric($_POST['forum']))
					return redirect(17, 'diviser-'.$_GET['id'].'.html', MSG_ERROR);

				//Si le forum n'existe pas
				$InfosForum = InfosCategorie($_POST['forum']);
				if(empty($InfosForum) || !verifier('voir_sujets', $InfosForum['cat_id']))
					return redirect(50, '/forum/', MSG_ERROR);

				//Si tout va bien on divise
				DiviserSujet($InfosSujet, $InfosSujet['sujet_corbeille']);
				return redirect(255, 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html');
			}

			$ListerCategories = ListerCategoriesForum();
			$ListerMessages = ListerMessages($_GET['id'], 0, $InfosSujet['nombre_de_messages']);

			if(count($ListerMessages) < 2)
				return redirect(261, 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);

			//Inclusion de la vue
			fil_ariane($InfosSujet['sujet_forum_id'], array(
				htmlspecialchars($InfosSujet['sujet_titre']) => 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html',
				'Diviser le sujet'
			));
			
			return render_to_response(array(
				'ListerCategories' => $ListerCategories,
				'ListerMessages' => $ListerMessages,
				'InfosSujet' => $InfosSujet,
			));

		}
		else
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	}
}
