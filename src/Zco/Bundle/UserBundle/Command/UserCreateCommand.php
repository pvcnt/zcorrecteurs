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
 * Création d'un compte utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UserCreateCommand extends ContainerAwareCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('user:create')
			->addArgument('username', InputArgument::REQUIRED, 'The username')
			->addArgument('email', InputArgument::REQUIRED, 'The email')
			->addArgument('password', InputArgument::REQUIRED, 'The password')
			->addOption('admin', null, InputOption::VALUE_NONE, 'Set the user as super admin')
			->addOption('invalid', null, InputOption::VALUE_NONE, 'Set the user as invalid')
			->setDescription('Creates a new user account')
			->setHelp('The <info>user:create</info> command creates a user:

  <info>php app/console user:create vincent vincent@zcorrecteurs.fr password</info>

You can create an administrator via the admin flag:

  <info>php app/console user:create admin admin@zcorrecteurs.fr password --admin</info>

You can create an invalid user (will not be able to log in):

  <info>php app/console user:create admin admin@zcorrecteurs.fr password --invalid</info>');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		//On commence par créer une entité répondant aux options fournies.
		$user = new \Utilisateur;
		$user->setUsername($input->getArgument('username'));
		$user->setRawPassword($input->getArgument('password'));
		$user->setEmail($input->getArgument('email'));
		if (!$input->getOption('invalid'))
		{
			$user->setAccountValid(true);
		}
		if ($input->getOption('admin'))
		{
			$user->setGroupId(GROUPE_ADMINISTRATEURS);
		}
		
		//On valide l'entité fournie conformément aux contraintes placées dessus.
		$validator = $this->getContainer()->get('validator');
		$errors    = $validator->validate($user, array('registration'));
		if (count($errors) > 0)
		{
			throw new \InvalidArgumentException((string) $errors);
		}
		
		//Propagation de l'événement avant inscription.
	    $event = new FilterRegisterEvent($user);
		$this->getContainer()->get('event_dispatcher')->dispatch(UserEvents::PRE_REGISTER, $event);
		if ($event->isAborted())
		{
			throw new \RuntimeException($event->getErrorMessage() ?: 'Error while registering.');
		}
		
		//Inscription effective de l'utilisateur.
		\Doctrine_Core::getTable('Utilisateur')->insert($user);
		
		//Propagation de l'événement après inscription.
		$event = new RegisterEvent($user);
		$this->getContainer()->get('event_dispatcher')->dispatch(UserEvents::POST_REGISTER, $event);
		
		$output->writeln(sprintf('Tue user "<info>%s</info>" has been created.', $user->getUsername()));
		
		return 0;
	}
}
