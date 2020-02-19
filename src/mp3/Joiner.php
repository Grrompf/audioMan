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
use audioMan\Registry;
use audioMan\utils\Messenger;
use audioMan\utils\Tools;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Joiner extends Messenger implements FileTypeInterface
{
    private $mp3Fixer;

    public function __construct()
    {
        $this->mp3Fixer = new Mp3Fixer();
    }

    /**
     * Merges all mp3 files to one. Merged file is given a temporary name.
     */
    final public function join(array $audioFiles, string $path =''): bool
    {
        //todo: path
        $newFilename = $path.Registry::get(Registry::KEY_PATH_SEPARATOR).self::CONCAT_FILE_NAME;


        $msg = "Join <".count($audioFiles)."> mp3 files.";
        $this->comment($msg);
        $size = 0;

        //concatenating mp3 files
        foreach ($audioFiles as $file) {

            //todo: file exist
            //file size in kB
            $size += filesize($file)/1000;

            $cmd = "cat ".escapeshellarg($file)." >> ".escapeshellarg($newFilename).' 2> /dev/null';
            exec($cmd, $output, $retVal);
            if (0 !== $retVal) {
                $this->error("Error while merging <".$file.">. Details: ".implode($output));

                return false;
            }
        }

        $this->comment("Merged <".count($audioFiles)."> mp3 files.");

        // size of merged product in kB
        $mergedSize     = filesize($newFilename)/1000;
        $mergedSizeMB   = Tools::getMB($mergedSize);
        $expectedSizeMB = Tools::getMB($size);

        // size check
        if ($expectedSizeMB === $mergedSizeMB) {
            $this->comment("File size : <".$expectedSizeMB." MB>");
        } else {
            $msg = "Size of merged files <".$mergedSizeMB.">do not match the expected size <".$expectedSizeMB.">. Please investigate manually";
            $this->error($msg);

            return false;
        }

        //fixing time issue
        $this->mp3Fixer->fix();

        return true;
    }

    private function fixMp3Length(string $newFileName): bool
    {
        //todo: move file? no
        //todo: collect file names
        $path = pathinfo($newFileName, PATHINFO_DIRNAME);
        $out = $path.Registry::get(Registry::KEY_PATH_SEPARATOR).self::CORRECTED_FILE_NAME;

        //correcting using ffmpeg
        $this->comment("Correcting mp3 file time using ffmpeg library.");
        $cmd = sprintf('ffmpeg -loglevel quiet -y -i %s -acodec copy %s', $newFileName, $out);

        exec($cmd, $output, $retVal);
        if (0 !== $retVal) {
            $this->error("Error while fixing file time. Details: ".implode($output));

            return false;
        }

        return true;
    }
}