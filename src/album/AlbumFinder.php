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

use audioMan\model\AlbumModel;
use audioMan\Registry;
use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AlbumFinder extends Messenger
{
    /**
     * Scans all sub dirs starting from actual. Find audio file level. 
     */
    final public function find(string $actualPath): void
    {
        $iterator = new \DirectoryIterator($actualPath);
        $noSubDir = 0;
        $noAudioFile = 0;

        $level = count(explode('/', $actualPath)) - count(explode('/', Registry::get(Registry::KEY_ROOT_DIR)));

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isDir()) {
                $noSubDir++;
              //  echo $fileInfo->getRealPath()." ($noSubDir)".PHP_EOL;
                $this->find($fileInfo->getRealPath());

                $this->warning($fileInfo->getFilename());
            }
            if ($fileInfo->isFile()) {
                $audioFiles = ['mp3', 'wma'];
                $extension = strtolower($fileInfo->getExtension());
                if (in_array($extension, $audioFiles)) {
                    $noAudioFile++;
                }
            }
        }


        //LOGIC
        $album = new AlbumModel($actualPath, $noSubDir, $noAudioFile);

        // parentDir (for rootDir parentDir is null)
        $parentDir = null;
        if (Registry::get(Registry::KEY_ROOT_DIR) !== $actualPath ) {
            $parentDir = dirname($actualPath, 1);
        }

        //SONDERFALL
        //ignore dir if no subDirs and no audioFiles
        if ($noSubDir === 0 && $noAudioFile === 0) {
            //todo: ignore
            //todo: get parent Dir to remove dir as subDir
            //todo: reevaluate parent Dir with less subDirs
        }

        //AUDIO FILE DIR
        if ($noSubDir === 0 && $noAudioFile > 0) {
            //isAudioFileDir
            //todo: get parent Dir to remove dir as subDir
            //todo: reevaluate parent Dir with less subDirs
        }

        $test = ['path' => $actualPath, 'level' => $level, 'parentDir' => $level-1, 'noSubDir' => $noSubDir, 'noAudioFile' => $noAudioFile];
        AlbumTree::add($test);
    }

}