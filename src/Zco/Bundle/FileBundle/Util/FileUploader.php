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

namespace Zco\Bundle\FileBundle\Util;

use Zco\Bundle\FileBundle\FileEvents;
use Zco\Bundle\FileBundle\Event\FilterUploadEvent;
use Zco\Bundle\FileBundle\Event\UploadEvent;
use Zco\Bundle\FileBundle\Exception\UploadRejectedException;
use Gaufrette\Filesystem;
use Imagine\Image\ImagineInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Classe découplée du reste facilitant l'envoi de fichiers. Elle est utilisée 
 * dans le bundle actuel mais peut être réutilisée ailleurs pour stocker des 
 * fichiers de différentes sources dans le gestionnaire de fichiers.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FileUploader
{
	private $filesystem;
	private $imagine;
	private $dispatcher;
	
	/**
	 * Constructeur.
	 *
	 * @param Filesystem $filesystem Le système de fichiers où stocker les fichiers
	 * @param ImagineInterface $imagine
	 * @param EventDispatcherInterface $dispatcher
	 */
	public function __construct(Filesystem $filesystem, ImagineInterface $imagine, EventDispatcherInterface $dispatcher)
	{
		$this->filesystem 	= $filesystem;
		$this->imagine		= $imagine;
		$this->dispatcher 	= $dispatcher;
	}
	
	public function batchUpload(Request $request, array $options)
	{
		$retval = array('failed' => array(), 'success' =>  array(), 'total' => count($request->files->get('file')));
		foreach ($request->files->get('file') as $uploadedFile)
		{
			//Si le fichier est invalide, il s'agit d'une erreur interne de PHP.
			if (!$uploadedFile->isValid())
			{
				//On tente de déterminer de quelle erreur il s'agit pour faciliter 
				//le rapport des erreurs et le débogage.
				switch ($uploadedFile->getError())
				{
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						$message = 'fichier trop volumineux';
						break;
					case UPLOAD_ERR_PARTIAL:
						$message = 'téléchargement échoué';
						break;
					case UPLOAD_ERR_NO_FILE:
						$message = 'aucun fichier trouvé';
						break;
					case UPLOAD_ERR_NO_TMP_DIR:
					case UPLOAD_ERR_CANT_WRITE:
					case UPLOAD_ERR_EXTENSION:
						$message = 'erreur interne';
						break;
					default:
						$message = null;
				}
				$retval['failed'][] = array(
					'name' => $uploadedFile->getClientOriginalName(), 
					'message' => $message,
				);
				continue;
			}
			
			try 
			{
				$file = $this->upload($uploadedFile, array(
					'user_id'   => $_SESSION['id'],
					'pseudo'	=> $_SESSION['pseudo'],
				));
				
				$retval['success'][] = array('name' => $uploadedFile->getClientOriginalName(), 'id' => $file['id']);
			}
			catch (UploadRejectedException $e)
			{
				$retval['failed'][] = array('name' => $uploadedFile->getClientOriginalName(), 'message' => $e->getMessage());
			}
		}
		
		return $retval;
	}
	
	/**
	 * Enregistre un fichier dans le gestionnaire de fichiers.
	 *
	 * @param  UploadedFile $uploadedFile
	 * @param  array $options
	 * @return \File $file
	 */
	public function upload(UploadedFile $uploadedFile, array $options)
	{
		//L'option spécifiant le créateur du fichier est obligatoire. Elle peut 
		//éventuellement être modifiée lors de la propagation de l'événement mais 
		//on assure à tous les observateurs qu'elle est déjà présente à la base.
		if (!array_key_exists('user_id', $options))
		{
			throw new \InvalidArgumentException('You must specify the "user_id" option.');
		}
		
		//On laisse une chance à tous les modules de valider/rejeter le fichier.
		$event = new FilterUploadEvent($uploadedFile, $options);
		$this->dispatcher->dispatch(FileEvents::PRE_UPLOAD, $event);
		if (!($isAcceptable = $event->isAcceptable()))
		{
			$message = ($isAcceptable === null) ? 'fichier non reconnu' : $event->getErrorMessage();
			throw new UploadRejectedException($message ?: 'fichier rejeté');
		}
		$options = $event->getOptions();
		
		//On crée en premier l'enregistrement représentant le fichier.
		$mime = explode('/', $uploadedFile->getMimeType(), 2);
		
		$file = new \File();
		$file['user_id']	= $options['user_id'];
		$file['name']	   	= substr($uploadedFile->getClientOriginalName(), 0, strrpos($uploadedFile->getClientOriginalName(), '.'));
		$file['extension']  = substr($uploadedFile->getClientOriginalName(), strrpos($uploadedFile->getClientOriginalName(), '.') + 1) ?: 'bin';
		$file['major_mime'] = $mime[0];
		$file['minor_mime'] = $mime[1];
		$file['size']	   	= $uploadedFile->getSize();
		$file['type']	   	= isset($options['type']) ? (int) $options['type'] : 0;
		
		//Pour spécifier une licence on doit spécifier le pseudo (conservé comme 
		//trace inaltérable en cas de changement de pseudo ou suppression de compte).
		if (isset($options['license_id']))
		{
			if (empty($options['pseudo']))
			{
				throw new \InvalidArgumentException('You must specify the "pseudo" when specifying a "license_id".');
			}
			$file['license_id'] = $options['license_id'];
		}
		
		//Une option permet de configurer si le fichier est décompté du quota de l'utilisateur.
		if (isset($options['quota_affected']))
		{
			$file['quota_affected'] = (boolean) $options['quota_affected'];
		}
		
		//Si le fichier est une image on remplit les paramètres spécifiques.
		if ($file->isImage())
		{
			$image 			= $this->imagine->open($uploadedFile->getPathname());
			$size  			= $image->getSize();
			$file['width']  = $size->getWidth();
			$file['height'] = $size->getHeight();
		}
		$file->save();
		
		//On peut maintenir définir le chemin vers le fichier.
		$file['path'] = 'fichiers/'.$file->getSubDirectory().'/'.$file['id'].'.'.$file['extension'];
		
		//Si le fichier est une image, on lui crée une première miniature. Celle-ci 
		//sera utilisée dans les listes de fichiers, elle est donc systématiquement 
		//créée après l'envoi du fichier.
		if ($file->isImage())
		{
			$thumbnail 	= $image->thumbnail(new \Imagine\Image\Box(150, 80));
			$size		= $thumbnail->getSize();
			$path 		= sys_get_temp_dir().'/'.$file['id'].'-'.$size->getWidth().'x'.$size->getHeight().'.'.$file['extension'];
			
			$thumbnail->save($path);
			$thumbnail = null;
			
			$thumbnail = new \FileThumbnail();
			$thumbnail->File	 = $file;
			$thumbnail['width']  = $size->getWidth();
			$thumbnail['height'] = $size->getHeight();
			$thumbnail['size']   = filesize($path);
			$thumbnail['path']   = 'fichiers/min/'.$file->getSubdirectory().'/'.$file['id'].'.'.$file['extension'].'/'.$file['id'].'-'.$size->getWidth().'x'.$size->getHeight().'.'.$file['extension'];
			$thumbnail->save();
			
			//On associe l'image principale en retour au fichier.
			$file['thumbnail_id'] = $thumbnail['id'];
						
			//On écrit cette miniature sur le système de fichiers.
			$this->filesystem->write($thumbnail->getRelativePath(), file_get_contents($path));
			unlink($path);
		}
		
		//Et on sauvegarde à nouveau le fichier !
		$file->save();
		
		//Si le fichier a été publié sous une certaine licence on l'enregistre 
		//afin de garder un historique.
		if ($file->hasLicense())
		{
			$license = new \FileLicense();
			$license['file_id']		= $file['id'];
			$license['license_id']  = $file['license_id'];
			$license['pseudo']	    = $options['pseudo'];
			$license->save();

			$file->License = $license;
		}
		
		//Et on écrit le fichier original sur le système de fichiers.
		$this->filesystem->write($file->getRelativePath(), file_get_contents($uploadedFile->getPathname()));
		unlink($uploadedFile->getPathname());
		
		//On termine par notifier tout le monde de la création d'un nouveau fichier.
		$event = new UploadEvent($file);
		$this->dispatcher->dispatch(FileEvents::POST_UPLOAD, $event);
		
		return $file;
	}
}