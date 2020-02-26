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


use audioMan\interfaces\FileTypeInterface;
use audioMan\model\AudioBookModel;
use audioMan\model\EpisodeModel;

class VolumeFixer implements FileTypeInterface
{
    private CONST _VOLUME_PATTERN = '#([0-9]+)#';
    private $normalizer;

    public function __construct()
    {
        $this->normalizer = new Normalizer();
    }

    /**
     * Fixes the title on episodes like CD1, CD2 etc.
     * New title is a combination of parent dir name and number.
     */
    public function fixTitle(AudioBookModel $album): void
    {
        if (count($album->episodes) <= 1) {
            return;
        }

        if (!$this->isVolume($album)) {
            return;
        }

        $number = 0;
        foreach($album->episodes as $episode) {
            assert($episode instanceof EpisodeModel);
            $title = basename(dirname($episode->path));

            //get appending number
            $number++;
            if (false !== preg_match(self::_VOLUME_PATTERN, $episode->title, $matches)) {
                $number = $matches[1];
            }

            //new title
            $episode->title = $title." ".$number;
            $episode->normalizedFileName = $this->normalizer->normalizeUtf8($episode->title).self::DEFAULT_EXT;
        }
    }

    private function isVolume(AudioBookModel $album): bool
    {
        $volumes=[];
        foreach($album->episodes as $episode) {
            assert($episode instanceof EpisodeModel);

            $title = trim($episode->title);

            //remove appending number
            $edited = preg_replace(self::_VOLUME_PATTERN, '_', $title);

            //just in case
            $edited = strtolower($edited);

            if (!in_array($edited, $volumes)) {
                $volumes[] = $edited;
            }
        }

        //no volumes
        return count($volumes) === 1;
    }
}