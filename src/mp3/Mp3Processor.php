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



use audioMan\utils\CoverFinder;
use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Mp3Processor extends Messenger
{
    private $converter;
    private $joiner;
    private $mover;
    private $coverFinder;

    public function __construct()
    {
        parent::__construct();
        $this->converter = new Converter();
        $this->coverFinder = new CoverFinder();
        $this->joiner = new Joiner();
        $this->mover = new Mover();
    }

    /**
     * Join multiple mp3 files to a single file, correcting time length and moves the processed mp3 file to parent dir.
     */
    final public function handle(): void
    {
        //convert wma or other audio format files
        $this->converter->handle();

        //no mp3 files found? => break processing
        if (false === $files = $this->getScanner()->scanFiles('mp3')) {
            return;
        }

        //merge files
        $this->joiner->join($files);

        //cover finder IMPORTANT: after join but before moving
        $cover = $this->coverFinder->find();
        if ($cover) {
            (new Mp3AlbumCover())->import($cover);
        }

        //move merged file
        $this->mover->move();


    }
}