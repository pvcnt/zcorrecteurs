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
 * File
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class File extends BaseFile
{
    const TYPE_MANUAL = 0;
    const TYPE_AUTO = 1;
    //const TYPE_COMMONS = 2;
    const TYPE_IMPORTED = 3;
    
    private static $filesPerFolder = 1000;
    
    private static $icons = array(
        //Types précis
        'application/pdf' => 'pdf.png',
        'application/zip' => 'archive.png',
        'application/x-tar' => 'archive.png',
        'application/x-gzip' => 'archive.png',
        'application/x-tex' => 'tex.png',
        'text/plain' => 'text.png',
        'text/rtf' => 'wordprocessing.png',
        'application/msword' => 'wordprocessing.png',
        'application/vnd.oasis.opendocument.text' => 'wordprocessing.png',
        'application/vnd.oasis.opendocument.spreadsheet' => 'spreadsheet.png',
        'application/vnd.ms-excel' => 'spreadsheet.png',
        'application/vnd.ms-powerpoint' => 'presentation.png',
        'application/vnd.oasis.opendocument.presentation' => 'presentation.png',
        
        //Types partiels
        'video/' => 'video.png',
        'audio/' => 'audio.png',
        'image/' => 'image.png',
    );
    
    /**
     * Détermine si le fichier est une image.
     *
     * @return boolean
     */
    public function isImage()
    {
        return $this->major_mime === 'image';
    }
    
    /**
     * Détermine si le fichier est un fichier audio.
     *
     * @return boolean
     */
    public function isAudio()
    {
        return $this->major_mime === 'audio';
    }
    
    public function getMimetype()
    {
        return $this->major_mime.'/'.$this->minor_mime;
    }
    
    public function getFullname()
    {
        return $this->name.'.'.$this->extension;
    }
    
    /**
     * Détermine si le fichier a été publié sous une certaine licence.
     * Sinon on considère qu'il est en « tous droits réservés ».
     *
     * @return boolean
     */
    public function hasLicense()
    {
        return (boolean) $this->license_id;
    }
    
    /**
     * Retourne le chemin relatif du fichier par rapport à la racine 
     * du système de fichiers associé et la racine du dossier web.
     *
     * @return string
     */
    public function getRelativePath()
    {
        return $this->path;
    }
    
    /**
     * Retourne le chemin à partir du web pour accéder au fichier en 
     * version originale.
     *
     * @return string
     */
    public function getWebPath()
    {
        return $this->getWebBasePath().'/'.$this->getRelativePath();
    }
    
    /**
     * Retourne le chemin à partir du web pour accéder à la miniature 
     * « principale » du fichier (150x80 maximum).
     *
     * @return string
     */
    public function getImageWebPath()
    {
        if ($this->isImage())
        {
            return $this->Thumbnail->getWebPath();
        }
        
        foreach (self::$icons as $mime => $icon)
        {
            if (strpos($this->getMimetype(), $mime) === 0)
            {
                return '/bundles/zcofile/img/mimetypes/'.$icon;
            }
        }
        
        return '/bundles/zcofile/img/placeholder.png';
    }
    
    /**
     * Retourne le dossier de base pour accéder au fichier depuis le web.
     *
     * @return string
     */
    public function getWebBasePath()
    {
        return '/uploads';
    }
    
    /**
     * Retourne le nom du sous-dossier associé au fichier. L'identifiant du 
     * fichier doit déjà avoir été créé.
     *
     * @return string
     */
    public function getSubDirectory()
    {
        if (!$this->id)
        {
            throw new \RuntimeException(
				'Vous devez fixer l\'identifiant du fichier avant le calcul '
				.'de son sous-dossier de destination.'
			);
        }
        
        $start = $this->id - ($this->id % self::$filesPerFolder);
        
        return $start.'_'.($start + self::$filesPerFolder);
    }
}