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

use audioMan\interfaces\FileTypeInterface;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Filter implements FileTypeInterface
{
    private $allowedTypes;
    private $dirIterator;

    public function __construct(\RecursiveDirectoryIterator $dirIterator)
    {
        $this->dirIterator = $dirIterator;

        // all allowed audio and image types
        $this->allowedTypes = array_merge(self::AUDIO_TYPES, self::IMAGE_TYPES);
    }

    /**
     * Filter for audio and image files
     */
    final public function filter(): \RecursiveCallbackFilterIterator
    {
        //filter
        return new \RecursiveCallbackFilterIterator(
            //callback is not documented yet. All params are of mixed type
            $this->dirIterator, function ($current, $key, $iterator) {

                // Allow recursion
                assert($iterator instanceof \RecursiveDirectoryIterator);
                if ($iterator->hasChildren()) {
                    return true;
                }

                assert($current instanceof \SplFileInfo);
                // skip hidden files
                if ($current->getBasename()[0] === '.') {
                    return false;
                }

                // Check for audio and cover files
                if ($current->isFile() && in_array(strtolower($current->getExtension()), $this->allowedTypes)) {
                    return true;
                }

                return false;
            }
        );
    }
}