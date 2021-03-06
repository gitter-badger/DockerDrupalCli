<?php

/**
 * @file
 * Contains \Docker\Drupal\Command\DemoCommand.
 */

namespace Docker\Drupal\Command\Drush;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Docker\Drupal\Style\DockerDrupalStyle;

/**
 * Class DemoCommand
 * @package Docker\Drupal\Command
 */
class DrushCommand extends Command {
	protected function configure() {
		$this
			->setName('drush:cmd')
			->setDescription('Run drush commands ')
			->setHelp("This command will execute Drush commands directly against your Drupal APP.")
			->addOption('cmd', 'c', InputOption::VALUE_OPTIONAL, 'Specify the command ["bash"]');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$application = $this->getApplication();

		$cmd = $input->getOption('cmd');

		$io = new DockerDrupalStyle($input, $output);
		$io->section('EXEC drush ' . $cmd);

		if (!$cmd) {
			$helper = $this->getHelper('question');
			$question = new Question('Enter command : ', 'bash');
			$cmd = $helper->ask($input, $output, $question);
		}

		$command = 'docker exec -i $(docker ps --format {{.Names}} | grep php) drush ' . $cmd;
		$application->runcommand($command, $io);

	}
}
