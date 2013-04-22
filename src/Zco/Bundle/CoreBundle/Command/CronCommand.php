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

namespace Zco\Bundle\CoreBundle\Command;

use Zco\Bundle\CoreBundle\CoreEvents;
use Zco\Bundle\CoreBundle\Event\CronEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Cron. Cette commande en elle-même ne fait rien, elle se contente de propager 
 * un événement afin de permettre à chaque bundle d'exécuter périodiquement 
 * les actions qu'ils souhaitent.
 *
 * Deux crons différents sont gérés ici :
 *   - un cron s'exécutant à chaque fin d'heure (en minute 59), utile notamment 
 *     pour tous les bundles ayant des statistiques à mettre à jour pour l'heure 
 *     écoulée ;
 *   - un cron s'exécutant tous les jours à minuit.
 *
 * Pour des besoins plus spécifiques, vous devrez créer vos propres commandes 
 * et demander à les installer dans la crontab.
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
            ->setName('zco:cron')
            ->setDescription('Runs periodic commands')
            ->addOption('hourly', null, InputOption::VALUE_NONE, 'Run the hourly cron')
            ->addOption('daily', null, InputOption::VALUE_NONE, 'Run the daily cron')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force the run');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('daily') && !$input->getOption('hourly')) {
            throw new \InvalidArgumentException('You must run the cron in hourly or daily mode.');
        }

        ignore_user_abort(true);
        set_time_limit(0);

        $dispatcher = $this->getContainer()->get('event_dispatcher');
        $registry   = $this->getContainer()->get('zco_core.registry');

        //Si on souhaite lancer le cron horaire.
        if ($input->getOption('hourly')) {
            $lastRun = $registry->get('zco_core.hourly_cron.last_run');
            $event   = new CronEvent($output, $lastRun);
            if ($input->getOption('force') || $event->ensureHourly()) {
                $startTime = microtime(true);
                $dispatcher->dispatch(CoreEvents::HOURLY_CRON, $event);
                $registry->set('zco_core.hourly_cron.last_run', date('Y-m-d H:i:s'));
                $output->writeln(
                    'Hourly cron terminated <info>successfully</info> in '
                    . ceil((microtime(true) - $startTime) * 1000) . ' ms'
                );
            } else {
                $output->writeln(sprintf('<error>Hourly cron already launched less than an hour ago (%s)</error>', $lastRun));
            }
        }

        //Si on souhaite lancer le cron quotidien.
        if ($input->getOption('daily')) {
            $lastRun = $registry->get('zco_core.daily_cron.last_run');
            $event   = new CronEvent($output, $lastRun);
            if ($input->getOption('force') || $event->ensureDaily()) {
                $startTime = microtime(true);
                $dispatcher->dispatch(CoreEvents::DAILY_CRON, new CronEvent($output, $lastRun));
                $registry->set('zco_core.daily_cron.last_run', date('Y-m-d H:i:s'));
                $output->writeln(
                    'Daily cron terminated <info>successfully</info> in '
                    . ceil((microtime(true) - $startTime) * 1000) . ' ms'
                );
            } else {
                $output->writeln(sprintf('<error>Daily cron already launched less than an day ago (%s)</error>', $lastRun));
            }
        }
    }
}