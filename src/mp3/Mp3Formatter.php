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

namespace audioMan\mp3;

use audioMan\AbstractBase;
use audioMan\Registry;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Mp3Formatter extends AbstractBase
{
    private $path;

    public function __construct(string $path)
    {
        parent::__construct();
        $this->path = $path;
    }

    /**
     * Reformat mp3 fileName
     */
    final public function handle(): void
    {
        //change to working dir
        chdir($this->path);
        $this->comment("Name formatting!");

        //rescan after moving files
        if (false === $files = $this->getScanner()->scanFiles('mp3', true)) {
            $this->warning("No files found in <".basename(getcwd()).">!");
            $msg = PHP_EOL."Exit".PHP_EOL;
            die($msg);
        }

        $noChanges = 0;
        foreach ($files as $file) {
            $rename = $this->makeFileName($file);
            if ($rename !== $file) {
                $noChanges++;
                $move = escapeshellarg($rename);
                $orig = escapeshellarg($file);
                shell_exec("mv $orig $move");
            }
        }
        if ($noChanges > 0) {
            $msg = "<".$noChanges."/".count($files)." files> in <".basename(getcwd())."> renamed.";
            $this->comment($msg);
        } else {
            $msg = "Found <".count($files)." files> in <".basename(getcwd()).">.".PHP_EOL."No need for file renaming.";
            $this->info($msg);
        }

    }

    /**
     * Replacing dots for a dash resulting in file format "01 - File.mp3".
     */
    private function makeFileName(string $fileName): string
    {
        if ($this->isPerfect($fileName)) {
            return $fileName;
        }

        //remove brackets (07).get me.mp3
        $pattern = '#^[\\(|\\[|\\{]?(\d+)[\\)|\\]|\\}]?(.*)$#';
        if (1 === preg_match($pattern, $fileName, $matches)) {
            $fileName = $matches[1].$matches[2];
            if ($this->isPerfect($fileName)) {
                return $fileName;
            }
        }

        //find dash with spaces inside the filename eg file 07 - get - me.mp3
        $pattern = '#(([^\d]+)\s+\-\s+(\w+))#';
        if (1 === preg_match($pattern, $fileName, $matches)) {
            $replace = str_replace(' ', '', $matches[1]);
            $fileName = str_replace($matches[1], $replace, $fileName);
            if ($this->isPerfect($fileName)) {
                return $fileName;
            }
        }

        //remove point, dash or space eg 07.get me.mp3 | 07 get me.mp3 | 07-get me.mp3
        $pattern = '#^(\d+)(\.|\s|-)\s?(.*)$#';
        if (1 === preg_match($pattern, $fileName, $matches)) {
            $fileName = $matches[1].'-'.$matches[3];
            if ($this->isPerfect($fileName)) {
                return $fileName;
            }
        }

        //leading letters and spaces AFD 07 - get me.mp3
        $pattern = '#^(\w+\s+)?(\d+)(.*)$#';
        if (1 === preg_match($pattern, $fileName, $matches)) {
            $fileName = $matches[2].$matches[3];
            if ($this->isPerfect($fileName)) {
                return $fileName;
            }
        }

        //file number without spaces AFD 07-get me.mp3
        $pattern = '#^(\d+\-\w+).*$#';
        if (1 === preg_match($pattern, $fileName, $matches)) {
            $replace = str_replace('-', ' - ', $matches[1]);
            $fileName = str_replace($matches[1], $replace, $fileName);
            if ($this->isPerfect($fileName)) {
                return $fileName;
            }
        }

        return $fileName;
    }

    private function isPerfect(string $fileName): bool
    {
        //wanted 01 - playMe for fun.mp3
        $pattern = '#^\d+\s\-\s(.*)$#';

        return 1 === preg_match($pattern, $fileName, $matches);
    }
}