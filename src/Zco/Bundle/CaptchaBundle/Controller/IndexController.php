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

namespace Zco\Bundle\CaptchaBundle\Controller;

use Zco\Bundle\CaptchaBundle\Captcha\Captcha;
use Symfony\Component\HttpFoundation\Response;

/**
 * Génération et affichage
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class IndexController
{
	public function defaultAction()
	{
		\Config::load('messages');
		$config = \Config::get('messages');
		$config = $config['captcha'];

		$captcha = new Captcha($config);
		$captcha->afficher();

		$response = new Response();
		$response->headers->set('Content-Type', 'image/png');
		return $response;
	}
}
