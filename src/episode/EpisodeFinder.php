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
    private $creator;

    public function __construct()
    {
        $this->treeMaker  = new TreeMaker();
        $this->creator    = new EpisodeCreator();
    }

    final public function assign(AudioBookModel $album): void
    {
        //todo: complete other cases
        //todo: origin path (deeper nested album) or by album correction
        $tree = $this->treeMaker->makeAlbumTree($album);
        if (empty($tree)) {
            //todo: look for Radio Krimis NEU !!!
            var_dump('EMPTY TREE');
            return;
        }
        $maxLevel = max(array_keys($tree));
        $minLevel = min(array_keys($tree));

        //todo: what if there are also files on deeper levels
        if (array_key_exists(0, $tree)) {
            //files on album root
            $files = $tree[0];

            //test if volumes
            if ((new VolumeChecker())->isVolume($files)) {
                $title = basename(pathinfo($files[0], PATHINFO_DIRNAME));
                $album->episodes[] = $this->creator->create($title, $files);
            } else {
                //episodes are filenames
                foreach ($files as $file) {
                    $title = pathinfo($file, PATHINFO_FILENAME);
                    $album->episodes[] = $this->creator->create($title, [$file]);
                }
            }
        }
        if (array_key_exists(1, $tree)) {
            //files on next album level
            $files = $tree[1];

            //get episode titles
            $albumEpisodes=[];
            foreach ($files as $file) {
                $pathToEpisode = pathinfo($file, PATHINFO_DIRNAME);
                $originalTitle = basename($pathToEpisode);
                $albumEpisodes[$originalTitle][]=$file;
            }
            foreach ($albumEpisodes as $originalTitle => $files) {
                $album->episodes[] = $this->creator->create($originalTitle, $files);
            }
        }
        if (array_key_exists(2, $tree)) {
            //files on next album level
            $files = $tree[2];

            //get episode titles
            $albumEpisodes=[];
            foreach ($files as $file) {
                $pathToEpisode = pathinfo($file, PATHINFO_DIRNAME);
                $originalTitle = basename($pathToEpisode);
                $albumEpisodes[$originalTitle][]=$file;
            }
            foreach ($albumEpisodes as $originalTitle => $files) {
                $album->episodes[] = $this->creator->create($originalTitle, $files);
            }
        }


        else {
            //check for volumes
            $names = [];
            foreach($tree[$maxLevel] as $path) {
                $names[] = basename(dirname($path));
            }
            //var_dump((new Volume())->check($names));
        }
    }
}