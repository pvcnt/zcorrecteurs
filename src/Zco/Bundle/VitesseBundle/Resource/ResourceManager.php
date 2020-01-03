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

namespace Zco\Bundle\VitesseBundle\Resource;

use Zco\Bundle\VitesseBundle\Assetic\Filter\CssRewriteFilter;
use Zco\Bundle\VitesseBundle\Assetic\Filter\JavascriptMinifierFilter;
use Zco\Bundle\VitesseBundle\Assetic\Filter\CssMinFilter;
use Assetic\Asset\AssetCollection;
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Classe retenant les ressources requises sur la page et s'occupant de 
 * leur rendu. S'appuie sur Assetic pour la gestion des ressources.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ResourceManager implements ResourceManagerInterface
{
	private $am;
	private $map;
	private $router;
	private $webDir;
	private $debug;
	private $logger;
	private $writer;
	
	private $feeds = array();
	private $symbols = array();
	private $resolved = array();
	private $needsResolve = true;
	private $rendered = array();
	
	/**
	 * Constructeur.
	 *
	 * @param AssetManager $am
	 * @param ResourceMap $map
	 * @param RouterInterface $router
	 * @param string $webDir
	 * @param boolean $debug
	 * @param LoggerInterface|null $logger
	 */
	public function __construct(
		AssetManager $am, 
		ResourceMap $map, 
		RouterInterface $router, 
		$webDir,
		$debug = false, 
		LoggerInterface $logger = null
	)
	{
		$this->am = $am;
		$this->map = $map;
		$this->router = $router;
		$this->webDir = $webDir;
		$this->debug = $debug;
		$this->logger = $logger;
		$this->writer = new AssetWriter($webDir);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function requireResource($symbol)
	{
		$this->symbols[$symbol] = true;
		$this->needsResolve	 = true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function requireResources(array $symbols)
	{
		foreach ($symbols as $symbol)
		{
			$this->symbols[$symbol] = true;
		}
		$this->needsResolve	 = true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function stylesheets(array $stylesheets = array())
	{
		$filters	= array(new CssMinFilter(), new CssRewriteFilter());
		$collection = new AssetCollection(array(), $filters);
		
		return $this->render($collection, 'css', $stylesheets);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function javascripts(array $javascripts = array())
	{
		$filters	= array(new JavascriptMinifierFilter());
		$collection = new AssetCollection(array(), $filters);
		
		return $this->render($collection, 'js', $javascripts);
	}
	
	public function addFeeds(array $feeds)
	{
		foreach ($feeds as $feed => $options)
		{
			if (is_numeric($feed))
			{
				$feed = $options;
				$options = array();
			}
			
			$this->addFeed($feed, $options);
		}
	}
		
	public function addFeed($feed, array $options = array())
	{
		$options = array_merge(array('type' => 'application/atom+xml', 'title' => 'Flux'), $options);
		$this->feeds[$feed] = $options;
	}
	
	public function renderFeeds()
	{
		$html = '';
		foreach ($this->feeds as $feed => $options)
		{
			$html .= '<link rel="alternate" type="'.$options['type'].'" title="'.$options['title'].'" href="'.$feed.'" />'."\n\t\t";
		}
		
		return $html;
	}
	
	/**
	 * Effectue le rendu d'une collection de ressources.
	 *
	 * @param  AssetCollection $collection Une collection de ressources initialisée
	 * @param  string $type Le type des ressources ('js' ou 'css')
	 * @param  array $assets La liste des ressources qu'on veut inclure
	 * @return array La liste des chemins vers la(es) ressource(s) finale(s)
	 */
	private function render(AssetCollection $collection, $type, array $assets = array())
	{
		$assets = !empty($assets) ? $assets : array_keys($this->symbols);
		foreach ($assets as $i => $asset)
		{
			$assets[$i] = $this->map->getAssetName($asset);
		}
		
		if (empty($assets) && !$this->needsResolve)
		{
			$resolved = $this->resolved;
		}
		else
		{
			$resolved = $this->map->resolveResources($assets);
			if (empty($assets))
			{
				$this->resolved = $resolved;
			}
		}
		
		$this->buildCollection($collection, $resolved, $type);

        if (!is_file($this->webDir.$collection->getTargetPath()))
        {
            $this->writer->writeAsset($collection);
        }
		
		return array($collection->getTargetPath());
	}
	
	/**
	 * Construit une collection en y insérant les ressources à inclure.
	 *
	 * @param  AssetCollection $collection Une collection de ressources initialisée
	 * @param  array $resolved La liste des ressources à inclure (tous types confondus)
	 * @param  string $type Le type des ressources ('js' ou 'css')
	 */
	private function buildCollection(AssetCollection $collection, array $resolved, $type)
	{
		$suffix  = '_'.$type;
		$len	 = -strlen($suffix);
		$symbols = array();
		$hashes  = array();
				
		foreach (array_keys($resolved) as $symbol)
		{
			if (
				substr($symbol, $len) === $suffix
				&& empty($this->rendered[$symbol])
			)
			{
				try
				{
					$asset = $this->am->get($symbol);
				}
				catch (\InvalidArgumentException $e)
				{
					if ($this->logger)
					{
						$this->logger->warn(sprintf('Cannot find resource "%s".', $symbol));
					}
					continue;
				}
				
				$collection->add($asset);
				$this->rendered[$symbol] = true;
				$symbols[] = $symbol;
				$hashes[] = $symbol.':'.$asset->getLastModified();
			}
		}
		
		$collection->setTargetPath('/compiled/'.substr(sha1(implode("\n", $hashes)), 0, 10).'.min.'.$type);
	}
}