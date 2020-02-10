<?php
declare(strict_types=1);
/**
 * @license MIT License <https://opensource.org/licenses/MIT>
 *
 * Copyright (c) 2020 Dr. Holger Maerz
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace audioMan\command;

use audioMan\Main;
use audioMan\Registry;
use audioMan\Requirements;
use audioMan\utils\Tools;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class DefaultCommand extends Command
{
    final protected function configure(): void
    {
        $this
            ->setName('audioMan')
            ->addArgument('root', InputArgument::OPTIONAL, 'Directory to scan')
            ->addOption('multiple', 'm', InputOption::VALUE_NONE, 'multiple audio books')
            ->addOption('no-normalize', 'N', InputOption::VALUE_NONE, 'force not normalizing file names')
            ->addOption('force', null, InputOption::VALUE_NONE, 'force processing on deep directory structures')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'custom file name format')
            ->addOption('out', 'o', InputOption::VALUE_REQUIRED, 'custom output directory')
            ->addOption('volumes', null, InputOption::VALUE_NONE, 'force volumes')
            ->setHelp('Find more information in README.md')
            ->setDescription("Merges multiple audio files. Suited for audio books and radio play.".PHP_EOL.
                "  Time issue of merged files are corrected. File size of is checked, too.".PHP_EOL.
                "  Wma files are converted. If album art (cover) is found, it is tagged, next to title, album and genre.".PHP_EOL.
                "  Files are renamed in format <# - title.mp3> and finally normalized.")
        ;
    }

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->hasArgument('root')) {
            $rootDir = $input->getArgument('root');
            //important to resolve home directory
            $rootDir = str_replace('~', getenv('HOME'), $rootDir);
        }

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

        //normalization
        $normalize = true;
        if (true === $input->hasParameterOption(['--no-normalize', '-N'], true)) {
            $normalize = false;
        }

        //volumes
        $volumes = false;
        if (true === $input->hasParameterOption('--volumes', true)) {
            $volumes = true;
        }

        //force
        $force = false;
        if (true === $input->hasParameterOption('--force', true)) {
            $force = true;
        }

        //multiple
        $multiple = false;
        if (true === $input->hasParameterOption(['--multiple', '-m'], true)) {
            $multiple = true;
        }

        //format
        if (true === $input->hasParameterOption(['--format', '-f'], false)) {
            //todo check VALIDATE!!!!
            Registry::set(Registry::KEY_FORMAT, $input->getOption('format'));
        }

        //output
        if (true === $input->hasParameterOption(['--out', '-o'], false)) {

            $outDir = $input->getOption('out');
            //important to resolve home directory
            $outDir = str_replace('~', getenv('HOME'), $outDir);
            Tools::createDir($outDir);
            Registry::set(Registry::KEY_OUTPUT, $outDir);
        }

        Registry::set(Registry::KEY_FORCE, $force);
        Registry::set(Registry::KEY_VOLUMES, $volumes);
        Registry::set(Registry::KEY_NORMALIZE, $normalize);
        Registry::set(Registry::KEY_VERBOSITY, $verbosity);
        Registry::set(Registry::KEY_MULTIPLE, $multiple);

        (new Requirements())->check();
        (new Main())->handle();

        return 0;
    }
}