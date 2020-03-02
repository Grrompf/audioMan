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
use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class EpisodeComposer extends Messenger implements FileTypeInterface
{
    private $treeMaker;
    private $creator;
    private $volCheck;

    public function __construct()
    {
        $this->treeMaker  = new TreeMaker();
        $this->creator    = new EpisodeCreator();
        $this->volCheck   = new VolumeChecker();
    }

    final public function bind(AudioBookModel $album): void
    {
        $tree = $this->treeMaker->makeAlbumTree($album);
        if (empty($tree)) {
            $this->caution("Empty album <".$album->albumTitle."> found.");
            return;
        }
        //needed for find sorting directory
        $album->maxLevel = max(array_keys($tree));

        foreach ($tree as $nestLevel => $files) {
            if ($nestLevel === 0) {
                $this->assignRootLevelEpisodes($files, $album);
                continue;
            }
            //get episode titles on deeper level
            $this->assignEpisodes($nestLevel, $files, $album);
        }

        $this->info("Found <".count($album->episodes)."> episodes in album <".$album->albumTitle.">.");
    }

    private function assignRootLevelEpisodes(array $files, AudioBookModel $album): void
    {
        //test if volumes
        if ($this->volCheck->isVolume($files)) {
            $title = basename(pathinfo($files[0], PATHINFO_DIRNAME));
            $album->episodes[] = $this->creator->create($title, $files);
            return;
        }

        //episodes are filenames
        foreach ($files as $file) {
            $title = pathinfo($file, PATHINFO_FILENAME);
            $album->episodes[] = $this->creator->create($title, [$file]);
        }
    }

    private function assignEpisodes(int $nestingLevel, array $files, AudioBookModel $album): void
    {
        //filter all episodes
        $albumEpisodes=[];
        foreach ($files as $file) {
            $pathToEpisode = pathinfo($file, PATHINFO_DIRNAME);
            $albumEpisodes[$pathToEpisode][]=$file;
        }
        //assign episodes
        foreach ($albumEpisodes as $pathToEpisode => $files) {
            $originalTitle = basename($pathToEpisode);
            $episode = $this->creator->create($originalTitle, $files);
            $episode->nestLevel = $nestingLevel;

            $album->episodes[] = $episode;
        }

    }
}