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

namespace audioMan\volume;

use audioMan\AbstractBase;
use audioMan\mp3\Mp3AlbumCover;
use audioMan\utils\CoverFinder;
use audioMan\utils\Scanner;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class VolumeProcessing extends AbstractBase
{
    private $coverFinder;
    private $volumeTitle;

    public function __construct(Scanner $scanner)
    {
        parent::__construct($scanner);
        $this->coverFinder = new CoverFinder();
        $this->volumeTitle = new VolumeTitle();

    }

    /**
     * find volume files, cover and title. Moves files found to root dir.
     */
    final public function handle(): void
    {
        //no mp3 files found? => break processing
        if (false === $files = $this->getScanner()->scanFiles('mp3')) {
            return;
        }

        //actual directory
        $title = basename(getcwd());

        //cover finder
        $cover = $this->coverFinder->find();
        foreach ($files as $volumeFile) {
            //tag cover
            if ($cover) {
                (new Mp3AlbumCover())->import($cover, $volumeFile);
            }
            //compose volume title
            $volumeTitle = $this->volumeTitle->findTitle($volumeFile, $title);
            $rootDir = $this->getScanner()->getRootDir();

            //move file to root dir
            $move  = $rootDir.'/'.$volumeTitle;
            $moveCmd = 'mv '.escapeshellarg($volumeFile)." ".escapeshellarg($move);
            exec($moveCmd, $output, $retVal);
            if (0 !== $retVal) {
                $this->error("Error while moving <".$volumeFile."> in <".basename(getcwd()).">".PHP_EOL."Details: ".$output);
                $msg = PHP_EOL."Exit".PHP_EOL;
                die($msg);
            } else {
                $this->info("Moving <".$volumeFile."> to root directory <".basename($rootDir).">.".PHP_EOL."New file name is <".$volumeTitle.">");
            }

        }
    }


}