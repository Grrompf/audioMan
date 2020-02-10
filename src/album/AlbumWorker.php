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

namespace audioMan\album;

use audioMan\AbstractBase;
use audioMan\mp3\Mp3Formatter;
use audioMan\mp3\Mp3Normalizer;
use audioMan\mp3\Mp3Processor;
use audioMan\mp3\Mp3TagWriter;
use audioMan\Registry;
use audioMan\utils\SubDirFinder;
use audioMan\utils\TmpCleaner;
use audioMan\volume\VolumeProcessing;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AlbumWorker extends AbstractBase
{
    private $actualPath;

    public function __construct(string $actualPath)
    {
        parent::__construct();
        $this->actualPath = $actualPath;
    }

    final public function handle(): void
    {
        $msg = "Start assembling audio files!".PHP_EOL."Investigating <".basename(getcwd()).">";
        $this->comment($msg);

        $finder = new SubDirFinder();
        $pathCollection = $finder->find($this->actualPath);
        $isVolumeSuitable = $pathCollection->isVolumeSuitable();
        $processor = new Mp3Processor();

        //processing files bringing them to root level
        for ($i=$pathCollection->getMaxLevel(); $i>0; $i--) {
            $subDirs = $pathCollection->findByLevel($i);
            $isVolumeLevel = $pathCollection->isVolumeLevel($i);
            foreach($subDirs as $path) {
                chdir($path);
                if (Registry::get(Registry::KEY_VOLUMES) && $isVolumeLevel && $isVolumeSuitable) {
                    (new VolumeProcessing())->handle();
                    continue;
                }
                $processor->handle();
            }
        }

        //option output
        if (Registry::get(Registry::KEY_OUTPUT)) {
            $isCopy = Registry::get(Registry::KEY_COPY);
            //todo: move or copy
            //todo: get path to working dir
        }

        //renaming files
        $formatter = new Mp3Formatter($path);
        $formatter->handle();

        //tagging
        $tagger = new Mp3TagWriter($path);
        $tagger->handle();

        //normalizing by default (OPTION)
        if (Registry::get(Registry::KEY_NORMALIZE)) {
            $normalizer = new Mp3Normalizer($path);
            $normalizer->handle();
        }

        //remove tmp files
        TmpCleaner::clean();

        $this->success("Finished <".basename($this->actualPath).">");
        $this->break();
    }
}