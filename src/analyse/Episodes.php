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

namespace audioMan\analyse;


use audioMan\analyse\level\Volume;
use audioMan\interfaces\FileTypeInterface;
use audioMan\utils\Tools;

class Episodes implements FileTypeInterface
{
    public function create(string $albumPath, array $albumFiles)
    {
        $tree = $this->makeTree($albumPath, $albumFiles);

        $maxLevel = max(array_keys($tree));
        $episodes=[];
        if ($maxLevel === 0) {
            $files = $tree[$maxLevel];
            foreach ($files as $file) {
                $episodes[] = pathinfo($file, PATHINFO_FILENAME);
            }
        } else {
            //check for volumes
            $names = [];
            foreach($tree[$maxLevel] as $path) {
                $names[] = basename(dirname($path));
            }
            var_dump((new Volume())->check($names));
        }
    }

    private function makeTree(string $albumPath, array $albumFiles): array
    {
        $tree =[];
        foreach ($albumFiles as $file) {
            $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            //skip img
            if (in_array($ext, self::IMAGE_TYPES)) {
                continue;
            }

            $fileDir = pathinfo($file, PATHINFO_DIRNAME);
            $lvl = abs(Tools::getNestLevel($albumPath) - Tools::getNestLevel($fileDir));
            $tree[$lvl][]=$file;
        }

        return $tree;
    }
}