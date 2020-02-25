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

namespace audioMan\episode;

use audioMan\Registry;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TitleMaker
{
    /**
     * Replacing dots for a dash resulting in file format "01 - File.mp3".
     */
    final public function makeTitle(string $originalTitle): string
    {
        //clean mistyping
        $originalTitle = str_replace('--', '-', $originalTitle);
        $originalTitle = str_replace('__', '_', $originalTitle);
        $originalTitle = str_replace('  ', ' ', $originalTitle);

        //skip on normalized files eg 01_-_file_for_you.mp3
        if (false === strpos($originalTitle, ' ') && false !== strpos($originalTitle, '_')) {
            return $originalTitle;
        };

        //remove whitespaces
        $originalTitle = trim($originalTitle);

        //default: '1. file', '01 - file', '(07).file', '07 file', '07-file', '(07).file',
        $pattern = '#([A-z].*)$#';;
        if (1 !== preg_match($pattern, $originalTitle, $matches)) {
            //no chars in title!
            return $originalTitle;
        }
        $title = $matches[1];

        //get number
        $extract = str_replace($title, '', $originalTitle);
        $numberPattern = '#(\d+).*$#';
        if (1 !== preg_match($numberPattern, $extract, $matches)) {
            //no number!
            return $originalTitle;
        }
        $number = $matches[1];

        //bad format: fronting eg AFD 07 - get me.mp3
        if (strlen($title) === strlen($originalTitle)) {
            $frontingPattern = '#^[A-z]+[-\.\s]*[0-9]+[-\.\s]*(.*)$#';
            if (1 === preg_match($frontingPattern, $originalTitle, $matches)) {
                $title  = $matches[1];
            } else {
                return $originalTitle;
            }
        }

        //always leading number on single digits
        if (strlen($number) == 1) {
            $number = '0'.$number;
        }

        return $number.Registry::get(Registry::KEY_SEPARATOR).$title;
    }
}