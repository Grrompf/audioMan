#!/usr/bin/env php
<?php
// AudioMan.php
require __DIR__.'/../vendor/autoload.php';

use audioMan\Requirements;
use audioMan\Main;
use audioMan\Registry;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

(new Application('audioMan', '@package_version@'))
    ->register('audioMan')
    ->addArgument('root', InputArgument::OPTIONAL, 'Directory to scan')
    ->addOption('normalize', 'N', InputOption::VALUE_NONE, 'Normalizing')
    ->setHelp('Find more information in README.md')
    ->setDescription("Merges multiple audio files. Suited for audio books and radio play.".PHP_EOL.
        "  Time issue of merged files are corrected. File size of is checked, too.".PHP_EOL.
        "  Wma files are converted. If album art (cover) is found, it is tagged, next to title, album and genre.".PHP_EOL.
        "  Files are renamed in format <# - title.mp3> and finally normalized.")
    ->setCode(function(InputInterface $input, OutputInterface $output) {

        //verbose level
        $verbosity = 0;
        if (true === $input->hasParameterOption(['--quiet', '-q'], true)) {
            $verbosity = -1;
        } else {
            if ($input->hasParameterOption('-vvv', true)) {
                $verbosity = 3;
            } elseif ($input->hasParameterOption('-vv', true)) {
                $verbosity = 2;
            } elseif ($input->hasParameterOption('-v', true) || $input->hasParameterOption('--verbose', true) || $input->getParameterOption('--verbose', false, true)) {
                $verbosity = 1;
            }
        }

        $normalize = false;
        if (true === $input->hasParameterOption(['--normalize', '-N'], true)) {
            $normalize = true;
        }

        Registry::set(Registry::KEY_NORMALIZE, $normalize);
        Registry::set(Registry::KEY_VERBOSITY, $verbosity);
        (new Requirements())->check();
        (new Main())->handle();

    })
    ->getApplication()
    ->setDefaultCommand('audioMan', true) // Single command application
    ->run();