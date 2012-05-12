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
 * Mise à jour des statistiques d'affichages et clics sur les publicités.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */

namespace Zco\Bundle\PubliciteBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CronCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('cron:publicites')
			->setDescription('Update ads\' views and clicks statistics');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$cache = $this->getContainer()->get('zco_core.cache');
	    $pks   = \Doctrine_Query::create()
        	->select('id, campagne_id')
        	->from('Publicite')
        	->execute(array(), \Doctrine_Core::HYDRATE_ARRAY);

        foreach ($pks as $pub)
        {
        	$nbv = $cache->get('pub_nbv-'.$pub['id'], 0);
        	$output->writeln('Publicité n°'.$pub['id'].' : '.$nbv.' affichage(s)');

        	if ($nbv > 0)
        	{
        		\Doctrine_Query::create()
        			->update('Publicite')
        			->set('nb_affichages', 'nb_affichages + ?', $nbv)
        			->where('id = ?', $pub['id'])
        			->execute();
        		\Doctrine_Query::create()
        			->update('PubliciteCampagne')
        			->set('nb_affichages', 'nb_affichages + ?', $nbv)
        			->where('id = ?', $pub['campagne_id'])
        			->execute();
        		$ret = \Doctrine_Query::create()
        			->update('PubliciteStat')
        			->set('nb_affichages', 'nb_affichages + ?', $nbv)
        			->where('publicite_id = ?', $pub['id'])
        			->andWhere('date = DATE(NOW())')
        			->execute();
        		if (!$ret)
        		{
        			$stat = new \PubliciteStat();
        			$stat['publicite_id']  = $pub['id'];
        			$stat['date']          = date('Y-m-d');
        			$stat['nb_clics']      = 0;
        			$stat['nb_affichages'] = $nbv;
        			$stat->save();
        		}
        	}
        	
        	$cache->delete('pub_nbv-'.$pub['id']);
        }
	}
}
