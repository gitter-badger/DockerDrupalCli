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
 * Class BehatCommand
 * @package Docker\Drupal\Command
 */
class BehatCommand extends Command {
	protected function configure() {
		$this
			->setName('behat:cmd')
			->setDescription('Run behat commands ')
			->setHelp("This command will execute Behat commands directly against your Drupal APP.")
      ->addOption('suite', '-s', InputOption::VALUE_OPTIONAL, 'Suite of features to test [global_features]')
      ->addOption('profile', '-p', InputOption::VALUE_OPTIONAL, 'Profile to test [local]')
      ->addOption('tags', '-t', InputOption::VALUE_OPTIONAL, 'Tags to test [about]');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$application = $this->getApplication();

    $suite = $input->getOption('suite');
    $profile = $input->getOption('profile');
    $tags = $input->getOption('tags');

		$io = new DockerDrupalStyle($input, $output);
    $config = $application->getAppConfig($io);

    if ($config) {
      $type = $config['apptype'];
    }

    if (isset($type) && $type == 'D8') {
      // D8 behat sites not yet supporter
      $io->info('Drupal 8 behat test suite not currently supported. ');
      return;
    } elseif (isset($type) && $type == 'D7') {

      $cmd = '--config /root/behat/behat.yml ';

      if (isset($suite) && $suite != NULL) {
        $cmd .= ' --suite ' . $suite;
      }

      if (isset($profile) && $profile != NULL) {
        $cmd .= ' --profile ' . $profile;
      }

      if (isset($tags) && $tags != NULL) {
        $cmd .= ' --tags ' . $tags;
      }

    }	else {
      $io->error('You\'re not currently in an Drupal APP directory');
      return;
    };

    $io->section('EXEC behat ' . $cmd);
    $command = 'docker exec -it $(docker ps --format {{.Names}} | grep behat) sh -c "behat ' . $cmd . '"';
    $application->runcommand($command, $io);

	}
}
