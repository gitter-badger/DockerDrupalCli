<?php

/**
 * @file
 * Contains \Docker\Drupal\Command\DemoCommand.
 */

namespace Docker\Drupal\Command\Mysql;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Docker\Drupal\Style\DockerDrupalStyle;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class MysqlImportExportCommand
 * @package Docker\Drupal\Command\Mysql
 */
class MysqlImportCommand extends Command {
	protected function configure() {
		$this
			->setName('mysql:import')
			->setDescription('Import .sql files')
			->setHelp("Use this to import .sql files to the current running APPs dev_db. [dockerdrupal mysql:import -p ./latest.sql]")
			->addOption('path', 'p', InputOption::VALUE_OPTIONAL, 'Specify import file path including filename');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$application = $this->getApplication();

		$io = new DockerDrupalStyle($input, $output);

		$helper = $this->getHelper('question');

		$io->warning("Dropping the database is potentially a very bad thing to do.\nAny data stored in the database will be destroyed.");

		$question = new ConfirmationQuestion('Do you really want to drop the \'dev_db\' database [y/N] : ', FALSE);
		if (!$helper->ask($input, $output, $question)) {
			return;
		}

		// GET AND SET APP TYPE
		$path = $input->getOption('path');
		if (!$path) {
			// specify import path
			$helper = $this->getHelper('question');
			$question = new Question('Specify import path, including filename : ');
			$importpath = $helper->ask($input, $output, $question);

			if (file_exists($importpath)) {

				//drop database;
				$command = 'docker exec -i $(docker ps --format {{.Names}} | grep db) mysql -u dev -pDEVPASSWORD -Bse "drop database dev_db;"';
				$application->runcommand($command, $io);

				// recreate dev_db
				$command = 'docker exec -i $(docker ps --format {{.Names}} | grep db) mysql -u dev -pDEVPASSWORD -Bse "create database dev_db;"';
				$application->runcommand($command, $io);

				// import new .sql file
				$command = 'docker exec -i $(docker ps --format {{.Names}} | grep db) mysql -u dev -pDEVPASSWORD dev_db < ' . $importpath;
				$application->runcommand($command, $io);

			} else {

				$io->error('Import .sql file not found at path ' . $importpath);
				exit;

			}
		}
	}
}