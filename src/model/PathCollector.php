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

namespace audiMan\model;


use audioMan\model\PathModel;
use audioMan\utils\Tools;

class PathCollector
{
    private $rootPath;

    /** @var PathModel[] */
    private $arModel;
    private $maxLevel=0;

    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * Adds path and level
     */
    final public function add(string $path): void
    {
        $level = Tools::getLevel($path, $this->rootPath);
        if ($level > $this->maxLevel) {
            $this->maxLevel = $level;
        }

        $model = new PathModel($level, $path);

        //key will make the array unique
        $key = md5($path);
        $this->arModel[$key] = $model;
    }

    final public function findByLevel(int $level): array
    {
        $found = [];
        if ($level > $this->maxLevel) {
            return $found;
        }
        foreach ($this->arModel as $model) {
            if ($model->level === $level) {
                $found[] =  $model->subDir;
            }
        }

        return $found;
    }

    final public function getMaxLevel(): int
    {
        return $this->maxLevel;
    }
}