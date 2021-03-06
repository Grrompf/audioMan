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
use audioMan\utils\Tools;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class LevelVolume implements LevelDirTypeInterface
{
    use Messenger;

    /**
     * Check names of current dir after removing numbers. If many different, these are titles.
     * If just one, its a volume!
     * Unknown is not expected.
     */
    final public function check(array $dirNames): int
    {
        $this->info("Investigating volumes...");
        $uniqueNames = Tools::assembleDirNamesNoNumber($dirNames);
        if (count($dirNames) === count($uniqueNames)) {
            $this->success("Episodes found.");

            return self::TYPE_TITLE;
        }
        if (1 === count($uniqueNames)) {
            $this->success("Volumes found.");

            return self::TYPE_VOLUME;
        }

        $this->warning("Evaluation ambiguous. Best guess are titles.");
        return self::TYPE_TITLE;
    }
}