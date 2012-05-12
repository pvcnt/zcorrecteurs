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

namespace Zco\Bundle\VitesseBundle\Assetic\Filter;

use Assetic\Filter\BaseCssFilter;
use Assetic\Asset\AssetInterface;

class CssRewriteFilter extends BaseCssFilter
{
	public function filterLoad(AssetInterface $asset)
	{
	}

	public function filterDump(AssetInterface $asset)
	{
		$root = $asset->getSourceRoot();
		if (null === $root)
		{
			return;
		}
	  
		$root = preg_replace('@'.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'[^'.DIRECTORY_SEPARATOR.']+$@', '', $root);
		$parts = explode('/', $root);
		$len = count($parts);
		$bundle = preg_replace('@Bundle$@', '', $parts[$len - 1]);
		$bundle = strtolower(($parts[$len - 2] === 'Bundle' ? $parts[$len - 3] : $parts[$len - 2]).$bundle);
		
		$content = $this->filterReferences($asset->getContent(), function($matches) use ($bundle)
		{
			if (false !== strpos($matches['url'], '://') || 0 === strpos($matches['url'], '//'))
			{
				return $matches[0];
			}
			if (false === strpos($matches['url'], '../'))
			{
				return $matches[0];
			}
			
			$url = str_replace('../', '/bundles/'.$bundle.'/', $matches['url']);

			return str_replace($matches['url'], $url, $matches[0]);
		});

		$asset->setContent($content);
	}
}
