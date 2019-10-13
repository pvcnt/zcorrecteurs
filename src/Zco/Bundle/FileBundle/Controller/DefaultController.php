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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur par défaut gérant les actions accessibles depuis l'interface.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
	private $smartFolders;
	private $contentFolders;
	
	/**
	 * Constructeur.
	 */
	public function __construct()
	{
		$this->smartFolders = \Doctrine_Core::getTable('File')->getSmartFolders();
		$this->contentFolders = \Doctrine_Core::getTable('File')->getContentFolders($_SESSION['id']);
	}
	
	/**
	 * Affichage d'un formulaire destiné à recueillir les fichiers que 
	 * l'utilisateur souhaite envoyer vers le module.
	 *
	 * @param Request $request
     * @return Response
	 */
	public function indexAction(Request $request)
	{
		if (!verifier('connecte'))
		{
			throw new AccessDeniedHttpException();
		}
		
		\Zco\Page::$titre = 'Gestionnaire de fichiers';
		$vars = $this->getVariables($request);

		//Données en Mo.
		$usage  = \Doctrine_Core::getTable('File')->getSpaceUsage($_SESSION['id']) / (1000 * 1000);
		$quota  = (int) verifier('fichiers_quota');
		$ratio  = $quota > -1 ? ($quota > 0 ? ceil(100 * $usage / $quota) : 100) : 0;
		
		//Colore la barre en fonction du quota utilisé.
		//< 50 % : OK, >= 50 % et < 80 % : attention, > 80 % : danger
		$usageClass = $ratio > 80 ? 'danger' : ($ratio < 50 ? 'success' : 'warning');
		
		return $this->render(
			'ZcoFileBundle::index.html.php', array_merge(array(
				'currentPage' => 'index',
				'usage'	   => $usage,
				'quota'		  => $quota,
				'ratio'	   => $ratio,
				'usageClass'  => $usageClass,
				'redirectUrl' => $this->generateUrl(
					'zco_file_folder', array(
						'id'	   => \FileTable::FOLDER_LAST_IMPORT, 
						'textarea' => $vars['textarea'], 
						'input'	   => $vars['input']
					)
				),
			), $vars)
		);
	}
	
	/**
	 * Téléverse un ou plusieurs fichiers vers le site. Les fichiers sont 
	 * attendus sous la clé "file".
	 *
	 * @param Request $request
     * @return Response
	 */
	public function uploadAction(Request $request)
	{
		if (!verifier('connecte'))
		{
			throw new AccessDeniedHttpException();
		}
		
		$retval = $this->get('zco_file.uploader')->batchUpload($request, array(
			'user_id'   => $_SESSION['id'],
			'pseudo'	=> $_SESSION['pseudo'],
		));
		
		$_SESSION['fichiers']['last_import'] = array();
		foreach ($retval->success as $item)
		{
			$_SESSION['fichiers']['last_import'][] = $item['id'];
		}
		
		$vars = $this->getVariables($request);

		if (count($retval->failed) > 0)
		{
			$message = array();
			foreach ($retval->failed as $item)
			{
				$message[] = 'Erreur lors de l\'envoi de '.$item['name'].  '('.
					(isset($item['message']) ? $item['message'] : 'erreur inconnue').').';
			}
			
			return redirect(implode("\n", $message), 
				count($retval->failed) >= $retval->total ?
					$this->generateUrl('zco_file_folder', array(
						'id' => \FileTable::FOLDER_LAST_IMPORT,
						'input'    => $vars['input'], 
						'textarea' => $vars['textarea'],
					))
					: $this->generateUrl('zco_file_index', array(
						'input'    => $vars['input'], 
						'textarea' => $vars['textarea'],
					)),
				MSG_ERROR);
		}
		
		return redirect('Tous les fichiers ont été envoyés avec succès.', 
			$this->generateUrl('zco_file_folder', array(
				'id'       => \FileTable::FOLDER_LAST_IMPORT, 
				'input'    => $vars['input'], 
				'textarea' => $vars['textarea'],
			))
		);
    }
	
	/**
	 * Affichage des fichiers contenus dans un dossier.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @param string $entities
     * @return Response
	 */
	public function folderAction(Request $request, $id, $entities)
	{
		if (!verifier('connecte'))
		{
			throw new AccessDeniedHttpException();
		}
		
		//Données en Mo.
		$usage  = \Doctrine_Core::getTable('File')->getSpaceUsage($_SESSION['id']) / (1000 * 1000);
		$quota  = (int) verifier('fichiers_quota');
		$ratio  = $quota > -1 ? ($quota > 0 ? ceil(100 * $usage / $quota) : 100) : 0;
		
		//Colore la barre en fonction du quota utilisé.
		//< 50 % : OK, >= 50 % et < 80 % : attention, > 80 % : danger
		$usageClass = $ratio > 80 ? 'danger' : ($ratio < 50 ? 'success' : 'warning');
		
		$folder = $this->getSmartFolder((int) $id);
		if (!empty($entities))
		{
			$contentFolder = $this->getContentFolder($entities);
		}
		else
		{
			$contentFolder = null;
		}
		\Zco\Page::$titre = $folder['name'];
		
		return $this->render(
			'ZcoFileBundle::folder.html.php', $this->getVariables($request, array(
				'currentPage'   => 'folder',
				'currentFolder' => $folder,
				'currentContentFolder' => $contentFolder,
				'usage'		    => $usage,
				'quota'			=> $quota,
				'ratio'		    => $ratio,
				'usageClass'	=> $usageClass,
			))
		);
	}
	
	/**
	 * Affichage du détail des informations sur un fichier.
	 * 
	 * @param Request $request
     * @param integer $id
     * @return Response
	 */
	public function fileAction(Request $request, $id)
	{
		$file = \Doctrine_Core::getTable('File')->getById($id);
		if (!$file)
		{
			throw new NotFoundHttpException(sprintf('Cannot find file #%s.', $id));
		}
		if (!verifier('connecte') || $file['user_id'] != $_SESSION['id'])
		{
			throw new AccessDeniedHttpException(sprintf('Not allowed to access file #%s.', $id));
		}
		
		$vars = $this->getVariables($request);
		$vars['insertRawFile'] =
			$vars['input'] ? $file->getWebPath() : 
			'<lien url="'.$file->getWebPath().'">'.
				htmlspecialchars($file['name']).'.'.$file['extension'].
			'</lien>';
		if ($file->isImage() && !$vars['input'])
		{
			$vars['insertFullFile']  =
				'<lien url="'.$file->getWebPath().'">'.
					'<image>'.$file['id'].':'.$file->getFullname().'</image>'.
				'</lien>';
			$vars['insertThumbnail'] =
				'<lien url="'.$file->getWebPath().'">'.
					'<image largeur="'.$file->Thumbnail['width'].'">'.
						$file['id'].':'.$file->getFullname().
					'</image>'.
				'</lien>';
		}
		
		\Zco\Page::$titre = sprintf('Propriétés du fichier "%s"', $file['name']);
		$timestamp = time();
		
		return $this->render(
			'ZcoFileBundle::file.html.php', array_merge(array(
				'currentPage'	=> 'file',
				'file'			=> $file,
				'timestamp'	   	=> $timestamp,
			), $vars)
		);
	}
	
	/**
	 * Renvoie les informations sur un dossier intelligent.
	 *
	 * @param  integer $id
	 * @return array
	 */
	private function getSmartFolder($id)
	{
		if (isset($this->smartFolders[$id]))
		{
			return $this->smartFolders[$id];
		}
		
		throw new NotFoundHttpException(sprintf('Cannot find smart folder #%s.', $id));
	}
	
	/**
	 * Renvoie les informations sur un dossier de contenu.
	 *
	 * @param  string $id
	 * @return array
	 */
	private function getContentFolder($id)
	{
		if (isset($this->contentFolders[$id]))
		{
			return $this->contentFolders[$id];
		}
		
		throw new NotFoundHttpException(sprintf('Cannot find content folder "%s".', $id));
	}
	
	/**
	 * Renvoie les variables par défaut nécessaires au layout.
	 *
	 * @param  Request $request
	 * @param  array $variables Variables facultatives à fusionner
	 * @return array
	 */
	private function getVariables(Request $request, array $variables = array())
	{
		return array_merge(array(
			'smartFolders'   => $this->smartFolders,
			'contentFolders' => $this->contentFolders,
			'currentFolder'  => array(),
			'currentContentFolder' => array(),
			'input'		     => $request->query->has('input') ? htmlspecialchars($request->query->get('input')) : null,
			'textarea'	     => $request->query->has('textarea') ? htmlspecialchars($request->query->get('textarea')) : null,
			'xhr'		     => $request->query->has('xhr') && $request->query->get('xhr'),
		), $variables);
	}
}
