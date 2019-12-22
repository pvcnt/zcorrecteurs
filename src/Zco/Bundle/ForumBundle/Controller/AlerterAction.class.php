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
 * Contrôleur gérant l'alerte des modérateurs sur un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AlerterAction extends ForumActions
{
	public function execute()
	{
		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/sujets.php');

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosSujet = InfosSujet($_GET['id']);
			$InfosForum = InfosCategorie($InfosSujet['sujet_forum_id']);
			if(empty($InfosSujet))
				return redirect(47, '/forum/', MSG_ERROR);

			zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true);
			Page::$titre .= ' - '.$InfosSujet['sujet_titre'].' - Alerter les modérateurs';
			
			//Mise à jour de la position sur le site.
			\Doctrine_Core::getTable('Online')->updateUserPosition($_SESSION['id'], 'ZcoForumBundle:sujet', $_GET['id']);

			if(verifier('signaler_sujets', $InfosSujet['sujet_forum_id']))
			{
				//Si le sujet est fermé
				if($InfosSujet['sujet_ferme'])
					return redirect(41, 'sujet-'.$InfosSujet['sujet_id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);
				//S'il y a déjà une alerte en cours
				elseif(!Doctrine_Core::getTable('ForumAlerte')->VerifierAutorisationAlerter($_GET['id']))
					return redirect(42, 'sujet-'.$InfosSujet['sujet_id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);

				//Si on veut signaler le sujet
				if(isset($_POST['send']))
				{
					if(empty($_POST['texte']))
						return redirect(17, 'sujet-'.$InfosSujet['sujet_id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', MSG_ERROR);

					$alerte = new ForumAlerte;
					$alerte['sujet_id'] = $_GET['id'];
					$alerte['resolu'] = false;
					$alerte['raison'] = $_POST['texte'];
					$alerte['ip'] = ip2long($this->get('request')->getClientIp(true));
					$alerte->save();

					return redirect(40, 'sujet-'.$InfosSujet['sujet_id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html');
				}
				//Inclusion de la vue
				fil_ariane($InfosSujet['sujet_forum_id'], array(htmlspecialchars($InfosSujet['sujet_titre']) => 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html', 'Alerter les modérateurs'));
				
				return render_to_response(array(
					'tabindex_zform' => 1,
					'InfosSujet' => $InfosSujet,
					'InfosForum' => $InfosForum,
				));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(45, '/forum/', MSG_ERROR);
	}
}
