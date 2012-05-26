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

namespace Zco\Bundle\TwitterBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Intégration des comptes Twitter au site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ApiController extends Controller
{
	/**
	 * Raccourcit une URL via le service bit.ly.
	 *
	 * @param Request $request
	 */
	public function bitlyAction(Request $request)
	{
		if (!$request->request->has('url'))
		{
			return new Response(json_encode(array('status' => 'ERROR')));
		}
		
		return new Response(json_encode(array(
			'status' => 'OK', 
			'url' => $this->get('zco_twitter.bitly')->shorten($request->request->get('url')),
		)));
	}
}
