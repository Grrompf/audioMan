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

use audioMan\interfaces\FileTypeInterface;
use audioMan\utils\Messenger;
use audioMan\utils\Tools;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AlbumFinder extends Messenger implements FileTypeInterface
{
    private $files;
    private $rootLevel;
    private $albumCreator;

    public function __construct(array $files, string $actualPath)
    {
        $this->files = $files;
        $this->rootLevel = Tools::getNestLevel($actualPath);
        $this->albumCreator = new AlbumCreator();
    }

    final public function check(int $albumLevel): array
    {
        $this->info("Assemble files for albums ...");
        $totalLevel = $albumLevel + $this->rootLevel;
        $albums=[];

        //elements of this copy will be removed on each loop
        $allFiles = $this->files;

        foreach ($this->files as $file) {

            //get album path
            $albumPath = pathinfo($file, PATHINFO_DIRNAME);
            $level = abs(Tools::getNestLevel($albumPath) - $totalLevel);
            if ($level > 0) {
                $albumPath = dirname($albumPath, $level);
            }

            //skip if album already added
            if (array_key_exists($albumPath, $albums)) {
                continue;
            }
            $this->chat("Working on album <".basename($albumPath).">");

            //create album
            $album = $this->albumCreator->create($allFiles, $albumPath);
            $albums[$albumPath]=$album;
        }

        //this is not expected to happen!
        if (count($allFiles) > 0) {
            $this->warning("Some files <".count($allFiles)."> were not assigned");
        }

        return $albums;
    }
}