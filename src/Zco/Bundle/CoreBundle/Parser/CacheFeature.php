<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

namespace Zco\Bundle\CoreBundle\Parser;

use Doctrine\Common\Cache\Cache;

/**
 * Met en cache un texte parsé. La clé du cache est fonction de l'empreinte
 * du contenu avant parsage, le cache sera donc automatiquement invalidé lors
 * d'un changement de contenu.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CacheFeature extends AbstractFeature
{
    private $cache;
    private $cacheKey;

    /**
     * Constructeur.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Vérifie l'existence de ce contenu dans le cache. Si c'est le cas
     * sort directement de la procédure de parsage.
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public function preProcessText(string $content, array $options): string
    {
        // Prendre en compte dans la clé de cache les options. Exemple typique : on
        // peut demander ou non à afficher des ancres à côté des titres.
        $this->cacheKey = 'zco_core:parser:' . sha1($content) . '_' . sha1(serialize($options));
        if (($text = $this->cache->fetch($this->cacheKey)) !== false) {
            return $text;
        }

        return $content;
    }

    /**
     * Met en cache le contenu du texte parsé pour une semaine.
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    public function postProcessText(string $content, array $options): string
    {
        if (null !== $this->cacheKey) {
            $this->cache->save($this->cacheKey, $content, 3600);
        }

        return $content;
    }
}