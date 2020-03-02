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

namespace audioMan\episode\helper;

use audioMan\registry\Registry;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TitleHelper implements EpisodeHelperInterface
{
    /**
     * Replacing dots for a dash resulting in file format "01 - File.mp3".
     */
    final public function process(string $title): string
    {
        //clean mistyping
        $title = str_replace('--', '-', $title);
        $title = str_replace('__', '_', $title);
        $title = str_replace('  ', ' ', $title);

        //skip on normalized files eg 01_-_file_for_you.mp3
        if (false === strpos($title, ' ') && false !== strpos($title, '_')) {
            return $title;
        };

        //remove whitespaces
        $title = trim($title);

        //default: '1. file', '01 - file', '(07).file', '07 file', '07-file', '(07).file',
        $pattern = '#(\p{L}+.*)$#u'; //pattern for umlaut
        if (1 !== preg_match($pattern, $title, $matches)) {
            //no chars in title!
            return $title;
        }
        $newTitle = $matches[1];

        //get number
        $extract = str_replace($newTitle, '', $title);
        $numberPattern = '#(\d+).*$#';
        if (1 !== preg_match($numberPattern, $extract, $matches)) {
            //no number!
            return $title;
        }
        $number = $matches[1];

        //bad format: fronting eg AFD 07 - get me.mp3
        if (strlen($newTitle) === strlen($title)) {
            $frontingPattern = '#^[A-z]+[-\.\s]*[0-9]+[-\.\s]*(.*)$#';
            if (1 === preg_match($frontingPattern, $title, $matches)) {
                $newTitle  = $matches[1];
            } else {
                return $title;
            }
        }

        //always leading number on single digits
        if (strlen($number) == 1) {
            $number = '0'.$number;
        }

        return $number.Registry::get(Registry::KEY_SEPARATOR).$newTitle;
    }
}