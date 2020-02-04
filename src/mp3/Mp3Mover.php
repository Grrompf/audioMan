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
class Mp3Mover extends Messenger implements FileNameInterface
{
    /**
     * Rename joined mp3 file after correction to upper dir. New file name is the name of the parent dir.
     */
    final public function move(): void
    {
        $oldFileName = self::CORRECTED_FILE_NAME;
        $parentDir = dirname(getcwd(), 1);
        $newFilename = basename(getcwd()).'.mp3';
        $newFilePath = $parentDir."/".$newFilename;

        $msg = "Moving file <".$oldFileName."> to parent directory <".basename($parentDir).">.".PHP_EOL."New file name is <".$newFilename.">";
        $this->info($msg);

        //rename korrigiert.mp and move file to upper dir
        $move = escapeshellarg($newFilePath);
        $moveCmd = 'mv '.$oldFileName." $move";
        exec($moveCmd, $output, $retVal);
        if (0 !== $retVal) {
            $this->error("Error while moving <".$oldFileName."> in <".getcwd().">".PHP_EOL."Details: ".$output);
            $msg = "Exit".PHP_EOL;
            die($msg);
        } else {
            $this->info("Moving <".$oldFileName."> to parent directory <".basename($parentDir).">.".PHP_EOL."New file name is <".$newFilename.">");
        }

        //removing kombiniert.mp3
        $removeCmd = 'rm '.self::CONCAT_FILE_NAME;
        exec($removeCmd, $output, $retVal);
        if (0 !== $retVal) {
            $this->error("Error while removing <".self::CONCAT_FILE_NAME."> in <".getcwd().">".PHP_EOL."Details: ".$output);
            $msg = "Exit".PHP_EOL;
            die($msg);
        } else {
            $this->info("Removed <".self::CONCAT_FILE_NAME."> in <".basename(getcwd()).">.");
        }
    }
}