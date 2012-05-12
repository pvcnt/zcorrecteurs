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

namespace Zco\Bundle\FileBundle\Mediawiki;

use Zco\Bundle\FileBundle\Mediawiki\Request\RequestInterface;

class API
{
    private $endPoint;
    
    public function __construct($endPoint)
    {
        $this->endPoint = $endPoint;
    }
    
    public function request(RequestInterface $request)
    {
        $query = $request->getRequest();
        ksort($query);
        $url = $this->endPoint.'?'.http_build_query($query, '', '&').'&format=json';
        
        $curl = curl_init(); 

        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_USERAGENT, 'zCoBot/0.1 (http://www.zcorrecteurs.fr)'); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); 

        $json = curl_exec($curl);
        curl_close($curl);

        return json_decode($json, /* assoc */ true);
    }
}