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
use audioMan\utils\GarbageCollector;
use audioMan\utils\Messenger;
use audioMan\utils\SkipCollector;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Converter extends Messenger implements FileTypeInterface
{
    //config conversion
    private const _AUDIO_SAMPLING_FREQUENCY =  "44100"; //audio sampling frequency
    private const _AUDIO_BIT_RATE           =  "192";  //audio bit rate

    /**
     * Converts other audio files to mp3 files. Expects full path of files to convert and the output path.
     */
    final public function convert(array $files, string $pathToMove): array
    {
        $converted= [];
        if (empty($files)) {
            return $converted;
        }

        $noFiles = count($files);
        $msg = "Found <".$noFiles."> audio files which are not mp3. Start to convert...";
        $this->comment($msg);

        foreach ($files as $fileToConvert) {

            if (!file_exists($fileToConvert)) {
                $this->error("File not found <".$fileToConvert.">. Skipping whole episode!");
                SkipCollector::add($fileToConvert, SkipCollector::TYPE_EPISODE);
                return [];
            };

            $fileName = pathinfo($fileToConvert, PATHINFO_FILENAME);
            $newFile  = $pathToMove.Registry::get(Registry::KEY_PATH_SEPARATOR).$fileName.self::DEFAULT_EXT;

            //sampling
            if ($this->sampling($fileToConvert, $newFile)) {
                //remember for merging
                $converted[] = $newFile;
            } else {
                $this->error("Failed to convert <".$fileName.">. Skipping whole episode!");
                SkipCollector::add($fileToConvert, SkipCollector::TYPE_EPISODE);
                return [];
            }
        }
        $this->success("Audio files are converted to mp3.");

        return $converted;
    }

    /**
     * - c:v copies meta infos including album art
     * - ar audio sampling frequency
     * - ac number of audio channels (2: stereo)
     * - b:a audio bitrate
     */
    private function sampling(string $input, string $output): bool
    {
        //conversion by using ffmpeg.
        $cmd = "ffmpeg -loglevel quiet -y -i ".escapeshellarg($input)." -c:v copy -ar ".self::_AUDIO_SAMPLING_FREQUENCY." -ac 2 -b:a ".self::_AUDIO_BIT_RATE."k -map_metadata 0:s:0 -id3v2_version 3 -write_id3v1 1 ".escapeshellarg($output);
        exec($cmd, $details, $retVal);
        if (0 !== $retVal) {
            $this->error("Error while converting <".$input."> to <".$output.">. Details: ".$cmd);
            return false;
        }
        //temporary files will be removed
        GarbageCollector::add($output);

        return true;
    }
}