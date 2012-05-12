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

/**
 * Mise à jour des statistiques d'affichage des annonces.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */

namespace Zco\Bundle\AnnoncesBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CronCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('cron:annonces')
			->setDescription('Update announces\' views statistics');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$cache = $this->getContainer()->get('zco_core.cache');
	    $pks   = \Doctrine_Query::create()
        	->select('id')
        	->from('Annonce')
        	->execute(array(), \Doctrine_Core::HYDRATE_ARRAY);

        foreach ($pks as $annonce)
        {
        	$nbv = $cache->get('annonce_nbv-'.$annonce['id'], 0);
        	echo 'Annonce n°'.$annonce['id'].' : '.$nbv.' affichage(s)'."\n";

        	if ($nbv > 0)
        	{
        		\Doctrine_Query::create()
        			->update('Annonce')
        			->set('nb_affichages', 'nb_affichages + ?', $nbv)
        			->where('id = ?', $annonce['id'])
        			->execute();
        	}
        	
        	$cache->delete('annonce_nbv-'.$annonce['id']);
        }
	}
}
