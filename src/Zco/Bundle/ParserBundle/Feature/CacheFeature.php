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

namespace Zco\Bundle\ParserBundle\Feature;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\CoreBundle\Cache\CacheInterface;
use Zco\Bundle\ParserBundle\ParserEvents;
use Zco\Bundle\ParserBundle\Event\FilterContentEvent;

/**
 * Met en cache un texte parsé. La clé du cache est fonction de l'empreinte 
 * du contenu avant parsage, le cache sera donc automatiquement invalidé lors 
 * d'un changement de contenu.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CacheFeature implements EventSubscriberInterface
{
    private $cache;
    private $debug;
    private $cacheKey;
    
    /**
     * Constructeur.
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache, $debug = false)
    {
        $this->cache = $cache;
        $this->debug = $debug;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ParserEvents::PRE_PROCESS_TEXT  => array('preProcessText', 128),
            ParserEvents::POST_PROCESS_TEXT => array('postProcessText', -128),
        );
    }
        
    /**
     * Vérifie l'existence de ce contenu dans le cache. Si c'est le cas 
     * sort directement de la procédure de parsage.
     *
     * @param FilterContentEvent $event
     */
    public function preProcessText(FilterContentEvent $event)
    {
        $this->cacheKey = 'zco_core:parser:'.sha1($event->getContent());
		if (($text = $this->cache->get($this->cacheKey)) !== false)
		{
		    if ($this->debug)
		    {
		        $text .= '<div style="font-style: italic; font-size: 0.8em; '.
		                    'text-align: right; margin-top: 15px;">'.
		                    'Texte servi depuis le cache.'.
		                    '</div>';
		    }
		    
		    $event->setContent($text);
			$event->stopProcessing();
		}
    }
    
    /**
     * Met en cache le contenu du texte parsé pour une semaine.
	 *
	 * @param FilterContentEvent $event
     */
    public function postProcessText(FilterContentEvent $event)
    {
        $this->cache->set($this->cacheKey, $event->getContent(), 3600 * 24 * 7);
    }
}