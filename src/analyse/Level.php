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

use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Level
{
    use Messenger;

    private $dirType;

    public function __construct()
    {
        $this->dirType = new LevelDirType();
    }

    final public function check(array $nesting): int
    {
        if (empty($nesting)) {
            throw new \InvalidArgumentException("No audio files found.");
        }

        $lvl = min(array_keys($nesting));

        //Just information
        if ($lvl === max(array_keys($nesting))) {
            $this->warning('Just one level found. If you are not happy with the result consider using option --level | -l.');
        }

        if ($lvl === 0) {
            $this->success('Album and title identified...');

            //actual dir is album level
            return 0;
        }
        if ($lvl > 0) {
            $fileNames = $nesting[$lvl];
            $this->debug("Nesting level is <".$lvl.">");
            $type = $this->dirType->check($fileNames);
            if (LevelDirTypeInterface::TYPE_TITLE === $type) {
                return $lvl;
            };
            if (LevelDirTypeInterface::TYPE_VOLUME === $type) {
                $albumLevel = $lvl-2;
            };

        }

        return $albumLevel;
    }
}