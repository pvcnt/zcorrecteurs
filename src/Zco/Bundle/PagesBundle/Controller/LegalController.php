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

namespace Zco\Bundle\PagesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant l'affichage des pages d'informations légales.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class LegalController extends Controller
{
	public function mentionsAction()
	{
		\Page::$titre = 'Mentions légales';
		
		return render_to_response('ZcoPagesBundle:Legal:mentions.html.php');
	}
	
	public function privacyAction()
	{
		\Page::$titre = 'Politique de confidentialité';
		
		return render_to_response('ZcoPagesBundle:Legal:privacy.html.php');
	}
	
	public function rulesAction()
	{
		\Page::$titre = 'Règlement';

		return render_to_response('ZcoPagesBundle:Legal:rules.html.php');
	}
}