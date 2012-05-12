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

namespace Zco\Bundle\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Mise à jour des sessions des visiteurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CronCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('cron:sessions')
			->setDescription('Update sessions');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
	    $dbh = \Doctrine_Manager::connection()->getDbh();
	    
		$stmt = $dbh->prepare("SELECT connecte_id_utilisateur, connecte_derniere_action " .
        		"FROM zcov2_connectes " .
        		"WHERE connecte_derniere_action < NOW() - INTERVAL ".NOMBRE_MINUTES_CONNECTE." MINUTE AND connecte_id_utilisateur IS NOT NULL");
        $stmt->execute();
        $membres = $stmt->fetchAll();

        foreach ($membres as $m)
        {
        	$stmt = $dbh->prepare("UPDATE zcov2_utilisateurs " .
        			"SET utilisateur_date_derniere_visite = :date " .
        			"WHERE utilisateur_id = :id");
        	$stmt->bindParam(':date', $m['connecte_derniere_action']);
        	$stmt->bindParam(':id', $m['connecte_id_utilisateur']);
        	$stmt->execute();
        }

        $stmt = $dbh->prepare("DELETE FROM zcov2_connectes " .
        		"WHERE connecte_derniere_action < NOW() - INTERVAL ".NOMBRE_MINUTES_CONNECTE." MINUTE");
        $stmt->execute();
	}
}
