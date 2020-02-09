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

use audioMan\album\AlbumWorker;
use audioMan\model\AlbumTree;;
use audioMan\utils\AlbumFinder;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Main extends AbstractBase
{
    // todo: find album root
    // todo: option for format
    // todo: manifest for update

    final public function handle(): void
    {
        $msg = "Start assembling audio files!".PHP_EOL."Investigating <".basename(getcwd()).">";
        $this->comment($msg);

        $actualPath = getCwd();
        //starting dir is root dir
        Registry::set(Registry::KEY_ROOT_DIR, $actualPath);

        //path of the processed album
        Registry::set(Registry::KEY_LIB_DIR, $actualPath);

        $worker = new AlbumWorker($actualPath);
        $worker->handle();
//        (new AlbumFinder())->find($actualPath);
//        var_dump(array_keys(AlbumTree::getAll()));
//        return;
    }
}