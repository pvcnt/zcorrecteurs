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

namespace Zco\Bundle\CoreBundle\Sitemap;

final class SitemapLink
{
    private $url;
    private $options;

    /**
     * Constructor.
     *
     * @param string $url
     * @param array $options
     */
    public function __construct(string $url, array $options)
    {
        $this->url = $url;
        $this->options = $options;
    }

    /**
     * Render this link into the sitemap XML document.
     *
     * @param \DomDocument $doc XML document.
     * @param \DOMNode $parent Parent node where to write this link.
     */
    public function render(\DomDocument $doc, \DOMNode $parent): void
    {
        $url = $parent->appendChild($doc->createElement('url'));

        $loc = $url->appendChild($doc->createElement('loc'));
        $loc->appendChild($doc->createTextNode($this->url));

        if (isset($this->options['changefreq'])) {
            $changefreq = $url->appendChild($doc->createElement('changefreq'));
            $changefreq->appendChild($doc->createTextNode($this->options['changefreq']));
        }
        if (isset($this->options['priority'])) {
            $priority = $url->appendChild($doc->createElement('priority'));
            $priority->appendChild($doc->createTextNode($this->options['priority']));
        }
        if (isset($this->options['lastmod'])) {
            $lastmod = $url->appendChild($doc->createElement('lastmod'));
            $lastmod->appendChild($doc->createTextNode($this->options['lastmod']));
        }
    }
}