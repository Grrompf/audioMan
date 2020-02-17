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

namespace audioMan\utils;

use audioMan\AbstractBase;
use audioMan\Registry;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class OutPut extends AbstractBase
{
    private $isCopy;
    private $workingDir;

    public function __construct(string $actualPath, bool $isCopy=false)
    {
        parent::__construct();
        $this->workingDir = $actualPath;
        $this->isCopy = $isCopy;
    }

    final public function handle(): void
    {
        $parentDir = basename($this->workingDir); //get Parent
        $output = rtrim(Registry::get(Registry::KEY_OUTPUT), '/');
        $outDir = $output.'/'.$parentDir;
        Tools::createDir($outDir); //create album dir
        $this->info("Start moving files from<".basename(getcwd())."> to <".$outDir.">.");

        chdir($this->workingDir);
        //rescan
        if (false === $files = $this->getScanner()->scanFiles('mp3', true)) {
            $this->warning("No files found in <".basename(getcwd()).">!".PHP_EOL.'Skip moving files.');
            return;
        }

        foreach ($files as $fileName) {
            $this->move($fileName, $outDir);
        }

        $this->workingDir = $outDir;
    }

    final public function getWorkingDir(): string
    {
        return $this->workingDir;
    }

    private function move(string $fileName, string $outDir, bool $isCopy=false): void
    {
          $cmd = $isCopy?'cp':'mv';
          $origin = $this->workingDir."/".$fileName;
          $move = $outDir."/".$fileName;

          $moveCmd = $cmd.' '.escapeshellarg($origin)." ".escapeshellarg($move);
          exec($moveCmd, $output, $retVal);
          if (0 !== $retVal) {
              $this->error("Error while moving <".$origin."> to <".$outDir.">".PHP_EOL."Details: ".implode($output));
              $msg = PHP_EOL."Exit".PHP_EOL;
              die($msg);
          } else {
              $this->info("Moving <".$origin."> to <".basename($outDir).">.");
          }
    }

}