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

namespace Zco\Bundle\TwitterBundle\Service;

/**
 * Interactions avec l'API de Twitter.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
*/
class Twitter extends OAuth
{
	private $apiEndpoint;
	
	public function __construct($apiEndpoint, $oauthEndpoint, array $consumerKey)
	{
		$this->apiEndpoint = rtrim($apiEndpoint, '/');
		parent::__construct($oauthEndpoint, $consumerKey);
	}
	
	public function addTweet($status, $rid = null)
	{
		$params = compact('status');
		if ($rid !== null)
		{
			$params['in_reply_to_status_id'] = $rid;
		}

		return $this->send(
			'POST',
			$this->apiEndpoint.'/statuses/update.json',
			$params
		);
	}

	public function deleteTweet($id)
	{
		return $this->send(
			'POST',
			$this->apiEndpoint.'/statuses/destroy.json',
			compact('id')
		);
	}

	public function getMentions($lastID = 0)
	{
		$lastID = $lastID
			? array('since_id' => $lastID)
			: array();

		return $this->send(
			'GET',
			$this->apiEndpoint.'/statuses/mentions.json',
			$lastID
		);
	}
}

