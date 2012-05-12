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

namespace Zco\Bundle\InformationsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant l'affichage de certaines pages d'informations statiques.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class StaticController extends Controller
{
	public function mentionsAction()
	{
		\Page::$titre = 'Mentions légales';
		
		return render_to_response('ZcoInformationsBundle:Static:mentions.html.php');
	}
	
	public function privacyAction()
	{
		\Page::$titre = 'Politique de confidentialité';
		
		return render_to_response('ZcoInformationsBundle:Static:privacy.html.php');
	}
	
	public function rulesAction()
	{
		\Page::$titre = 'Règlement';

		return render_to_response('ZcoInformationsBundle:Static:rules.html.php');
	}
}