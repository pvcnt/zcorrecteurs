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

/**
 * Contrôleur gérant la modification d'un sondage. Permet de modifier
 * à la fois les propriétés générales du sondages, mais aussi les *
 * questions appartenant au sondage.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ModifierAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$sondage = Doctrine_Core::getTable('Sondage')->find($_GET['id']);
			if ($sondage == false)
				return redirect(7, 'index.html', MSG_ERROR);

			if (verifier('sondages_editer') || ($sondage['utilisateur_id'] == $_SESSION['id'] && verifier('sondages_editer_siens')))
			{
				//Modification du sondage demandée.
				if (!empty($_POST['nom']))
				{
					$sondage['nom']         = $_POST['nom'];
					$sondage['description'] = $_POST['texte'];
					$sondage['date_debut']  = $_POST['date_debut'];
					$sondage['date_fin']    = !empty($_POST['date_fin']) ? $_POST['date_fin'] : null;
					$sondage['ouvert']      = isset($_POST['ouvert']);
					$sondage->save();

					return redirect(2, 'modifier-'.$sondage['id'].'.html');
				}

				//Montée d'une question demandée.
				if (!empty($_GET['monter']) && is_numeric($_GET['monter']))
				{
					$question = Doctrine_Core::getTable('SondageQuestion')->find($_GET['monter']);
					if ($question['sondage_id'] != $sondage['id'])
						return redirect(21, 'modifier-'.$sondage['id'].'.html', MSG_ERROR);

					$ret = $question->monter();
					return redirect($ret ? 17 : 19, 'modifier-'.$sondage['id'].'.html', $ret ? MSG_OK : MSG_ERROR);
				}

				//Descente d'une question demandée.
				if (!empty($_GET['descendre']) && is_numeric($_GET['descendre']))
				{
					$question = Doctrine_Core::getTable('SondageQuestion')->find($_GET['descendre']);
					if ($question['sondage_id'] != $sondage['id'])
						return redirect(21, 'modifier-'.$sondage['id'].'.html', MSG_ERROR);

					$ret = $question->descendre();
					return redirect($ret ? 18 : 20, 'modifier-'.$sondage['id'].'.html', $ret ? MSG_OK : MSG_ERROR);
				}

				Page::$titre = $sondage['nom'];
				fil_ariane(array(
					'Gestion des sondages' => 'gestion.html',
					htmlspecialchars($sondage['nom']) => 'sondage-'.$sondage['id'].'-'.rewrite($sondage['nom']).'.html',
					'Modifier le sondage',
				));
				
				return render_to_response(array(
					'sondage'   => $sondage,
					'questions' => $sondage->Questions,
				));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(7, 'index.html', MSG_ERROR);
	}
}