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

namespace Zco\Bundle\UserBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Zco\Bundle\GroupesBundle\Domain\GroupDAO;

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

  <info>php bin/console user:promote vincent</info>');
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

		$adminGroup = GroupDAO::InfosGroupe(\Groupe::ADMIN);
		if ($user->getGroupId() == $adminGroup['groupe_id'])
		{
			$output->writeln('<ERROR>The user is already an administrator.</ERROR>');
			
			return -1;
		}
		
		$user->setGroupId($adminGroup['groupe_id']);
		$user->save();
		$output->writeln(sprintf('The user "<info>%s</info>" has been promoted administrator.', $user->getUsername()));
		
		return 0;
	}
}
