<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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
 * Validation d'un compte utilisateur inactif.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserValidateCommand extends ContainerAwareCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('user:validate')
			->addArgument('username', InputArgument::REQUIRED, 'The username')
			->setDescription('Validates an account')
			->setHelp('The <info>user:validate</info> command validates an user account to make it able to log in:

  <info>php app/console user:validate vincent</info>');
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
		if ($user->isAccountValid())
		{
			$output->writeln('<ERROR>The user account is already valid.</ERROR>');
			
			return -1;
		}
		
		$user->setAccountValid(true);
		$user->save();
		
		$output->writeln(sprintf('The user "<info>%s</info>" has been validated.', $user->getUsername()));
		
		return 0;
	}
}
