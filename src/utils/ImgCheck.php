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

namespace audioMan\utils;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ImgCheck
{
    public const MAX_FILE_SIZE = 1200; //800 kB

    /**
     * Checking if file is an image type, in detail jpg, jpeg or png
     */
    public static function isImage(string $filename): bool
    {
        if (false === $mimeType = mime_content_type($filename)) {
            return false;
        }

        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/png' : return true;
        }

        return false;
    }

    /**
     * Check if image is a square
     */
    public static function hasSquareDimension(string $filename): bool
    {
        if (false === $size = getimagesize($filename)) {
            return false;
        }
        $ratio = round($size[0]/$size[1], 1);

        return  ($ratio > 0.9 && $ratio < 1.1);
    }

    /**
     * Check if image size is suited for album cover in tags
     */
    public static function isSmallSized(string $filename): bool
    {
        $kBytes = round(filesize($filename)/1000, 1);

        return $kBytes < self::MAX_FILE_SIZE;
    }
}