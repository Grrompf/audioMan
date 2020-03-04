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

use audioMan\album\AlbumProcessor;
use audioMan\analyse\Checker;
use audioMan\model\AudioBookModel;
use audioMan\registry\Registry;
use audioMan\utils\Messenger;
use audioMan\utils\Tools;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Main
{
    use Messenger;

    //todo: remove leading chars using user input
    //todo: fix check for deeper level
    //todo: manifest for update
    final public function handle(): void
    {
        $actualPath = getCwd();
        $albums = (new Checker())->check($actualPath);
        $processor = new AlbumProcessor();

        sort($albums);
        foreach($albums as $album) {
            assert($album instanceof AudioBookModel);
            if (!Registry::get(Registry::KEY_NO_INTERACTION)) {
                $msg = sprintf("Next album <%s>. Do you want to proceed? Enter y or n (default: y)", $album->albumTitle);
                if ('n' === readline($msg)) {
                    continue;
                };
            }

            $processor->process($album);
        }
    }
}