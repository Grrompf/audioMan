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

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Mp3Converter extends AbstractBase
{
    /**
     * Converts other audio files to mp3 files. Works for wma.
     */
    final public function handle(): void
    {
        $types = ['wma'];
        foreach ($types as $type) {
            if (false !== $files = $this->getScanner()->scanFiles($type)) {
                $noFiles = count($files);
                $msg = "<".$noFiles." ".$type." files> found in <".getcwd().">!".PHP_EOL."Start to convert...";
                $this->warning($msg);

                $this->convert($files, $type);
            }
        }
    }

    private function convert(array $files, string $type): bool
    {
        if (empty($files)) {
            return false;
        }
        $this->comment("Convert <".count($files)." ".$type." files> to mp3.");

        $cmd = 'find '.getcwd()."/ -iname \*.".$type." -exec ffmpeg -i {} -ab 160k {}.mp3 \; -exec rename 's/\.".$type."\.mp3$/.mp3/' {}.mp3 \\";
        exec($cmd, $output, $retVal);
        if (0 !== $retVal) {
            $this->error("Error while converting ".$type." to mp3 in <".getcwd().">".PHP_EOL."Details: ".$output);
            $msg = PHP_EOL."Exit".PHP_EOL;
            die($msg);
        }

        return true;
    }
}