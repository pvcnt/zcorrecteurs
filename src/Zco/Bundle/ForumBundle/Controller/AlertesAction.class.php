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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\ForumBundle\Domain\AlertDAO;
use Zco\Bundle\ForumBundle\Domain\TopicDAO;

/**
 * Contrôleur se chargeant de la visualisation des alertes sur un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AlertesAction extends ForumActions
{
	public function execute()
	{
		if (!verifier('voir_alertes')) {
            throw new AccessDeniedHttpException();
        }

		//Si un sujet a été envoyé
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosSujet = TopicDAO::InfosSujet($_GET['id']);
			if(empty($InfosSujet))
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();

			if(!verifier('voir_alertes', $InfosSujet['sujet_forum_id']))
				throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
		}
		else
		{
			$InfosSujet = null;
		}

		//Si on veut marquer en résolu une alerte
		if(!empty($_GET['resolu']) && is_numeric($_GET['resolu']))
		{
		    AlertDAO::AlerteResolue($_GET['resolu'], $_SESSION['id']);

            return redirect(
                'L\'alerte a bien été marquée comme résolue.',
                (!empty($InfosSujet) ? 'alertes-'.$_GET['id'].'.html' : 'alertes.html')
            );
		}

		//Si on veut marquer en non-résolu une alerte
		if(!empty($_GET['nonresolu']) && is_numeric($_GET['nonresolu']))
		{
            AlertDAO::AlerteNonResolue($_GET['resolu']);

            return redirect(
                'L\'alerte a bien été marquée comme non résolue.',
                (!empty($InfosSujet) ? 'alertes-'.$_GET['id'].'.html' : 'alertes.html')
            );
		}

		if(!empty($InfosSujet))
			Page::$titre = $InfosSujet['sujet_titre'].' - Voir les alertes';
		else
			Page::$titre = 'Voir les alertes du forum';

		$sujet_id = !empty($InfosSujet) ? $_GET['id'] : null;
		$solved = isset($_GET['solved']) ? (boolean)$_GET['solved'] : null;
		$Alertes = \Doctrine_Core::GetTable('ForumAlerte')->ListerAlertes($solved, $sujet_id);

		//Inclusion de la vue
		if(!empty($InfosSujet))
			fil_ariane($InfosSujet['sujet_forum_id'], array(
				htmlspecialchars($InfosSujet['sujet_titre']) => 'sujet-'.$_GET['id'].'-'.rewrite($InfosSujet['sujet_titre']).'.html',
				'Voir la liste des alertes'
			));
		else
			fil_ariane('Voir la liste des alertes');

		return render_to_response('ZcoForumBundle::alertes.html.php', array(
			'InfosSujet' => $InfosSujet,
			'Alertes' => $Alertes,
		));
	}
}
