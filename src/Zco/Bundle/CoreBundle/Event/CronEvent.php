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

namespace Zco\Bundle\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Événement notifiant de l'exécution d'un cron.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CronEvent extends Event
{
	protected $output;
	protected $lastRun;
	
	/**
	 * Constructeur.
	 *
	 * @param OutputInterface $output Sortie console
	 * @param string $lastRun Date de dernier lancement du cron
	 */
	public function __construct(OutputInterface $output, $lastRun)
	{
		$this->output  = $output;
		$this->lastRun = $lastRun;
	}

	/**
	 * Retourne la sortie console.
	 *
	 * @return OutputInterface
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * Vérifie que le cron n'a pas été lancé lors de la dernière journée.
	 *
	 * @return boolean Vrai si le cron n'a pas été lancé dans les dernières 
	 *                 24 heures, faux sinon.
	 */
	public function ensureDaily()
	{
		//Cas trivial si le cron n'a jamais été lancé.
		if (!$this->lastRun)
		{
			return true;
		}

		//On laisse 30 minutes de tolérance en cas de décalage dans les crons.
		//Concrètement, on vérifie que le cron n'ait pas été lancé dans les 
		//23 heures et 30 minutes qui précèdent l'heure actuelle.

		return strtotime($this->lastRun) + 3600 * 23.5 <= time();
	}

	/**
	 * Vérifie que le cron n'a pas été lancé lors de la dernière heure.
	 *
	 * @return boolean Vrai si le cron n'a pas été lancé dans les dernières 
	 *                 60 minutes, faux sinon.
	 */
	public function ensureHourly()
	{
		//Cas trivial si le cron n'a jamais été lancé.
		if (!$this->lastRun)
		{
			return true;
		}

		//On laisse 5 minutes de tolérance en cas de décalage dans les crons.
		//Concrètement, on vérifie que le cron n'ait pas été lancé dans les 
		//55 minutes qui précèdent l'heure actuelle.

		return strtotime($this->lastRun) + 3300 <= time();
	}

	/**
	 * Retourne la date du dernier lancement du cron.
	 *
	 * @return string|boolean Date (au format Y-m-d H:i:s) ou faux si le cron 
	 *                        n'a jamais été lancé.
	 */
	public function getLastRun()
	{
		return $this->lastRun;
	}
}