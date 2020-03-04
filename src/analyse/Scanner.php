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

use audioMan\registry\Registry;
use audioMan\utils\ImgCheck;
use audioMan\utils\Messenger;
use audioMan\utils\SkipCollector;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Scanner
{
    use Messenger;

    private $iterator;

    public function __construct(string $actualPath)
    {
        $dirIterator = new \RecursiveDirectoryIterator($actualPath);
        $filter = (new Filter($dirIterator))->filter();
        $this->iterator = new \RecursiveIteratorIterator($filter);
    }

    final public function scan(): array
    {
        //vars
        $allFiles = [];
        $noFiles = $noSkippedFiles = $totalSize = 0;

        //files
        foreach ($this->iterator as $file) {
            assert($file instanceof \SplFileInfo);
            $noFiles++;

            //img type check
            $isImg = in_array(strtolower($file->getExtension()), Registry::IMAGE_TYPES);

            //skip empty files
            if ($file->getSize() === 0) {
                $msg = "File <".$file->getPathname()."> has no content. File skipped!";
                $this->warning($msg);
                $noSkippedFiles++;
                SkipCollector::add($file->getPathname(), SkipCollector::TYPE_EMPTY_FILE);

                continue;
            }
            //skip no mime typed image
            if ($isImg && !ImgCheck::isImage($file->getPathname())) {
                $msg = "Image file <".$file->getPathname()."> is not an image!!! File skipped!";
                $this->warning($msg);
                $noSkippedFiles++;
                SkipCollector::add($file->getPathname(), SkipCollector::TYPE_NOT_IMAGE);

                continue;
            }

            $allFiles[] = $file->getPathname();
            $totalSize += round($file->getSize() / (1000 * 1000), 1); //MB
        }

        //messages
        $this->createInfoMessages($noFiles, $noSkippedFiles, $totalSize);

        return $allFiles;
    }

    private function createInfoMessages(int $noFiles, int $noSkippedFiles, float $totalSize): void
    {
        //messages after filtered iteration
        $msgFound = ("Found <".$noFiles."> audio book files.");
        $noFiles>0? $this->success($msgFound): $this->warning($msgFound);

        $msgSkipped = ("Skipped <".$noSkippedFiles."> files.");
        $noSkippedFiles>0? $this->warning($msgSkipped): $this->success($msgSkipped);

        $sizeMsg = $this->createFileSizeMsg($totalSize);
        $this->info($sizeMsg);
    }

    private function createFileSizeMsg(float $totalSize): string
    {
        //file size
        $precision = "MB";
        if ($totalSize > 1000) {
            $totalSize = round($totalSize/1000, 1); //GB
            $precision = "GB";
        }
        if ($totalSize > 1000) {
            $totalSize = round($totalSize/1000, 1); //TB
            $precision = "TB";
        }

        return "Total size: <".$totalSize."> ".$precision.".";
    }
}