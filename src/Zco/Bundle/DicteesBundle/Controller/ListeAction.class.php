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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Liste des dictées.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class ListeAction extends DicteesActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, false, false, 1);

		Page::$titre = 'Liste des dictées';
		fil_ariane('Liste des dictées');

		$_GET['p'] = (int)$_GET['p'] > 0 ? (int)$_GET['p'] : 1;
		if($_GET['p'] > 1)
			Page::$titre .= ' - Page '.(int)$_GET['p'];
		$tri = isset($_GET['tri']) ? $_GET['tri'] : '-creation';

		$paginator = ListerDictees($_GET['p'], $tri);
		try
		{
		    $pager = $paginator->createView($_GET['p']);
		    $pager->setUri('liste-p%d.html'.($tri ? '?tri='.$tri : ''));
		}
		catch (\InvalidArgumentException $e)
		{
		    throw new NotFoundHttpException('La page demandée n\'existe pas.');
		}

		return render_to_response(compact('pager', 'tri'));
	}
}
