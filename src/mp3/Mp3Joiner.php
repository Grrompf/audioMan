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


use audioMan\interfaces\FileTypeInterface;
use audioMan\utils\Messenger;
use audioMan\utils\Tools;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Mp3Joiner extends Messenger implements FileTypeInterface
{
    private $mp3Fixer;

    public function __construct()
    {
        $this->mp3Fixer = new Mp3Fixer();
    }

    /**
     * Merges all mp3 files to one. Merged file is given a temporary name.
     */
    final public function join(array $files): bool
    {
        $newFilename = self::CONCAT_FILE_NAME;

        //remove eventually garbage files
        if (($key = array_search(self::CONCAT_FILE_NAME, $files)) !== false) {
            unset($files[$key]);
        }
        if (empty($files)) {
            return false;
        }


        $msg = "Join <".count($files)." mp3 files> in <".basename(getcwd()).">";
        $this->comment($msg);
        $size = 0;

        //concatenating mp3 files
        foreach ($files as $filePart) {

            //just in case
            if ($filePart === self::CONCAT_FILE_NAME || $filePart === self::CORRECTED_FILE_NAME) {
                continue;
            }
            $this->debug($filePart);
            //file size
            $size += filesize($filePart);

            $cmd = "cat ".escapeshellarg($filePart)." >> ".$newFilename.' 2> /dev/null';
            exec($cmd, $output, $retVal);
            if (0 !== $retVal) {
                $this->error("Error while merging <".$filePart."> in <".getcwd().">".PHP_EOL."Details: ".implode($output));
                $msg = PHP_EOL."Exit".PHP_EOL;
                die($msg);
            }

        }
        // size of merged product
        $mergedSize = filesize($newFilename);
        $this->info("Merged <".count($files)." mp3 files> in <".basename(getcwd()).">");

        // size check
        if (Tools::getMB($size) === Tools::getMB($mergedSize)) {
            $this->comment("File size : <".Tools::getMB($size)." MB>");
        } else {
            $msg = "Size of merged files of <".basename(getcwd())."> does not match expectation.".PHP_EOL."Please investigate manually";
            $this->caution($msg);
        }

        //fixing time issue
        $this->mp3Fixer->fix();

        return true;
    }
}