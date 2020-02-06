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

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BaseScanner extends Messenger
{
    /**
     * Scan for files in current directory. Result is filtered case insensitively by its type.
     * Exit on error.
     *
     * @return array|bool Return false if no files are found otherwise an array of file paths.
     */
    final protected function search(string $type)
    {
        $files = glob('*');
        if (false === $files) {
            $this->error("Unexpected error in scanning <".getcwd().">");
            $msg = PHP_EOL."Exit".PHP_EOL;
            die($msg);
        }

        $pattern = '#.\.'.$type.'$#i';
        $search = preg_grep($pattern, $files);
        if (0 === count($search)) {
            $msg ="No <".$type."> files found in <".basename(getcwd()).">".PHP_EOL."Skip directory.";
            $this->info($msg);

            return false;
        }

        return $search;
    }
}