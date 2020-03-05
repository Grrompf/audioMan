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

namespace audioMan\album\helper;

use audioMan\model\AudioBookModel;
use audioMan\model\EpisodeModel;
use audioMan\utils\ImgCheck;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class CoverHelper implements AlbumHelperInterface
{
    //LOWER CASE ONLY!
    private const _COVER_NAMING = ['cover', 'folder', 'front'];

    public function operate(AudioBookModel $album): void
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
            $imgDir = pathinfo($fileName, PATHINFO_DIRNAME);
            if (false !== strpos($imgDir, $episode->path)) {
                $episodeCovers[] = $fileName;
            }
            if (false !== strpos($fileName, $episode->title)) {
                $episodeCovers[] = $fileName;
            }
            if ($albumPath === pathinfo($fileName, PATHINFO_DIRNAME)) {
                $albumCovers[] = $fileName;
            }
            //finds images in parent dir
            if (false !== strpos($episode->path, $imgDir)) {
                $albumCovers[] = $fileName;
            }
            //image path contains episode path
        }

        //episode level first
        if (!empty($episodeCovers)) {
            $episode->cover = $this->findBestMatch($episodeCovers);
        }
        //album level last
        if (!empty($albumCovers) && empty($episode->cover)) {
            $episode->cover =  $this->findBestMatch($albumCovers);
        }
    }

    /**
     * Find best match of multiple files by file name.
     * On first run large images and images with no square dimension are ignored to get the best suited image.
     */
    private function findBestMatch(array $files): string
    {
        if (count($files) === 1) {
            return array_shift($files);
        }

        $secondTry=[];
        foreach ($files as $file) {

            //skip over sized images
            if (round(filesize($file) / 1000, 1) > ImgCheck::MAX_FILE_SIZE) {
                $secondTry[] = $file;
                continue;
            }

            //skip over no square images
            if (!ImgCheck::hasSquareDimension($file)) {
                $secondTry[] = $file;
                continue;
            }

            //matching naming convention
            if ($this->isNamingMatch($file)) {
                return $file;
            }

            $secondTry[] = $file;
        }

        //better one than no cover
        if (count($secondTry) === 1) {
            return array_shift($secondTry);
        }

        //second run on naming convention
       foreach ($secondTry as $file) {
            //matching naming convention
            if ($this->isNamingMatch($file)) {
                return $file;
            }
        }

        //return random image
        $max = count($secondTry)-1;
        $key = rand(0, $max);

        return $files[$key];
    }

    private function isNamingMatch(string $file): bool
    {
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $filename = strtolower($filename);

        return in_array($filename, self::_COVER_NAMING);
    }
}