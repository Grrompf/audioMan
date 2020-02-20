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

namespace audioMan\episode;


use audioMan\analyse\level\Volume;
use audioMan\analyse\TreeMaker;
use audioMan\interfaces\FileTypeInterface;
use audioMan\model\AudioBookModel;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class EpisodeFinder implements FileTypeInterface
{
    private $treeMaker;
    private $assignment;

    public function __construct()
    {
        $this->treeMaker  = new TreeMaker();
        $this->assignment = new EpisodeAssignment();
    }

    final public function assign(AudioBookModel $album): void
    {
        //todo: complete other cases
        //todo: origin path (deeper nested album) or by album correction
        $tree = $this->treeMaker->makeAlbumTree($album);
        if (empty($tree)) {
            //todo: look for Radio Krimis NEU !!!
            return;
        }
        $maxLevel = max(array_keys($tree));

        //todo: what if there are also files on deeper levels
        if ($maxLevel === 0) {
            //files on album root
            $files = $tree[$maxLevel];
            $album->episodes = $this->assignment->assign($files);
        } else {
            //check for volumes
            $names = [];
            foreach($tree[$maxLevel] as $path) {
                $names[] = basename(dirname($path));
            }
            //var_dump((new Volume())->check($names));
        }
    }
}