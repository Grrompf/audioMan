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
use audioMan\registry\Registry;
use audioMan\registry\Separator;
use audioMan\Requirements;
use audioMan\utils\Tools;
use Symfony\Component\Console\Command\Command;
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
            ->addOption('force-merge', 'm', InputOption::VALUE_NONE, 'force merge of all episodes')
            ->addOption('no-normalize', 'N', InputOption::VALUE_NONE, 'force not normalizing file names')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'custom file name format')
            ->addOption('out', 'o', InputOption::VALUE_REQUIRED, 'custom output directory')
            ->setHelp('Find more information in README.md')
            ->setDescription("Merges multiple audio files. Suited for audio books and radio play.".PHP_EOL.
                "  Time issue of merged files are corrected. File size of is checked, too.".PHP_EOL.
                "  Other audio formats are converted. If album art (cover) is found, it is tagged, next to title, album and genre.".PHP_EOL.
                "  Files are renamed in format <# - title.mp3> and finally normalized.")
        ;
    }

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
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

        //merge
        $merge = false;
        if (true === $input->hasParameterOption(['--force-merge', '-m'], true)) {
            $merge = true;
        }

        //format
        if (true === $input->hasParameterOption(['--format', '-f'], false)) {
            $simplifiedRegex = $input->getOption('format');
            (new Separator())->setCustomSeparator($simplifiedRegex);
        }

        //output
        if (true === $input->hasParameterOption(['--out', '-o'], false)) {

            $outDir = $input->getOption('out');

            Tools::createDir($outDir);
            Registry::set(Registry::KEY_OUTPUT, $outDir);
        } else {
            //output default: HOME/audioMan
            $output = Tools::createDir("~/audioMan");
            Registry::set(Registry::KEY_OUTPUT, $output);
        }
        Registry::set(Registry::KEY_NORMALIZE, $normalize);
        Registry::set(Registry::KEY_VERBOSITY, $verbosity);
        Registry::set(Registry::KEY_FORCE_MERGE, $merge);

        (new Requirements())->check();
        (new Main())->handle();

        return 0;
    }
}