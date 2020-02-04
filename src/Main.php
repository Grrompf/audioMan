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

namespace audioMan;

use audioMan\mp3\Mp3Formatter;
use audioMan\mp3\Mp3Normalizer;
use audioMan\mp3\Mp3Processor;
use audioMan\mp3\Mp3TagWriter;
use audioMan\utils\Scanner;
use audioMan\utils\SubDirFinder;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Main extends AbstractBase
{
    // todo: git
    // todo: version

    private $subDirScanner;

    public function __construct()
    {
        parent::__construct(new Scanner());
    }

    final public function handle(): void
    {
        $msg = "Start assembling audio files!".PHP_EOL."Investigating <".basename($this->getScanner()->getRootDir()).">";
        $this->comment($msg);

        $actualPath = getCwd();
        $finder = new SubDirFinder();
        $pathCollection = $finder->find($actualPath);
        $processor = new Mp3Processor($this->getScanner());

        //processing files bringing them to root level
        for ($i=$pathCollection->getMaxLevel(); $i>0; $i--) {
            $subDirs = $pathCollection->findByLevel($i);
            foreach($subDirs as $path) {
                chdir($path);
                $processor->handle();
            }
        }

        //renaming files
        $formatter = new Mp3Formatter($this->getScanner());
        $formatter->handle();

        //tagging
        $tagger = new Mp3TagWriter($this->getScanner());
        $tagger->handle();

        //OPTION: normalizing
        if (Registry::get(Registry::KEY_NORMALIZE)) {
            $normalizer = new Mp3Normalizer($this->getScanner());
            $normalizer->handle();
        }

        $this->success("Finished <".basename($actualPath).">");
        $this->break();

        return;

        $worker = new Worker();
        $noSubDirs = count(explode('/', $actualPath));
        echo $worker->process($dirs, $noSubDirs);

        return;

        $directory = new \RecursiveDirectoryIterator($actualPath);
        $iterator = new \RecursiveIteratorIterator($directory);
        $dirs = [];

        /** @var \SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo)
        {
            if ($fileInfo->isDir() && $fileInfo->getPath() !== $actualPath)
            {
                $dirs[] = ($fileInfo->getPath());
            }
        }
        var_dump(array_unique($dirs));
        return;

        $iterator = new \DirectoryIterator($this->getScanner()->getRootDir());
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isDir()) {
                chdir($fileInfo->getRealPath());
//                $msg = "Sub directory found: <".basename(getcwd()).">. Investigating ...";
//                $this->comment($msg);

                $this->subDirScanner->handle();
                //chdir($fileInfo->getRealPath());
            }
//            echo 'DIR: '.getcwd()."\n";
//            $this->processor->handle();
        }
    }

    private function scanSubDir(string $path): void
    {
        $actualPath = getCwd();
        $msg = "Scan for sub directories in <".$actualPath.">";
        $this->info($msg);

        $iterator = new \DirectoryIterator($path);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->isFile()) {
                continue;
            }
            if ($fileInfo->isDir()) {
                chdir($fileInfo->getRealPath());
                $subDirectory = getcwd();
                $msg = "Sub directory found: <".$subDirectory.">. Change to dir...";
                $this->comment($msg);

                if (!$this->getScanner()->scanFiles('mp3')) {
                    $this->scanSubDir($subDirectory);
                }
                $processor = new Processor($this->getScanner());

                while ($processor->isProcessing()) {

                    chdir('..');
                    //break if root dir is reached
                    if (getcwd() === $subDirectory) {
                        $msg = "Start directory reached. <".$subDirectory.">. Break.";
                        $this->comment($msg);
                        break;
                    }
                    $msg = "Changed to parent directory: <".getcwd().">.";
                    $this->info($msg);
                }
            }
        }
    }
}