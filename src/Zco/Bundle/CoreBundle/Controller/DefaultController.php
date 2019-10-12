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

namespace Zco\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zco\Bundle\CoreBundle\Sitemap\SitemapFactory;
use Zco\Bundle\CoreBundle\Javelin\Filter\CssRewriteFilter;

class DefaultController extends Controller
{
	public function parseAction(Request $request)
	{
		return new Response($this->get('zco_parser.parser')->parse($request->request->get('texte')));
	}

    /**
     * @Route(path="/robots.txt")
     * @return Response
     */
    public function robotsAction()
    {
        if ('prod' === $this->container->getParameter('kernel.environment')) {
            $content = 'Sitemap: ' . $this->generateUrl('zco_sitemap', [], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $content = 'User-agent: *' . "\n" . 'Disallow: /';
        }

        return new Response($content, 200, ['Content-type' => 'text/plain']);
    }

    /**
     * @Route(path="/health")
     * @return Response
     */
    public function healthAction()
    {
        return new Response('OK', 200, ['Content-type' => 'text/plain']);
    }

    /**
     * @Route(path="/sitemap.xml")
     * @return Response
     */
    public function sitemapAction()
    {
        $cache = $this->get('cache');
        if (($content = $cache->fetch('zco_pages.sitemap')) === false) {
            $factory = new SitemapFactory($this->get('router'));
            $sitemap = $factory->createSitemap();
            $xml = $sitemap->render();
            $xml->formatOutput = true;

            $content = $xml->saveXML();
            $cache->save('zco_pages.sitemap', $content, 3600 * 24);
        }

        $response = new Response($content);
        $response->headers->set('Content-type', 'text/xml');

        return $response;
    }

    public function renderAssetAction($hash)
    {
        $types = array(
            'js' => 'javascript',
            'css' => 'css',
        );

        $hash = str_replace('.', '_', $hash);
        $type = substr($hash, strpos($hash, '_') + 1);

        if (!isset($types[$type])) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot render resource of unknown type "%s".',
                $type
            ));
        }

        $asset = $this->get('zco_core.assetic.asset_manager')->get($hash);
        if ($type === 'css') {
            $asset->ensureFilter(new CssRewriteFilter());
        }

        $response = new Response($asset->dump());
        $response->headers->set('Content-Type', 'text/' . $types[$type]);

        return $response;
    }
}