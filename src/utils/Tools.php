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

namespace audioMan\utils;

use audioMan\Registry;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Tools
{
    /**
     * Get level depth of the sub directory compared to the root directory
     */
    public static function getLevel(string $path, string $rootPath): int
    {
        $subDirs  = explode('/', $path);
        $rootDirs = explode('/', $rootPath);

        return count($subDirs) - count($rootDirs);
    }

    /**
     * Get file size in MB
     */
    public static function getMB(int $size): float
    {
        return round($size/1000000, 1);
    }

    /**
     * Create directory recursive
     */
    public static function createDir(string $path): void
    {
        if ( !file_exists($path) && !is_dir($path) ) {
            if (!mkdir( $path, 0755, true )) {
                die(PHP_EOL."Cannot create output dir <".$path.">".PHP_EOL."Check your rights!".PHP_EOL."Exit".PHP_EOL);
            }
        }
    }

    /**
     * Create directory recursive
     */
    public static function getNestLevel(string $filePath): int
    {
        return substr_count(realpath($filePath),Registry::get(Registry::KEY_PATH_SEPARATOR));
    }
}