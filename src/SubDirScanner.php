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

namespace Console;

use audioMan\AbstractBase;
use audioMan\mp3\Mp3Processor;
use audioMan\utils\Scanner;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class SubDirScanner extends AbstractBase
{
    private $processor;

    public function __construct(Scanner $scanner)
    {
        parent::__construct($scanner);
        $this->processor = new Mp3Processor($scanner);
    }

    final public function handle(): void
    {
        $actualPath = getCwd();
        $msg = "Scan for sub directories in <".$actualPath.">";
        $this->info($msg);

        $directory = new \RecursiveDirectoryIterator($actualPath);
        $iterator = new \RecursiveIteratorIterator($directory);
        $dirs = [];

        /** @var \SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo)
        {
            if ($fileInfo->isDir())
            {
                $dirs[] = ($fileInfo->getPath());
            }
        }
        var_dump(array_unique($dirs));


        return;
        $iterator = new \DirectoryIterator($actualPath);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                chdir($fileInfo->getRealPath());
                $this->handle();;
//                if (!$this->getScanner()->scanFiles('mp3')) {
//                    $this->scanSubDir($subDirectory);
//                }
//                $processor = new Processor($this->getScanner());
//
//                while ($processor->isProcessing()) {
//
//                    chdir('..');
//                    //break if root dir is reached
//                    if (getcwd() === $subDirectory) {
//                        $msg = "Start directory reached. <".$subDirectory.">. Break.";
//                        $this->comment($msg);
//                        break;
//                    }
//                    $msg = "Changed to parent directory: <".getcwd().">.";
//                    $this->info($msg);
//                }
            }
            if ($processing && $fileInfo->isFile()) {
                echo "PROCESSING ". getcwd().PHP_EOL;
                $processing = false;
                continue;
            }
        }
    }

    private function scanDir(): bool
    {

        // nur die verzeichnisse aufnehmen mit leveltiefe
        // danach rückwarts processing
        // rootDir ist das erste Verzeichnis
        $actualPath = getCwd();
        $msg = "Scan for sub directories in <".$actualPath.">";
        $this->info($msg);

        $iterator = new \DirectoryIterator($actualPath);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isDir()) {
                chdir($fileInfo->getRealPath());
                if (false === $this->scanDir()) {
                    $this->processor->handle();;
                    continue;
                }
            }
        }

        return false;
    }
}