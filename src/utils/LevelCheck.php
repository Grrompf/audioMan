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

namespace audioMan\util;


use audioMan\album\AlbumFinder;
use audioMan\Registry;
use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class LevelCheck extends Messenger
{
    private $albumFinder;

    public function __construct()
    {
        $this->albumFinder = new AlbumFinder();
    }

    /**
     * Sets copy flag and skip or force merge on deep dir level
     */
    final public function check(string $actualPath): bool
    {
        $tree = $this->albumFinder->find($actualPath);
        if (!$tree) {
            $this->warning("Empty folder found <".basename($actualPath).">. Skipping...");
            return false;
        }

        //set copy flag if files on album level
        $copy = $tree->getMinLevel() <= 1;
        $this->debug("Min level is <".$tree->getMinLevel().">");
        $this->info("Files in <".basename($actualPath)."> are ".($copy?'COPIED':'MOVED'));
        Registry::set(Registry::KEY_COPY, $copy);

        $maxLevel = $tree ? $tree->getMaxLevel() : 0;

        if (Registry::get(Registry::KEY_FORCE)) {
            $this->warning('File merge is forced!');
            return true;
        }

        $errorMsg = 'Path <'.$actualPath.'> is not suited for correct joining audio book files.';
        if (Registry::get(Registry::KEY_MULTIPLE) && $maxLevel > 3) {
            $this->error($errorMsg);
            return false;
        }

        if ($maxLevel > 2) {
            $this->error($errorMsg);
            return false;
        }

        return true;
    }
}