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
use audioMan\model\EpisodeModel;
use audioMan\utils\SkipCollector;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class EpisodeCreator implements FileTypeInterface
{
    private $titleMaker;
    private $normalizer;

    public function __construct()
    {
        $this->titleMaker = new TitleMaker();
        $this->normalizer = new Normalizer();
    }

    public function create(string $originalTitle, array $audioFiles): EpisodeModel
    {
        $episode = new EpisodeModel($originalTitle, $audioFiles);

        //files to convert
        $extension = pathinfo($audioFiles[0], PATHINFO_EXTENSION);
        $episode->hasConvertible = in_array($extension, self::CONVERT_TYPES);

        //skip if empty files are found
        $episode->isSkipped = $this->hasEmptyFiles($audioFiles);

        //path to episode
        $episode->path = pathinfo($audioFiles[0], PATHINFO_DIRNAME);

        //reformat title for tagging
        $title = $this->titleMaker->makeTitle($originalTitle);
        $episode->title = $title;

        //normalize file name for poor mp3 players
        $episode->normalizedFileName = $this->normalizer->normalizeUtf8($title).self::DEFAULT_EXT;

        return $episode;
    }

    private function hasEmptyFiles(array $files): bool
    {
        $found = array_intersect(SkipCollector::get(SkipCollector::TYPE_EMPTY_FILE), $files);

        return !empty($found);
    }
}