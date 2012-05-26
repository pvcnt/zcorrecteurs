<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

namespace Zco\Bundle\CoreBundle;

use Symfony\Bundle\FrameworkBundle\Client as BaseClient;

/**
 * Client compatible avec le fonctionnement actuel du site. Procède à 
 * l'extraction des variables superglobales ainsi qu'à l'émulation du 
 * comportement des règles de rewriting Apache.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Client extends BaseClient
{
    /**
     * {@inheritdoc}
     */
    protected function doRequest($request)
    {
        $uri = $request->server->get('REQUEST_URI');
        $param = function(array $matches, $index)
        {
            return isset($matches[$index]) ? $matches[$index] : '';
        };
        
        if ($uri === '/')
        {
            $request->query->add(array('page' => 'accueil'));
        }
        elseif (preg_match('@^/([a-zA-Z]+)/(index\.html)?$@', $uri, $matches))
        {
            $request->query->add(array(
                'page' => $param($matches, 1),
                'index' => $param($matches, 2),
            ));
        }
        elseif (preg_match('@^/([a-zA-Z-]+)/([a-zA-Z-]+)(?:-([0-9]+))?(?:-([0-9]+))?(?:-p([0-9]+))?(?:-([a-zA-Z0-9-]*))?\.html$@', $uri, $matches))
        {
            $request->query->add(array(
                'page' => $param($matches, 1),
                'act' => $param($matches, 2),
                'id' => $param($matches, 3),
                'id2' => $param($matches, 4),
                'p' => $param($matches, 5),
                'titre' => $param($matches, 6),
            ));
        }

		$request->overrideGlobals();
        
        return parent::doRequest($request);
    }
}