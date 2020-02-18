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

use audioMan\analyse\AlbumImageFinder;
use audioMan\episode\CoverHelper;
use audioMan\episode\EpisodeFinder;
use audioMan\interfaces\FileTypeInterface;
use audioMan\model\AudioBookModel;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AlbumCreator implements FileTypeInterface
{
    private $episodeFinder;
    private $imageFinder;

    public function __construct()
    {
        $this->episodeFinder = new EpisodeFinder();
        $this->imageFinder   = new AlbumImageFinder();
    }

    public function create(array &$allFiles, string $albumPath): AudioBookModel
    {
        //extract all album files: audio and images
        $albumFiles = $this->extractAlbumFiles($allFiles, $albumPath);

        //create album model
        $album = new AudioBookModel($albumPath, $albumFiles);

        //add album images
        $this->imageFinder->assign($album);

        //add episodes
        $this->episodeFinder->assign($album);

        //add covers
        (new CoverHelper())->assignCovers($album);

        return $album;
    }

    /**
     * Extracts all files of an album. Removes files found from total files (performance).
     */
    private function extractAlbumFiles(array &$allFiles, string $albumPath): array
    {
        $albumFiles=[];
        foreach ($allFiles as $file) {
            //file path contain album path
            if (false !== strpos($file, $albumPath)) {
                $albumFiles[] = $file;
            }
        }
        //remove files found from origin (performance)
        $allFiles = array_diff($allFiles, $albumFiles);

        return $albumFiles;
    }

    private function extractImages(array $allAlbumFiles): array
    {
        $albumImages = [];
        foreach ($allAlbumFiles as $file) {

            //skip audio files
            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
            if (!in_array($fileExtension, self::IMAGE_TYPES)) {
                continue;
            };
            $albumImages[] = $file;
        }

        return $albumImages;
    }
}