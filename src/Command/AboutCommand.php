<?php

/**
 * @file
 * Contains \Docker\Drupal\Command\DemoCommand.
 */

namespace Docker\Drupal\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Filesystem\Filesystem;
use Docker\Drupal\Style\DockerDrupalStyle;

/**
 * Class DemoCommand
 * @package Docker\Drupal\Command
 */
class AboutCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('self:about')
            ->setAliases(['about'])
            ->setDescription('About DockerDrupal')
            ->setHelp("Output general INFO")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();
        $io = new DockerDrupalStyle($input, $output);


        if($dockerversion = $application->getDockerVersion()){
            $dv = $dockerversion;
        }else{
            $dv = 'Docker version [UNKNOWN]';
        }

        $io->info(' ');
        $io->title('DockerDrupal [cli] - '.$application->getVersion().' ::: Create and manage Drupal projects with Docker ::: '.$dv);

        $io->info(' ');
        $io->section(' Docker status ');

        // docker status
        if($application->checkDocker($io, false)){
            $io->success(' Docker Running');
        }else{
            $io->error(' Docker Not found');
        }

        $io->info(' ');
        $io->section(' PHP version ');
        $this->checkPHPVersion($io);

        $io->info(' ');
        $io->section(' GIT installed ');
        $this->checkGitInstalled($io);

        $io->info(' ');
        $io->section('Get started');

        $io->simple('SETUP DOCKER ENVIRONMENT');
        $io->info(' ');
        $io->info('     dockerdrupal env:init');
        $io->info(' ');

        $io->simple('BUILD DRUPAL 8 APP');
        $io->info(' ');
        $io->info('     dockerdrupal build:init my-new-app D8');
        $io->info(' ');

        $io->simple('BUILD DRUPAL 7 APP');
        $io->info(' ');
        $io->info('     dockerdrupal build:init my-new-app D7');
        $io->info(' ');

        $io->simple('AVAILABLE COMMANDS');
        $io->info(' ');
        $io->info('     dockerdrupal list');
        $io->info(' ');

    }

    /**
     * @return string
     */
    private function checkPHPVersion($io){
        $phpversion = intval(phpversion());
        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $io->success(' PHP VERSION :: '.PHP_VERSION );
        }else{
            $io->warning(' PHP VERSION is > 5.5.0 and should be upgraded.');
        }
    }

    /**
     * @return boolean
     */
    private function checkGitInstalled($io){
        exec('which git', $output);
        if ($output) {
            $io->success('GIT installed' );
        }else{
            $io->error('GIT not found. Please install.');
        }
    }


}