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

namespace Zco\Bundle\CoreBundle\Filesystem;

use Zco\Bundle\FileBundle\Util\UrlResolver;

final class LocalUrlResolver implements UrlResolver
{
    private $directory;

    /**
     * Constructor.
     *
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = trim($directory, '/');
    }

    /**
     * {@inheritDoc}
     */
    public function resolveUrl(string $path)
    {
        return sprintf('/%s/%s', $this->directory, $path);
    }
}