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

use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TagWriter
{
    use Messenger;

    /**
     * Rewriting title and album of the mp3 file
     */
    final public function write(string $fileName, string $album, string $title, string $cover=null): bool
    {
        if (false === file_exists($fileName)) {
            $this->error("Expected file <".$fileName."> not found.");

            return false;
        }

        //fix write permission
        chmod($fileName, 0644);

        $this->comment("Write tag to <".$fileName.">");

        //cover
        $coverCmd='';
        if ($cover && !$this->hasCover($fileName)) {
            //import cmd
            $coverCmd =sprintf("-p %s", escapeshellarg($cover));
        }

        //mid3v2 command
        $cmd = sprintf("mid3v2 -q -t %s -A %s -g Other %s %s",
            escapeshellarg($title),
            escapeshellarg($album),
            $coverCmd,
            escapeshellarg($fileName)
        );

        //mid3v2
        exec($cmd, $details, $retVal);
        if (0 !== $retVal) {
            $this->error("Error while writing tags to <".$fileName.">. Details: ".implode($details));

            return false;
        }
        //success
        $this->comment("Tag written to <".$fileName. ">.");

        return true;
    }

    private function hasCover(string $fileName): bool
    {
        //list tags cmd (tags are listed as an array)
        $cmd =sprintf("mid3v2 -l %s",  escapeshellarg($fileName));

        //mid3v2
        exec($cmd, $details, $retVal);
        if (0 !== $retVal) {
            $this->error("Error while listing all tags of <".$fileName.">. Details: ".implode($details));

            return false;
        }

        //album cover found?
        if ($coverFound = strpos(implode($details),"APIC") > 0) {
            $this->comment("Album cover found.");
        }

        return $coverFound;
    }
}