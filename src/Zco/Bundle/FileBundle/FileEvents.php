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

namespace Zco\Bundle\FileBundle;

/**
 * Événements générés par le ZcoFileBundle.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class FileEvents
{
	/**
	 * Événement déclenché juste avant l'envoi d'un fichier à travers le module. 
	 * Il permet d'accepter ou rejeter le fichier qui tente de s'envoyer. Par 
	 * défaut un fichier est rejeté s'il n'est accepté par aucun observateur. Il 
	 * est également possible de modifier les options d'envoi.
	 *
	 * L'événement associé est de type Zco\Bundle\FileBundle\Event\FilterUploadEvent.
	 */
	const PRE_UPLOAD  = 'zco_file.pre_upload';
	
	/**
	 * Événement déclenché juste après l'envoi d'un fichier à travers le module. 
	 * À ce stage le fichier est stocké dans le système de fichiers et toutes les 
	 * informations ont été enregistrées dans la base de données.
	 *
	 * L'événement associé est de type Zco\Bundle\FileBundle\Event\UploadEvent.
	 */
	const POST_UPLOAD = 'zco_file.post_upload';
}