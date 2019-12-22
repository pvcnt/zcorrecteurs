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

/**
 * Classe contenant des méthodes statiques pour faciliter les uploads de
 * fichiers. Supporte l'upload depuis le disque dur ou bien depuis une URL.
 *
 * @author Vanger
 */
class File_Upload
{
	const FILE = 1;
	const URL = 2;
	const IMAGE = 4;

	/**
	*	Fonction qui permet d'uploader un fichier (sans que celui-ci finisse dans le module d'upload).
	*
	*	@param mixed $fichier				L'array ou le nom du fichier à uploader
	*	@param string $dossierDestination	Le dossier de destination
	*	@param string $nomDestination		Le nom de destination
	*	@param boolean $image               Est-ce que le fichier est une image ?
	*	@return bool
	*/
	public static function Fichier($fichier, $dossierDestination, $nomDestination, $image = false)
	{
		//S'il y a eu une erreur lors de l'upload, on peut arrêter dès maintenant.
		if (empty($fichier['name']) || $fichier['error'] > 0)
		{
			return false;
		}

		//Normalisation du nom du dossier puis création si nécessaire.
		$dossierDestination = rtrim($dossierDestination, '/').'/';
		if (!is_dir($dossierDestination))
		{
			mkdir($dossierDestination, 0777, true);
		}
		
		//Si on envoie une image, on vérifie qu'elle soit d'un type acceptable 
		//(par son extension et son mimetype).
		if ($image)
		{
			$extensions = array('.gif', '.png', '.jpg', '.jpeg');
			$mimetypes = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
			
			$infos = getimagesize($fichier['tmp_name']);
			$ext = strtolower(strrchr($fichier['name'], '.'));
			
			if (!in_array($infos[2], $mimetypes) || !in_array($ext, $extensions))
			{
				return false;
			}
		}

        \Container::getService('gaufrette.uploads_filesystem')->write($dossierDestination . $nomDestination, file_get_contents($fichier['tmp_name']));

		return true;
	}
}
