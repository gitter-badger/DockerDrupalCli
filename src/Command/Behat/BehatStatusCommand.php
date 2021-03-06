<?php

/**
 * @file
 * Contains \Docker\Drupal\Command\DemoCommand.
 */

namespace Docker\Drupal\Command\Behat;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Docker\Drupal\Style\DockerDrupalStyle;

/**
 * Class BehatStatusCommand
 * @package Docker\Drupal\Command
 */
class BehatStatusCommand extends Command {
  protected function configure() {
    $this
      ->setName('behat:status')
      ->setDescription('Runs example command against running APP and current config')
      ->setHelp("Currently hardcoded options [behat:status]");
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $application = $this->getApplication();

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
      $cmd = '--config /root/behat/behat.yml --suite global_features --profile local --tags about';
    }	else {
      $io->error('You\'re not currently in an Drupal APP directory');
      return;
    };

    $io->section('EXEC behat ' . $cmd);
    $command = 'docker exec -it $(docker ps --format {{.Names}} | grep behat) sh -c "behat ' . $cmd . '"';
    $application->runcommand($command, $io);

  }
}