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

use audioMan\album\AlbumComposer;
use audioMan\album\AlbumProcessor;
use audioMan\registry\Registry;
use audioMan\utils\Messenger;
use audioMan\utils\Tools;


/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 *
* episodes: audio files and probably img or img dir ; dirname is episode name (title)
 * chapters: audio files; dirname is eventually "chapter No" or "cd No"; many dirs ; parent dir name is eventually book name (title).
 * volumes:  contain chapters and eventually an img or img dir; dir name is title.
 * album or book: may contain any of the above (ven probably nested) and eventually imgDir or img file; dir name is album
 * collection: many albums or books; dir name is collection name and may be nested in parent dirs; keep path for moving files
 *
* starting point could be album or nested collection; keep directory structure for moving files
 * audio files: mp3, wma, wav, ogg, acc ac3
 * files size > 0
 * img files (cover): jpg, jpeg, png. imgSize and square dimension are important. Eventual names: cover, folder, front
 *
* album level (up to 2 nesting level):
  * - contains audio files-> dir name is title and album
  * - contains episodes in sub dirs -> sub dir name is complex and is title, parent dir is album
  * - contains chapters in sub dirs -> sub dir name is simple (CD, Chapter), parent dir is title and album
  * - contains volumes. volumes contain chapters. dir name is title, parent dir is album
 *
* collection level (up to 3 nesting level)
  * - contains album(s)
 */
class Checker
{
    use Messenger;

    final public function check(string $actualPath): array
    {
        $this->info("Check for audio and cover files in <".$actualPath."> ...");
        $actualPath = getCwd();

        //scan
        $files = (new Scanner($actualPath))->scan();

        //exit condition: no files
        if (empty($files) === 0) {
            $msg = "No files, no work! Try another directory...";
            $this->warning($msg);
            exit(PHP_EOL.'EXIT'.PHP_EOL);
        }

        $this->info("Determine directory nesting ...");
        //nesting
        $nesting = (new Nesting($actualPath))->arrange($files);

        //album level
        if (null !== $albumLevel = Registry::get(Registry::KEY_LEVEL)) {
            $this->info("Album level is forced to <".$albumLevel.">");
        } else {
            $albumLevel = (new Level())->check($nesting);
            $this->info("Evaluated album level is <".$albumLevel.">");
        }

        //albums
        $albums = (new AlbumComposer($files, $actualPath))->bind($albumLevel);
        $this->info("Number of albums <".count($albums)."> found.");

        return $albums;


    }
}