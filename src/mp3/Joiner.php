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
use audioMan\utils\GarbageCollector;
use audioMan\utils\Messenger;
use audioMan\utils\SkipCollector;
use audioMan\utils\Tools;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Joiner extends Messenger implements FileTypeInterface
{
    /**
     * Merges all mp3 files to one. Merged file is given a temporary name.
     */
    final public function merge(array $audioFiles, string $combinedFileName, string $newFileName): bool
    {
        //todo: we need to do everything on episode Path.. edit. moving to another dir does not work
        GarbageCollector::add($combinedFileName);

        $msg = "Join <".count($audioFiles)."> audio files.";
        $this->comment($msg);
        $size = 0;

        //concatenating mp3 files
        sort($audioFiles, SORT_DESC);
        foreach ($audioFiles as $file) {

            if (!file_exists($file)) {
                $this->error("File not found <".$file.">.");
                SkipCollector::add($file, SkipCollector::TYPE_EPISODE);

                return false;
            }

            //file size
            $size += filesize($file);

            //merge cmd
            $cmd = "cat ".escapeshellarg($file)." >> ".escapeshellarg($combinedFileName).' 2> /dev/null';
            exec($cmd, $details, $retVal);
            if (0 !== $retVal) {
                $this->error("Error while merging <".$file.">. Details: ".implode($details));

                return false;
            }
        }

        $this->comment("Merged <".count($audioFiles)."> audio files.");

        // size of merged product in kB
        $mergedSize     = filesize($combinedFileName);
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
        return $this->fixMp3Length($combinedFileName, $newFileName);
    }

    private function fixMp3Length(string $mergedFile, string $newFileName): bool
    {
        //correcting using ffmpeg
        $this->comment("Correcting mp3 file time using ffmpeg library.");
        $cmd = sprintf('ffmpeg -loglevel quiet -y -i %s -acodec copy %s', escapeshellarg($mergedFile), escapeshellarg($newFileName));

        exec($cmd, $details, $retVal);
        if (0 !== $retVal) {
            $this->error("Error while fixing file time. Details: ".implode($details));die;

            return false;
        }

        return true;
    }
}