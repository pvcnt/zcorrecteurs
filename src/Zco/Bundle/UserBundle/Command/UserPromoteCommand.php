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

use Zco\Bundle\UserBundle\Event\RegisterEvent;
use Zco\Bundle\UserBundle\Event\FilterRegisterEvent;
use Zco\Bundle\UserBundle\UserEvents;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Promotion d'un compte utilisateur administrateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserPromoteCommand extends ContainerAwareCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('user:promote')
			->addArgument('username', InputArgument::REQUIRED, 'The username')
			->setDescription('Promotes an user as administrator')
			->setHelp('The <info>user:promote</info> command promotes an user as administrator:

  <info>php app/console user:promote vincent</info>');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if (!($user = \Doctrine_Core::getTable('Utilisateur')->getOneByPseudo($input->getArgument('username'))))
		{
			$output->writeln('<ERROR>No user with this username was found.</ERROR>');
			
			return -1;
		}
		if ($user->getGroupId() == GROUPE_ADMINISTRATEURS)
		{
			$output->writeln('<ERROR>The user is already an administrator.</ERROR>');
			
			return -1;
		}
		
		$user->setGroupId(GROUPE_ADMINISTRATEURS);
		$user->save();
		
		$output->writeln(sprintf('The user "<info>%s</info>" has been promoted administrator.', $user->getUsername()));
		
		return 0;
	}
}
