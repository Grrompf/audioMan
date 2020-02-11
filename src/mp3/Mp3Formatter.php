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
    const DEFAULT_PATTERN   = '#^\d+\s\-\s(.*)$#';
    const DEFAULT_SEPARATOR = ' - ';

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
    private function makeFileName(string $basename): string
    {
        $fileName = pathinfo($basename, PATHINFO_FILENAME);

        //remove whitespaces
        $fileName = trim($fileName);

        //default: '1. file', '01 - file', '(07).file', '07 file', '07-file', '(07).file',
        $pattern = '#([A-z].*)$#';;
        if (1 !== preg_match($pattern, $fileName, $matches)) {
            $this->cleanTitle($fileName);
            return $fileName.'.mp3';
        }
        $title = $matches[1];

        //no number!
        $extract = str_replace($title, '', $fileName);
        $numberPattern = '#(\d+).*$#';
        if (1 !== preg_match($numberPattern, $extract, $matches)) {
            $this->cleanTitle($fileName);
            return $fileName.'.mp3';
        }
        $number = $matches[1];

        //bad format: fronting eg AFD 07 - get me.mp3
        if (strlen($title) === strlen($fileName)) {
            $frontingPattern = '#^[A-z]+[-\.\s]*[0-9]+[-\.\s]*(.*)$#';
            if (1 === preg_match($frontingPattern, $fileName, $matches)) {
                $title  = $matches[1];
            } else {
                $this->cleanTitle($fileName);
                return $fileName.'.mp3';
            }
        }

        //always leading number on single digits
        if (strlen($number) == 1) {
            $number = '0'.$number;
        }

        $this->cleanTitle($title);
        $fileName = $number.$this->getSeparator().$title;

        //custom format
        if ($this->isAppending()) {
            $fileName = $title.$this->getSeparator().$number;
        }

        return $fileName.'.mp3';
    }

    private function cleanTitle(string &$title): void
    {
        $title = str_replace('--', '-', $title);
        $title = str_replace(' - ', '-', $title);
    }

    private function isAppending(): bool
    {
        //custom format
        if (Registry::get(Registry::KEY_FORMAT)) {
            return (bool) Registry::get(Registry::KEY_APPEND);
        }

        return false;
    }

    private function getSeparator(): string
    {
        //custom format
        if (Registry::get(Registry::KEY_FORMAT)) {
            return (string) Registry::get(Registry::KEY_SEPARATOR);
        }

        return self::DEFAULT_SEPARATOR;
    }
}