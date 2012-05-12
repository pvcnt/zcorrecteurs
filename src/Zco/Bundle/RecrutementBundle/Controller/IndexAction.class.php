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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant l'affichage de l'accueil de l'espace recrutement.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class IndexAction extends Controller
{
	public function execute()
	{
		Page::$titre = 'Faites vivre la langue française !';
		fil_ariane();
		
		return render_to_response(array(
			'equipe' => \Doctrine_Core::getTable('Utilisateur')->listerAvatarsEquipe(),
		));
	}
}
