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


use audioMan\interfaces\FileNameInterface;
use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Mp3AlbumCover extends Messenger implements FileNameInterface
{
    final public function import(string $cover): void
    {
        //expecting a well-known file name
        $fileName = self::CORRECTED_FILE_NAME;
        if (false === file_exists($fileName)) {
            $this->error("Expected file <".$fileName."> not found in <".basename(getcwd()).">");
        }

        //list tags cmd (tags are listed as an array)
        $cmd =sprintf("mid3v2 -l %s",  escapeshellarg($fileName));

        //mid3v2
        exec($cmd, $output, $retVal);
        if (0 !== $retVal) {
            $this->error("Error while listing all tags to <".$fileName."> in <".getcwd().">".PHP_EOL."Details: ".$output);
            $msg = "Exit".PHP_EOL;
            die($msg);
        }
        //has album cover
        if (strpos(implode($output),"APIC") > 0) {
            $this->warning("Album cover found.");
            return;
        }

        //import cmd
        $cmd =sprintf("mid3v2 -p %s %s", escapeshellarg($cover), escapeshellarg($fileName));

        //mid3v2
        exec($cmd, $output, $retVal);
        if (0 !== $retVal) {
            $details = implode(" | ", $output);
            $this->error("Error while import album cover <".$cover."> to <".$fileName."> in <".getcwd().">".PHP_EOL."Details: ".$details);
            $msg = PHP_EOL."Exit".PHP_EOL;
            die($msg);
        }
        $this->success("Album cover <".$cover."> imported.");
    }
}