<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Tâche permettant de vider le cache.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CacheFlushCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:flush')
            ->setDescription('Flushes temporary cached files (application level)')
            ->setDefinition(array(
                new InputArgument('files', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'The files to delete (all if none specified)'),
            ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cache = $this->getContainer()->get('zco_core.cache');
        $args  = $input->getArgument('files');
        if (empty($args)) {
            $cache->flush();
        } else {
            foreach ($args as $arg) {
                $cache->delete($arg);
                $output->writeln('Cache "' . $arg . '" flushed');
            }
        }

        return 0;
    }

}