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

use audioMan\interfaces\FileTypeInterface;
use audioMan\model\AudioBookModel;
use audioMan\model\EpisodeModel;
use audioMan\mp3\Converter;
use audioMan\mp3\Joiner;
use audioMan\mp3\Mover;
use audioMan\mp3\TagWriter;
use audioMan\Registry;
use audioMan\utils\GarbageCollector;
use audioMan\utils\Messenger;
use audioMan\utils\Tools;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AlbumProcessor extends Messenger implements FileTypeInterface
{
    private $converter;
    private $joiner;
    private $mover;
    private $tagWriter;

    public function __construct()
    {
        $this->converter = new Converter();
        $this->joiner    = new Joiner();
        $this->mover     = new Mover();
        $this->tagWriter = new TagWriter();
    }

    /**
     * Join multiple mp3 files to a single file, correcting time length and moves the processed mp3 file to parent dir.
     */
    final public function process(AudioBookModel $album): void
    {
        $this->comment("Processing album <".$album->albumTitle.">");

        //create album path
        $albumPath = Registry::get(Registry::KEY_OUTPUT).Registry::get(Registry::KEY_PATH_SEPARATOR).$album->albumTitle;
        Tools::createDir($albumPath);

        foreach ($album->episodes as $episode) {
            assert($episode instanceof EpisodeModel);
            if ($episode->isSkipped) {
                $msg ="Episode <".$episode->title."> is skipped. Album <".$album->albumTitle.">";
                $this->caution($msg);
            }

            //todo: convert wma or other audio format files
            //merge file name and normalized filename
            $combinedFile = $albumPath.Registry::get(Registry::KEY_PATH_SEPARATOR).self::CONCAT_FILE_NAME;
            $newFileName  = $albumPath.Registry::get(Registry::KEY_PATH_SEPARATOR).$episode->normalizedFileName;
            $filesToMerge = $episode->audioFiles;

            //convert files
            //$filesToMerge = $this->converter->convert($filesToMerge, $albumPath);

            //merge and tagging
            if (count($filesToMerge) > 1) {
                $this->joiner->merge($filesToMerge, $combinedFile, $newFileName);
            }
            //just copy
            if (count($filesToMerge) === 1) {
                $file = array_shift($filesToMerge);
                $this->mover->copy($file, $newFileName);
            }

            //tagging
            $this->tagWriter->write($newFileName, $album->albumTitle, $episode->title, $episode->cover);

            //remove temp files
            GarbageCollector::clean();
        }
    }
}