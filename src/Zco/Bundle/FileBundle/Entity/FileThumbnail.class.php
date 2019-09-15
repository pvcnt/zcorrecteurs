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

/**
 * FileThumbnail
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FileThumbnail extends BaseFileThumbnail
{
    public function getRelativePath()
    {
        return $this->path;
    }

    /**
     * Retourne le chemin à partir du web pour accéder à l'image.
     *
     * @return string
     */
    public function getWebPath()
    {
        return $this->File->getWebBasePath() . '/' . $this->getRelativePath();
    }

    public function getFullname()
    {
        return $this->File->id . '-' . $this->width . 'x' . $this->height . '.' . $this->File->extension;
    }

    public function getSubdirectory()
    {
        return $this->File->getSubdirectory() . '/' . $this->File->id . '.' . $this->File->extension;
    }
}