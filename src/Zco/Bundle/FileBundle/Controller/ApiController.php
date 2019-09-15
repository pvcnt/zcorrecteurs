<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

namespace Zco\Bundle\FileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant toutes les actions liées à l'API du bundle. Ces actions 
 * sont normalement appelées lors d'opérations asynchrones.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ApiController extends Controller
{
	/**
	 * Récupère la liste des fichiers correspondant à une recherche donnée. Les 
	 * fichiers sont filtrés par dossier ainsi que selon une chaîne donnée 
	 * (optionnelle).
	 *
	 * @return Response Réponse JSON contenant la liste des fichiers retrouvés
	 *				  (les données sont formatées et prêtes à l'affichage)
	 */
	public function searchAction($folder, $entities)
	{
		if (!verifier('connecte'))
		{
			throw new AccessDeniedHttpException();
		}
		
		$search = !empty($_POST['search']) ? trim($_POST['search']) : '';
		$files = \Doctrine_Core::getTable('File')
			->getByFolderAndSearch((int) $folder, $_SESSION['id'], $search, $entities);
		
		$response = array();
		foreach ($files as $i => $file)
		{
			$response[] = array(
				'id'			 => (int) $file['id'],
				'name'		   	 => htmlspecialchars($file['name']),
				'size'		  	 => sizeformat($file['size']),
				'date'		   	 => dateformat($file['date']),
				'thumbnail_path' => htmlspecialchars($file->getImageWebPath()),
				'path'		     => htmlspecialchars($file->getWebPath()),
			);
		}
		
		return new Response(json_encode($response));
	}
	
	/**
	 * Modifie le nom et la license d'un fichier donné.
	 *
	 * @param  integer $id L'identifiant du fichier
	 * @return Response Réponse JSON
	 */
	public function editAction($id)
	{
		if (!verifier('connecte'))
		{
			throw new AccessDeniedHttpException();
		}
		
		$file = \Doctrine_Core::getTable('File')->find($id);
		if (!$file)
		{
			throw new NotFoundHttpException(sprintf('Cannot find file #%s.', $id));
		}
		if ($file['user_id'] != $_SESSION['id'])
		{
			throw new AccessDeniedHttpException(sprintf('Not allowed to access file #%s.', $id));
		}
		
		if (!empty($_POST['name']))
		{
			$file['name'] = trim($_POST['name']);
		}
		
		$file->save();
		
		return new Response(json_encode(array('status' => 'OK')));
	}
	
	public function usageAction()
	{
		if (!verifier('connecte'))
		{
			throw new AccessDeniedHttpException();
		}
		
		$usage  = \Doctrine_Core::getTable('File')->getSpaceUsage($_SESSION['id']) / (1000 * 1000);
		$quota  = (int) verifier('fichiers_quota');
		$ratio  = $quota > -1 ? ($quota > 0 ? ceil(100 * $usage / $quota) : 100) : 0;
		
		//Colore la barre en fonction du quota utilisé.
		//< 50 % : OK, >= 50 % et < 80 % : attention, > 80 % : danger
		$usageClass = $ratio > 80 ? 'danger' : ($ratio < 50 ? 'success' : 'warning');
		
		return new Response(json_encode(array(
			'status'	 => 'OK',
			'usage'	  => $usage,
			'quota'	  => $quota,
			'ratio'	  => $ratio,
			'usageClass' => $usageClass,
		)));
	}
	
	/**
	 * Supprime un fichier donné.
	 *
	 * @param  integer $id L'identifiant du fichier
	 * @return Response Réponse JSON
	 */
	public function deleteAction($id)
	{
		if (!verifier('connecte'))
		{
			throw new AccessDeniedHttpException();
		}
		
		$file = \Doctrine_Core::getTable('File')->find($id);
		if (!$file)
		{
			throw new NotFoundHttpException(sprintf('Cannot find file #%s.', $id));
		}
		if ($file['user_id'] != $_SESSION['id'])
		{
			throw new AccessDeniedHttpException(sprintf('Not allowed to access file #%s.', $id));
		}
		
		$file->delete();
		
		return new Response(json_encode(array('status' => 'OK')));
	}
}
