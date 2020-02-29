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


use audioMan\episode\helper\NormalizeHelper;
use audioMan\interfaces\FileTypeInterface;
use audioMan\model\AudioBookModel;
use audioMan\model\EpisodeModel;
use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class VolumeHelper extends Messenger implements AlbumHelperInterface, FileTypeInterface
{
    private CONST _VOLUME_PATTERN = '#([0-9]+)$#';
    private $normalizer;

    public function __construct(NormalizeHelper $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * Fixes the title on episodes like CD1, CD2 etc.
     * New title is a combination of parent dir name and number.
     */
    public function operate(AudioBookModel $album): void
    {
        //just one episode
        if (count($album->episodes) <= 1) {
            return;
        }

        //no volumes
        if (empty($fragments = $this->findVolumeFragments($album))) {
            $this->chat("No volumes found in album <".$album->albumTitle.">.");
            return;
        }

        //array of titles
        $volumeTitles = $this->findVolumeTitles($fragments, $album);

        $number = 0;
        foreach($album->episodes as $episode) {
            assert($episode instanceof EpisodeModel);

            //only volume titles
            if (!in_array($episode->title, $volumeTitles)) {
                continue;
            }

            $title = basename(dirname($episode->path));

            //get appending number
            $number++;
            if (false !== preg_match(self::_VOLUME_PATTERN, $episode->title, $matches)) {
                $number = $matches[1];
            }

            //new title
            $episode->title = $title." ".$number;
            $episode->normalizedFileName = $this->normalizer->process($episode->title).self::DEFAULT_EXT;

            $this->chat("Set new episode volume title <".$episode->title.">");
        }
    }

    /**
     * After removing appending numbers, we look for duplicates
     */
    private function findVolumeFragments(AudioBookModel $album): array
    {
        $noNumberTitle=[];
        foreach($album->getAllTitles() as $title) {

            //remove appending number
            $edited = preg_replace(self::_VOLUME_PATTERN, '', trim($title));
            $noNumberTitle[] = strtolower($edited);
        }

        //key becomes value, value is amount
        $countedValues  = array_count_values($noNumberTitle);

        //callback makes an filtered array of doubles
        $filtered = array_filter($countedValues, function($value) { return $value > 1; } );

        //value is now fragment to search for
        $volumeFragments = array_flip($filtered);

        return $volumeFragments;
    }

    /**
     * Find the complete title by volume fragments.
     */
    private function findVolumeTitles(array $volumeFragments, AudioBookModel $album): array
    {
        $found=[];
        foreach ($volumeFragments as $count => $fragment) {
            $pattern = sprintf('#^%s[0-9]+$#i', $fragment);
            $titles = [];
            foreach($album->getAllTitles() as $title) {
                if (1 === preg_match($pattern, $title)) {
                    $titles[] = $title;
                }
            }

            //proof if matched is the same as counted
            if (count($titles) !== $count) {
                $this->error("Volume count is not as expected. Album <".$album->albumTitle.">.");
            }
            $found = array_merge($found, $titles);
        }
        $this->chat("Found <".count($found)."> volumes in album <".$album->albumTitle.">.");

        return $found;
    }
}