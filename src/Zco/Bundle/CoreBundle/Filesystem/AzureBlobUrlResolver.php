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

use Gaufrette\Adapter\AzureBlobStorage\BlobProxyFactoryInterface;

final class AzureBlobUrlResolver implements UrlResolver
{
    private $blobProxyFactory;
    private $containerName;
    private $blobProxy;

    /**
     * Constructor.
     *
     * @param BlobProxyFactoryInterface $blobProxyFactory
     * @param string $containerName
     */
    public function __construct(BlobProxyFactoryInterface $blobProxyFactory, string $containerName)
    {
        $this->blobProxyFactory = $blobProxyFactory;
        $this->containerName = $containerName;
    }

    public function resolve(string $path): string
    {
        $this->init();

        return $this->blobProxy->getBlobUrl($this->containerName, $path);
    }

    /**
     * Lazy initialization, automatically called when some method is called after construction
     */
    protected function init()
    {
        if ($this->blobProxy == null) {
            $this->blobProxy = $this->blobProxyFactory->create();
        }
    }
}