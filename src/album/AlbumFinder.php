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

use audioMan\interfaces\AudioTypeInterface;
use audioMan\model\AudioBookModel;
use audioMan\Registry;
use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AlbumFinder extends Messenger implements AudioTypeInterface
{
    /**
     * Scans all sub dirs starting from actual. Find audio file level. 
     */
    final public function find(string $actualPath): ?AlbumTree
    {
        $noSubDir = 0;
        $noAudioFile = 0;
        $level = $this->calcLevel($actualPath);
        $albumTree = new AlbumTree();

        $iterator = new \DirectoryIterator($actualPath);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isDir()) {
                $noSubDir++;
                $this->find($fileInfo->getRealPath());
            }
            if ($fileInfo->isFile()) {
                $extension = strtolower($fileInfo->getExtension());
                if (in_array($extension, self::AUDIO_TYPES) || $extension === 'mp3') {
                    $noAudioFile++;
                }
            }
        }

        //LOGIC
       //ignore dir if no subDirs and no audioFiles
        if ($noSubDir === 0 && $noAudioFile === 0) {
            return null;
        }

        //AUDIO FILE DIR
        if ($noSubDir === 0 && $noAudioFile > 0) {
            //isAudioFileDir
            $album = new AudioBookModel($actualPath, $noSubDir, $noAudioFile, $level);
            $albumTree->add($album);
        }

        return $albumTree;
    }

    private function calcLevel(string $actualPath): int
    {
        $rootLevel =  count(explode('/', Registry::get(Registry::KEY_ROOT_DIR)));
        $actualLevel =  count(explode('/', $actualPath));

        return $actualLevel - $rootLevel;
    }


}