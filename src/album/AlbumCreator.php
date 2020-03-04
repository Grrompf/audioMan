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

use audioMan\album\helper\HelperFactory;
use audioMan\episode\EpisodeComposer;
use audioMan\model\AudioBookModel;
use audioMan\registry\Registry;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AlbumCreator
{
    private $episodeComposer;

    public function __construct()
    {
        $this->episodeComposer = new EpisodeComposer();
    }

    public function create(array &$allFiles, string $albumPath): AudioBookModel
    {
        //extract all album files: audio and images
        $albumFiles = $this->extractAlbumFiles($allFiles, $albumPath);

        //create album model
        $album = new AudioBookModel($albumPath, $albumFiles);

        //add album images
        HelperFactory::get(HelperFactory::IMAGE_HELPER)->operate($album);

        //add episodes
        $this->episodeComposer->bind($album);

        //add covers
        HelperFactory::get(HelperFactory::COVER_HELPER)->operate($album);

        //fix volume titles
        HelperFactory::get(HelperFactory::VOLUME_HELPER)->operate($album);

        //get sorting dir
        HelperFactory::get(HelperFactory::SORTING_HELPER)->operate($album);

        return $album;
    }

    /**
     * Extracts all files of an album. Removes files found from total files (performance).
     */
    private function extractAlbumFiles(array &$allFiles, string $albumPath): array
    {
        //add slash to distinguish albums with similar path, eg. myAlbum and myAlbumNew
        $needle = $albumPath.Registry::get(Registry::KEY_PATH_SEPARATOR);
        $albumFiles=[];
        foreach ($allFiles as $file) {
            //file path contain album path
            if (false !== strpos($file, $needle)) {
                $albumFiles[] = $file;
            }
        }
        //remove files found from origin (performance)
        $allFiles = array_diff($allFiles, $albumFiles);

        return $albumFiles;
    }
}