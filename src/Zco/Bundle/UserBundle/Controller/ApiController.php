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

namespace Zco\Bundle\UserBundle\Controller;

use Zco\Bundle\UserBundle\Exception\ValueException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Fonctions d'API internes.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ApiController extends Controller
{
	/**
	 * Recherche un nom d'utilisateur commençant par une chaîne de caractères 
	 * donnée.
	 *
	 * @param  Request $request
	 * @return Response
	 */
	public function searchUsernameAction(Request $request)
	{
		if (!$request->request->has('pseudo'))
		{
			return new Response('ERREUR');
		}
		
		$users = \Doctrine_Core::getTable('Utilisateur')->query(array(
			'pseudo'        => $request->request->get('pseudo'),
			'#pseudo_like' => \UtilisateurTable::LIKE_BEGIN,
		), \Doctrine_Core::HYDRATE_ARRAY);
		$retval = array();
		
		foreach ($users as $user)
		{
			$retval[] = htmlspecialchars($user['pseudo']);
		}

		$response = new Response;
		$response->headers->set('Content-type', 'application/json');
		$response->setContent(json_encode($retval));
		
		return $response;
	}

	/**
	 * Vérifie la validité d'un nom d'utilisateur donné.
	 *
	 * @param  Request $request
	 * @return Response
	 */
	public function validateUsernameAction(Request $request)
	{
		if (!$request->request->has('pseudo'))
		{
			$retval = array('status' => 'ERROR');
		}
		else
		{
			try
			{
				$this->get('zco_user.user')->validateUserName($request->request->get('pseudo'));
				$retval = array('result' => 'OK', 'message' => 'Le pseudo demandé est disponible.');
			}
			catch (ValueException $e)
			{
				$retval = array('result' => 'ERROR', 'message' => ($e->getMessage() ?: 'Le pseudo demandé est invalide.'));
			}
			$retval['status'] = 'OK';
		}
		
		return new Response(json_encode($retval));
	}
}