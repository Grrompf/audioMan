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

use audioMan\model\AlbumModel;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AlbumTree
{
    public $tree = [];

    final public function add(AlbumModel $albumModel): void
    {
        $level = (int) $albumModel->level;
        $this->tree[$level][] = $albumModel;
    }

    /**
     * Determine the depth of directory structure. For a single album,
     * we expect the files on next sub dir (lvl 1) or on volumes on level 2.
     * For multiple, we expect more than one book on root level. Therefore,
     * the level is one higher.
     */
    final public function getMaxLevel(): int
    {
        if (empty($tree)) {
            return 0;
        }
        return max(array_keys($this->tree));
    }

    /**
     * If level is 1, the audio files are directly on album level. Therefore,
     * the files are copied to an optional save dir instead of being moved.
     */
    final public function getMinLevel(): int
    {
        if (empty($tree)) {
            return 0;
        }
        return min(array_keys($this->tree));
    }
}