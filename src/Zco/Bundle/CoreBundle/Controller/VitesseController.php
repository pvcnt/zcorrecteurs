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

namespace Zco\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Zco\Bundle\CoreBundle\Javelin\Filter\CssRewriteFilter;

class VitesseController extends Controller
{
    public function renderAction($hash)
    {
        $types = array(
            'js' => 'javascript',
            'css' => 'css',
        );
        
        $hash = str_replace('.', '_', $hash);
        $type = substr($hash, strpos($hash, '_') + 1);
        
        if (!isset($types[$type]))
        {
            throw new \InvalidArgumentException(sprintf(
                'Cannot render resource of unknown type "%s".', 
                $type
            ));
        }
        
        $asset = $this->get('zco_core.assetic.asset_manager')->get($hash);
        if ($type === 'css')
        {
            $asset->ensureFilter(new CssRewriteFilter());
        }
        
        $response = new Response($asset->dump());
        $response->headers->set('Content-Type', 'text/'.$types[$type]);
        
        return $response;
    }
}