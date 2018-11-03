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

namespace Zco\Bundle\PagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zco\Bundle\PagesBundle\Event\FilterSitemapEvent;
use Zco\Bundle\PagesBundle\PagesEvents;

/**
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
    public function robotsAction()
    {
        if ('prod' === $this->container->getParameter('kernel.environment')) {
            $content = 'Sitemap: ' . $this->generateUrl('zco_sitemap', [], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $content = 'User-agent: *' . "\n" . 'Disallow: /';
        }

        return new Response($content, 200, ['Content-type' => 'text/plain']);
    }

    public function healthAction()
    {
        return new Response('OK', 200, ['Content-type' => 'text/plain']);
    }

    public function sitemapAction()
    {
        $cache = $this->get('cache');
        if (($content = $cache->fetch('zco_pages.sitemap')) === false) {
            $xml = new \DomDocument();
            $xml->formatOutput = true;

            $urlset = $xml->createElement('urlset');
            $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $urlset->setAttribute(
                'xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9
				http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
            $urlset = $xml->appendChild($urlset);

            $dispatcher = $this->container->get('event_dispatcher');
            $event = new FilterSitemapEvent();
            $dispatcher->dispatch(PagesEvents::SITEMAP, $event);

            foreach ($event->getLinks() as $link => $options) {
                $url = $xml->createElement('url');
                $url = $urlset->appendChild($url);

                $loc = $xml->createElement('loc');
                $loc = $url->appendChild($loc);
                $loc->appendChild($xml->createTextNode($link));

                $changefreq = $xml->createElement('changefreq');
                $changefreq = $url->appendChild($changefreq);
                $changefreq->appendChild($xml->createTextNode($options['changefreq']));

                $priority = $xml->createElement('priority');
                $priority = $url->appendChild($priority);
                $priority->appendChild($xml->createTextNode($options['priority']));
            }

            $content = $xml->saveXML();
            $cache->save('zco_pages.sitemap', $content, 3600 * 24);
        }

        $response = new Response($content);
        $response->headers->set('Content-type', 'text/xml');

        return $response;
    }
}
