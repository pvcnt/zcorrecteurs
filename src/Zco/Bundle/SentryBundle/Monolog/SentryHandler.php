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

namespace Zco\Bundle\SentryBundle\Monolog;

use Zco\Bundle\SentryBundle\Sentry\Client;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Gère l'envoi des messages de Monolog vers notre instance de Sentry.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SentryHandler extends AbstractProcessingHandler
{
	protected $client;

	/**
	 * Constructeur.
	 *
	 * @param Client $client Client Sentry
	 * @param integer $level Niveau d'erreur à partir duquel on enregistre
	 * @param boolean $bubble
	 */
	public function __construct(Client $client, $level = Logger::DEBUG, $bubble = true)
	{
		$level = is_int($level) ? $level : constant('Monolog\Logger::'.strtoupper($level));
		$this->client = $client;
		
		parent::__construct($level, $bubble);
	}

	/**
	 * Transmet l'enregistrement Monolog à notre instance de Sentry.
	 *
	 * @param array $record
	 */
	protected function write(array $record)
	{
		$this->client->captureRecord($record);
	}
}