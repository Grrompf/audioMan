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

namespace audioMan\analyse;


use audioMan\interfaces\FileTypeInterface;
use audioMan\model\AudioBookModel;
use audioMan\utils\Tools;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TreeMaker implements FileTypeInterface
{
    /**
     * Tree of the working dir. Sub dir level is the key.
     * Images are skipped.
     */
    final public function makeAlbumTree(AudioBookModel $album): array
    {
        $tree =[];
        foreach ($album->albumFiles as $file) {

            //skip image files
            $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, self::IMAGE_TYPES)) {
                continue;
            }

            $lvl = $this->calcLevel($file, $album->albumPath);
            $tree[$lvl][]=$file;
        }

        return $tree;
    }

    /**
     * Tree of the working dir. Sub dir level is the key.
     * Audio files are skipped.
     */
    final public function makeImageTree(AudioBookModel $album): array
    {
        $tree =[];
        foreach ($album->albumFiles as $file) {

            //skip image files
            $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, self::AUDIO_TYPES)) {
                continue;
            }

            $lvl = $this->calcLevel($file, $album->albumPath);
            $tree[$lvl][]=$file;
        }

        return $tree;
    }

    private function calcLevel(string $fileName, string $rootDir): int
    {
        $filePath = pathinfo($fileName, PATHINFO_DIRNAME);
        return abs(Tools::getNestLevel($rootDir) - Tools::getNestLevel($filePath));
    }
}