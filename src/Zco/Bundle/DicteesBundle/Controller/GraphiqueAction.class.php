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
 * Graphiques de la progression d'un membre sur les dictées.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */

include(dirname(__FILE__).'/../modeles/statistiques.php');

class GraphiqueAction extends DicteesActions
{
	public function execute()
	{
		$d = null;
		if($_GET['id'] == GRAPHIQUE_FREQUENCE)
			$d = GraphiqueFrequenceNotes();
		elseif($_GET['id'] == GRAPHIQUE_EVOLUTION)
			$d = GraphiqueEvolutionNotes($_GET['id2']);
		else
			return new Symfony\Component\HttpFoundation\RedirectResponse('statistiques.html');

		$Response = new Symfony\Component\HttpFoundation\Response($d);
		$Response->headers->set('Content-Type', 'image/png');
		return $Response;
	}
}
