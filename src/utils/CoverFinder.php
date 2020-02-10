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
class CoverFinder extends BaseScanner
{
    /**
     * Find cover files.
     */
    final public function find(): ?string
    {
        $imgFiles=[];
        $types = ['jpg', 'jpeg', 'png'];
        foreach ($types as $type) {
            if (false !== $files = $this->search($type)) {
                $this->filter($files, $imgFiles);
            }
        }
        //total number of images found
        $noImg = count($imgFiles);

        //no image
        if (0 === $noImg) {
            return null;
        }
        //single image
        if (1 === $noImg) {
            $cover = $imgFiles[0];
            $this->comment("Album cover <".$cover.">found in <".basename(getcwd()).">");
            return $cover;
        }
        //multiple images but best match found
        if ($noImg > 1 && ($cover = $this->findBestMatch($imgFiles))) {
            $this->comment("Album cover <".$cover.">found in <".basename(getcwd()).">");
            return $cover;
        }
        //multiple images left
        $msg = "<".$noImg." img files> found in <".basename(getcwd()).">!".PHP_EOL."You have to select manually.";
        $this->warning($msg);

        return null;
    }

    /**
     * Filter images by mime type, form and size
     */
    private function filter(array $files, array &$found): void
    {
        foreach ($files as $filename) {
            if (false === ImgCheck::isImage($filename)) {
                $this->caution("This file <".$filename.">is not a regular image.");
                continue;
            }
            if (false === ImgCheck::hasSquareDimension($filename)) {
                $this->warning("This image <".$filename.">is not quadratic.");
                continue;
            }
            if (false === ImgCheck::isSmallSized($filename)) {
                $this->warning("This image <".$filename.">is too large.");
                continue;
            }
            $found[] = $filename;
        }
    }

    /**
     * Find best match of multiple files by file name.
     */
    private function findBestMatch(array $files): ?string
    {
        foreach ($files as $filename) {
            //best match
            if (stripos($filename, 'cover') !== false) {
                return $filename;
            }
            //second best
            if (stripos($filename, 'folder') !== false) {
                return $filename;
            }
            //third best
            if (stripos($filename, 'front') !== false) {
                return $filename;
            }
        }

        return null;
    }
}