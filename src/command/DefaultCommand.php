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

use audioMan\interfaces\FileTypeInterface;
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
class DefaultCommand extends Command implements FileTypeInterface
{
    final protected function configure(): void
    {
        $this
            ->setName('audioMan')
            ->addOption('audio', 'a', InputOption::VALUE_REQUIRED, 'custom audio format ('.implode(', ', self::AUDIO_TYPES).')')
            ->addOption('level', 'l', InputOption::VALUE_REQUIRED, 'set album nesting level')
            ->addOption('no-interaction', 'y', InputOption::VALUE_NONE, 'force answer always yes')
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
        Registry::set(Registry::KEY_VERBOSITY, $verbosity);

        //audio
        if (true === $input->hasParameterOption(['--audio', '-a'], true)) {
            $audio = strtolower($input->getOption('audio'));
            if (!in_array($audio, self::AUDIO_TYPES)) {
                $msg = sprintf("Audio type <%s> unknown. Allowed audio types: %s", $audio, implode(', ', self::AUDIO_TYPES));
                throw new \InvalidArgumentException($msg);
            }

            Registry::set(Registry::KEY_AUDIO, $audio);
        }

        //normalization
        if (true === $input->hasParameterOption(['--no-normalize', '-N'], true)) {
            Registry::set(Registry::KEY_NORMALIZE, false);
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

        //output
        if (true === $input->hasParameterOption(['--out', '-o'], false)) {
            $outDir = $input->getOption('out');
            Registry::set(Registry::KEY_OUTPUT, $outDir);
        }

        (new Requirements())->check();
        (new Main())->handle();

        return 0;
    }
}