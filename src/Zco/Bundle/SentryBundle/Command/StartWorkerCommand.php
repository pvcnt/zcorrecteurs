<?php

namespace Zco\Bundle\SentryBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Assetic\Util\ProcessBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Démarre le worker nécessaire au bon fonctionnement de la queue stockant les 
 * messages à envoyer à notre instance de Sentry.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class StartWorkerCommand extends ContainerAwareCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
			->setDescription('Starts the worker needed to flush the Sentry queue.')
			->addOption('vverbose', null, InputOption::VALUE_NONE, 'Very verbose')
			->setName('sentry:resque')
		;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$queueName = $this->getContainer()->getParameter('zco_sentry.client.resque');
		if (!$queueName)
		{
			throw new \LogicException('Sentry is not configured to work with a queue.');
		}
		
		$builder = new ProcessBuilder(array('php', 'vendor/resque/resque.php'));
		$builder
			->setWorkingDirectory($this->getContainer()->getParameter('kernel.root_dir').'/..')
			->setEnv('QUEUE', $queueName)
			->setEnv('APP_INCLUDE', 'src/Zco/Bundle/SentryBundle/Resque/SentryJob.php');
		
		if ($input->hasOption('vverbose'))
		{
			$builder->setEnv('VVERBOSE', '1');
		}
		elseif ($input->hasOption('verbose'))
		{
			$builder->setEnv('VERBOSE', '1');
		}
		
		$builder->getProcess()->run(function($type, $data) use($output)
		{
			if ('out' === $type)
			{
				$output->write('<info>>></info> '.$data);
			}
			else
			{
				$output->write('<error>>></error> '.$data);
			}
		});
	}
}
