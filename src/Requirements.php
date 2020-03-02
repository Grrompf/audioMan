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

namespace audioMan;

use audioMan\utils\Messenger;

class Requirements
{
    use Messenger;
    private $isChecked = true;

    final public function check(): void
    {
        exec('ffmpeg -version', $output, $retValue);
        if (0 !== $retValue) {
            $msg = "No ffmpeg found!".PHP_EOL."This library is necessary.".PHP_EOL.PHP_EOL.
                "Use  \"sudo apt-get install ffmpeg\"  to install the tool.".PHP_EOL;

            $this->error($msg);
            $this->isChecked = false;
        }

        exec('mid3v2 --version', $output, $retValue);
        if (0 !== $retValue) {
            $msg = "No mid3v2 found!".PHP_EOL."This library is essential for tag writing.".PHP_EOL.PHP_EOL.
                "Use  \"sudo apt-get install python-mutagen\"  to install the tool.".PHP_EOL;

            $this->error($msg);
            $this->isChecked = false;
        }

        if (false === $this->isChecked) {
            $msg = PHP_EOL."Exit".PHP_EOL;
            die($msg);
        }
    }
}