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
    const MAX_LEVEL = 5;

    final protected function configure(): void
    {
        $this
            ->setName('audioMan')
            ->addOption('level', 'l', InputOption::VALUE_REQUIRED, 'set album nesting level')
            ->addOption('no-interaction', 'y', InputOption::VALUE_NONE, 'force answer always yes')
            ->addOption('no-normalize', 'N', InputOption::VALUE_NONE, 'force not normalizing file names')
            ->addOption('force-merge', null, InputOption::VALUE_NONE, 'force merging')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'custom file name format')
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
        Registry::set(Registry::KEY_VERBOSITY, $verbosity);

        //level
        if (true === $input->hasParameterOption(['--level', '-l'], true)) {
            $userInput = $input->getOption('level');

            if (!is_numeric($userInput)) {
                $msg = sprintf("Invalid level type <%s>. Allowed values: 0-%s.", $userInput, self::MAX_LEVEL);
                throw new \InvalidArgumentException($msg);
            }

            $level = (int) $userInput;
            if ($level < 0 || $level > self::MAX_LEVEL) {
                $msg = sprintf("Nesting level <%s> invalid. Allowed values: 0-%s.", $level, self::MAX_LEVEL);
                throw new \InvalidArgumentException($msg);
            }

            Registry::set(Registry::KEY_LEVEL, $level);
        }

        //normalization
        if (true === $input->hasParameterOption(['--no-normalize', '-N'], true)) {
            Registry::set(Registry::KEY_NORMALIZE, false);
        }

        //force-merge
        if (true === $input->hasParameterOption('--force-merge', true)) {
            Registry::set(Registry::KEY_FORCE_MERGE, true);
        }

        //no-interaction
        if (true === $input->hasParameterOption(['--no-interaction', '-y'], true)) {
            Registry::set(Registry::KEY_NO_INTERACTION, true);
        }

        //format
        if (true === $input->hasParameterOption(['--format', '-f'], false)) {
            $simplifiedRegex = $input->getOption('format');
            (new Separator())->setCustomSeparator($simplifiedRegex);
        }

        (new Requirements())->check();
        (new Main())->handle();

        return 0;
    }
}