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
use audioMan\model\AudioBookModel;
use audioMan\model\EpisodeModel;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class CoverHelper
{
    public function assignCovers(AudioBookModel $album)
    {
        if (empty($album->albumImages)) {
            return;
        }

        //assign covers to episodes
        foreach ($album->episodes as $episode) {
           $this->assignEpisodeCover($episode, $album->albumImages, $album->albumPath);
        }
    }


    private function assignEpisodeCover(EpisodeModel $episode, array $albumImages, string $albumPath): void
    {
        //img in episode dir or below
        $episodeCovers = [];
        $albumCovers   = [];
        foreach ($albumImages as $fileName) {
            if (false !== strpos($fileName, $episode->title)) {
                $episodeCovers[] = $fileName;
            }
            if ($albumPath === pathinfo($fileName, PATHINFO_DIRNAME)) {
                $albumCovers[] = $fileName;
            }
        }
        //episode level first
        if (!empty($episodeCovers)) {
            $episode->cover = $this->findBestMatch($episodeCovers);
        }
        //album level last
        if (!empty($albumCovers)) {
            $episode->cover =  $this->findBestMatch($albumCovers);
        }
    }

    /**
     * Find best match of multiple files by file name.
     */
    private function findBestMatch(array $files): string
    {
        foreach ($files as $file) {

            $filename = pathinfo($file, PATHINFO_FILENAME);
            //best match
            if (stripos($filename, 'cover') !== false) {
                return $file;
            }
            //second best
            if (stripos($filename, 'folder') !== false) {
                return $file;
            }
            //third best
            if (stripos($filename, 'front') !== false) {
                return $file;
            }
        }

        //return random image
        $max = count($files)-1;
        $key = rand(0, $max);

        return $files[$key];
    }
}