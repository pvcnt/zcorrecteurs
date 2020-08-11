<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

namespace Zco\Bundle\FileBundle\Parser\Feature;

use Zco\Bundle\ParserBundle\ParserEvents;
use Zco\Bundle\ParserBundle\Event\FilterDomEvent;
use Gaufrette\Filesystem;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Box;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Étend le parseur pour prendre en compte les références vers des images gérées
 * par ce bundle. Permet notamment le redimensionnement à la volée des images.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FilesFeature implements EventSubscriberInterface
{
	private $filesystem;
	private $imagine;
	
	/**
	 * Constructeur.
	 * 
	 * @param Filesystem $filesystem
	 * @param ImagineInterface $imagine
	 */
	public function __construct(Filesystem $filesystem, ImagineInterface $imagine)
	{
		$this->filesystem = $filesystem;
		$this->imagine	= $imagine;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			ParserEvents::PROCESS_DOM => array('processDom', 100),
		);
	}
		
	/**
	 * Transforme les balises images faisant référence à des images sous forme 
	 * de miniatures pour se ramener à une forme plus conventionnelle qui sera 
	 * ensuite altérée par le parseur principal.
	 *
	 * @return FilterDomEvent $event
	 */
	public function processDom(FilterDomEvent $event)
	{
		//Si on est dans ce mode, on va garder une trace des utilisations 
		//des différents fichiers en les attribuant à l'entité cible.
		$relatedToEntity = ($event->getOption('files.entity_id') && $event->getOption('files.entity_class'));
		
		//On récupère les nœuds image qu'on va analyser. S'il n'y en a aucun, 
		//on peut arrêter le traitement ici !
		$nodes = $event->getDom()->getElementsByTagName('image');
		if ($nodes->length <= 0)
		{
			return;
		}
		
		//On récupère la liste de toutes les images actuellement associées 
		//à cette entité.
		if ($relatedToEntity)
		{
			$part   = (int) $event->getOption('files.part', 0);
			$used   = array('files' => array(), 'thumbnails' => array());
			$unused = array('files' => array(), 'thumbnails' => array());
			
			$rows = \Doctrine_Query::create()
				->select('u.id, u.thumbnail_id, u.file_id')
				->from('FileUsage u')
				->where('entity_class = ?', $event->getOption('files.entity_class'))
				->andWhere('entity_id = ?', $event->getOption('files.entity_id'))
				->andWhere('part = ?', $part)
				->execute(array(), \Doctrine_Core::HYDRATE_SCALAR);
			
			foreach ($rows as $i => $usage)
			{
				if (!$usage['u_thumbnail_id'])
				{
					$used['files'][$usage['u_file_id']]   = $usage['u_id'];
					$unused['files'][$usage['u_file_id']] = $usage['u_id'];
				}
				else
				{
					$used['thumbnails'][$usage['u_thumbnail_id']]   = $usage['u_id'];
					$unused['thumbnails'][$usage['u_thumbnail_id']] = $usage['u_id'];
				}
			}
		}
		
		foreach ($nodes as $node)
		{
			if ($node->hasAttribute('largeur'))
			{
				$width = $node->getAttribute('largeur');
				$node->removeAttribute('largeur');
			}
			else
			{
				$width = null;
			}
			if ($node->hasAttribute('hauteur'))
			{
				$height = $node->getAttribute('hauteur');
				$node->removeAttribute('hauteur');
			}
			else
			{
				$height = null;
			}
			
			if (strpos($node->nodeValue, ':') === false || strpos($node->nodeValue, '://') !== false)
			{
				continue;
			}

			//On trouve le fichier qui correspond à l'url passée.
			list($id, $name) = explode(':', $node->nodeValue);
			$file = \Doctrine_Core::getTable('File')->find($id);
			if (!$file)
			{
				if (!$height && !$width)
				{
					$height = 100;
					$width = 100;
				}
				elseif (!$height)
				{
					$height = $width;
				}
				else
				{
					$width = $height;
				}
				$node->nodeValue = 'http://placehold.it/'.$width.'x'.$height.'&amp;text=%3F';
				$node->setAttribute('legende', 'L\'image utilisateur n\'existe pas ou plus !');
				
				return;
			}
			
			$thumbnail = $this->getThumbnail($file, $width, $height);
			
			//On ajoute une nouvelle utilisation du fichier.
			if ($relatedToEntity && $thumbnail)
			{
				if (!isset($used['thumbnails'][$thumbnail['id']]))
				{
					$usage				    = new \FileUsage();
					$usage['thumbnail_id']	= $thumbnail['id'];
					$usage['file_id']		= $file['id'];
					$usage['entity_class']  = $event->getOption('files.entity_class');
					$usage['entity_id']	 	= $event->getOption('files.entity_id');
					$usage->save();
					
					$used['thumbnails'][$thumbnail['id']] = $usage['id'];
				}
				else
				{
					unset($unused['thumnails'][$thumbnail['id']]);
				}
			}
			elseif ($relatedToEntity && !$thumbnail)
			{
				if (!isset($used['files'][$file['id']]))
				{
					$usage				  	= new \FileUsage();
					$usage['file_id']		= $file['id'];
					$usage['entity_class']  = $event->getOption('files.entity_class');
					$usage['entity_id']	 	= $event->getOption('files.entity_id');
					$usage->save();

					$used['files'][$file['id']] = $usage['id'];
				}
				else
				{
					unset($unused['files'][$file['id']]);
				}
			}
			
			//Et enfin on met à jour l'adresse l'image pour qu'elle pointe 
			//effectivement vers une image.
			$node->nodeValue = $thumbnail ? $thumbnail->getWebPath() : $file->getWebPath();
		}
		
		//On supprime les anciennes utilisations de l'image qui ne sont 
		//plus d'actualité pour l'entité donnée.
		if ($relatedToEntity)
		{
			$diff = array_merge(array_values($unused['files']), array_values($unused['thumbnails']));
			
			if (!empty($diff))
			{
				\Doctrine_Query::create()
					->delete('FileUsage')
					->whereIn('id', $diff)
					->execute();
			}
		}
	}
	
	private function getThumbnail(\File $file, $width = null, $height = null)
	{
		$width  = $width ? min($width, $file['width']) : $file['width'];
		$height = $height ? min($height, $file['height']) : $file['height'];
		
		if ($width == $file['width'] && $height == $file['height'])
		{
			return false;
		}
		
		$ratio  = min($width / $file['width'], $height / $file['height']);
		$size   = new Box($file['width'], $file['height']);
		$size   = $size->scale($ratio);		
		
		$thumbnail = \Doctrine_Core::getTable('FileThumbnail')
			->findOneByFileIdAndWidthAndHeight($file['id'], $size->getWidth(), $size->getHeight());
		
		if (!$thumbnail)
		{
			return $this->createThumbnail($file, $size);
		}
		
		return $thumbnail;
	}
	
	private function createThumbnail(\File $file, Box $size)
	{
		//On crée notre miniature à un emplacement temporaire.
		$path = sys_get_temp_dir().'/'.$file['id'].'-'.$size->getWidth().'x'.$size->getHeight().'.'.$file['extension'];
		$this->imagine
			->load($this->filesystem->read($file->getRelativePath()))
			->copy()
			->resize($size)
			->save($path);
		
		//On crée maintenant l'objet représentant notre miniature.
		$thumbnail = new \FileThumbnail();
		$thumbnail->File	 	= $file;
		$thumbnail['width']   	= $size->getWidth();
		$thumbnail['height']   	= $size->getHeight();
		$thumbnail['size']		= filesize($path);
		$thumbnail['path']		= 'fichiers/min/'.$thumbnail->getSubdirectory().'/'.$thumbnail->getFullname();
		$thumbnail->save();
		
		//On déplace finalement notre miniature à son emplacement final.
		$this->filesystem->write($thumbnail->getRelativePath(), file_get_contents($path));
		unlink($path);
		
		return $thumbnail;
	}
}