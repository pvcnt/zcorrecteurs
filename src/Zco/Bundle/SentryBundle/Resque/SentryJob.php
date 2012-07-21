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

namespace Zco\Bundle\SentryBundle\Resque;

require_once __DIR__.'/../Sentry/Connection.php';

/**
 * Worker ayant pour utilité de vider la vider la queue Resque des données en 
 * attente d'envoi vers Sentry.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SentryJob
{
	/**
	 * Effectue la tâche.
	 */
	public function perform()
	{
		$connection = new Connection($this->args['public_key'], $this->args['secret_key']);
		$connection->send($this->args['server'], unserialize($this->args['data']));
	}
}