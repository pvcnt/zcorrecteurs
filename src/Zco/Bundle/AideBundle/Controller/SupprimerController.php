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

namespace Zco\Bundle\AideBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Confirmation de la suppression d'un sujet d'aide.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SupprimerController
{
	public function defaultAction()
	{
        if (!verifier('aide_supprimer')) {
            throw new AccessDeniedHttpException();
        }

		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$page = \Doctrine_Core::getTable('Aide')->find($_GET['id']);
			if (!$page)
			{
				throw new NotFoundHttpException();
			}
			\Page::$titre = htmlspecialchars($page['titre']);

			if (isset($_POST['confirmer']))
			{
				$page->delete();
				return redirect('La page a bien été supprimée.', 'index.html');
			}
			if (isset($_POST['annuler']))
			{
				return new RedirectResponse('page-'.$page['id'].'-'.rewrite($page['titre']).'.html');
			}
			
			return render_to_response(array('page' => $page));
		}
		else
		{
            throw new NotFoundHttpException();
		}
	}
}