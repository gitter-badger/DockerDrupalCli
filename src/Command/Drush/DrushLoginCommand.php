<?php

/**
 * @file
 * Contains \Docker\Drupal\Command\DemoCommand.
 */

namespace Docker\Drupal\Command\Drush;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Docker\Drupal\Style\DockerDrupalStyle;

/**
 * Class DemoCommand
 * @package Docker\Drupal\Command
 */
class DrushLoginCommand extends Command {
	protected function configure() {
		$this
			->setName('drush:uli')
			->setDescription('Run Drush ULI')
			->setHelp("This command will output a login URL.");
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$application = $this->getApplication();

		$io = new DockerDrupalStyle($input, $output);
		$io->section('EXEC drush ' . $cmd);
		$command = 'docker exec -i $(docker ps --format {{.Names}} | grep php) drush uli';
		$application->runcommand($command, $io);

	}
}
