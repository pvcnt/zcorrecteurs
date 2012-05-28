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

namespace Zco\Bundle\ZcorrectionBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Récupération des derniers tickets disponibles sur Drupal.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CronCommand extends ContainerAwareCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('cron:tickets')
			->setDescription('Update tickets');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
	    include_once(__DIR__.'/../modeles/drupal_support.php');
	
		//L'appel à la fonction ListerTicketsSupportDrupal() sans aucun argument 
		//va avoir pour effet de régénérer le cache de l'ensemble des tickets Drupal.
		$this->getContainer()->get('zco_core.cache')->delete('zcorrection-node_*');
		ListerTicketsSupportDrupal();
	}
}
