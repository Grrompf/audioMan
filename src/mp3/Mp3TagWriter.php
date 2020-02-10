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
class Mp3TagWriter extends AbstractBase
{
    private $path;

    public function __construct(string $path)
    {
        parent::__construct();
        $this->path = $path;
    }

    /**
     * Rewriting title and album of the mp3 file
     */
    final public function handle(): void
    {
        //change to working dir
        chdir($this->path);
        $this->comment("Tag writing!");

        //rescan after renaming for having new name as title
        if (false === $files = $this->getScanner()->scanFiles('mp3', true)) {
            $this->warning("No tags written. No files found in <".basename(getcwd()).">!");
            $msg = PHP_EOL."Exit".PHP_EOL;
            die($msg);
        }

        foreach ($files as $fileName) {
            //tag editor
            $title = basename($fileName, '.mp3');
            $album = basename(getcwd());

            //cmd
            $cmd =sprintf("mid3v2 -q -t %s -A %s -g Other %s",
                escapeshellarg($title),
                escapeshellarg($album),
                escapeshellarg($fileName)
            );

            //mid3v2
            exec($cmd, $output, $retVal);
            if (0 !== $retVal) {
                $this->error("Error while writing tags to <".$fileName."> in <".getcwd().">".PHP_EOL."Details: ".$output);
                $msg = PHP_EOL."Exit".PHP_EOL;
                die($msg);
            }
        }

        //success
        if (count($files) > 0) {
            $msg = "Tags written to <".count($files). " files> in <".basename(Registry::get(Registry::KEY_LIB_DIR)).">";
            $this->comment($msg);
        } else {
            $this->comment("No tags written.");
        }
    }
}