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

namespace audioMan\mp3;


use audioMan\utils\GarbageCollector;
use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Mover
{
    use Messenger;

    /**
     * Rename joined mp3 file after correction to normalized.
     */
    final public function copy(string $oldFileName, string $newFileName): bool
    {
        $msg = "Copy file <".$oldFileName."> to <".$newFileName.">.";
        $this->debug($msg);

        //moving not copying!
        $cmd = sprintf("cp %s %s" , escapeshellarg($oldFileName), escapeshellarg($newFileName));
        exec($cmd, $details, $retVal);
        if (0 !== $retVal) {
            $this->error("Error while copying  <".$oldFileName."> to <".$newFileName.">. Details: ".implode($details));
            GarbageCollector::add($newFileName);

            return false;
        }

        return true;
    }
}