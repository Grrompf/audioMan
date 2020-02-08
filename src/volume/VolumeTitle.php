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

namespace audioMan\volume;

use audioMan\utils\Messenger;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class VolumeTitle extends Messenger
{
    final public function findTitle(string $fileName, string $title): string
    {
        //volume number
        $pattern = '#\D*(\d+)\D*\.mp3#i';
        if (1 === preg_match($pattern, $fileName, $matches)) {
            $volume = $matches[1];
        } else {
            $this->error("Expected number not found in <".$fileName.">");
            $msg = PHP_EOL."Exit".PHP_EOL;
            die($msg);
        }

        $pattern = '#^(\d+)(.*)$#';
        if (1 === preg_match($pattern, $title, $matches)) {
            //if number found in the beginning insert volume eg 2-1 myFile.mp3
            $newTitle = $matches[1].'-'.$volume.$matches[2];
            $this->info("Volume number inserted in <".$title.">");
        } else {
            //append volume
            $newTitle = $title.' '.$volume;
            $this->info("Volume number appended to <".$title.">");
        }
        $this->comment("New volume title: <".$newTitle.">");

        return $newTitle.'.mp3';
    }

}