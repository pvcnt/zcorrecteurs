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

namespace Zco\Bundle\FileBundle\EventListener;

use Zco\Bundle\FileBundle\FileEvents;
use Zco\Bundle\FileBundle\Event\FilterUploadEvent;
use Zco\Bundle\FileBundle\Exception\UploadRejectedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Observateur par défaut appliquant des règles de sécurité élémentaires 
 * sur les fichiers désirant être envoyés.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
	private static $allowedMimeTypes = array(
		//.ogg
		'audio/ogg',
		'application/ogg',
		'audio/x-ogg', 
		'application/x-ogg',
		
		//.mp3
		'audio/mpeg', 
		'audio/x-mpeg', 
		'audio/mp3', 
		'audio/x-mp3', 
		'audio/mpeg3', 
		'audio/x-mpeg3', 
		'audio/mpg', 
		'audio/x-mpg', 
		'audio/x-mpegaudio',
		
		//.pdf
		'application/pdf', 
		'application/x-pdf', 
		'application/acrobat', 
		'applications/vnd.pdf', 
		'text/pdf', 
		'text/x-pdf',
		
		//.txt
		'text/plain', 
		'application/txt', 
		'text/anytext', 
		'widetext/plain', 
		'widetext/paragraph',
		
		//.rtf
		'application/rtf', 
		'application/x-rtf', 
		'text/rtf', 
		'text/richtext', 
		
		//.png
		'image/png', 
		'application/png', 
		'application/x-png',
		
		//.jpeg
		'image/pjg',
		'image/jpeg',
		
		//.gif
		'image/gif',
	);
	
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			FileEvents::PRE_UPLOAD => 'onPreUpload',
		);
	}
	
	/**
	 * Effectue des vérifications élémentaires sur le fichier en attente d'envoi, 
	 * c'est-à-dire le non-dépassement du quota et la vérification du type MIME.
	 *
	 * @param FilterUploadEvent $event
	 */
	public function onPreUpload(FilterUploadEvent $event)
	{
		//Vérification des quotas (données en octets).
		$usage = \Doctrine_Core::getTable('File')->getSpaceUsage($event->getOption('user_id'));
		$quota  = verifier('fichiers_quota') * 1000 * 1000; //TODO: prendre en compte le vrai groupe
		if ($quota > -1  && $usage + $event->getFile()->getSize() > $quota)
		{
			$event->reject('dépassement de quota');
			$event->stopPropagation();
		}
		
		//Vérification des types MIME.
		if (in_array($event->getFile()->getMimeType(), self::$allowedMimeTypes))
		{
			$event->validate();
		}
	}
}