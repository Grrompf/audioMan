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
class GarbageCollector
{
    use Messenger;

    protected static $instance = null;
    protected $tempFiles = [];

    /**
     * Add temporary files which are removed after processing
     */
    public static function add(string $file): void
    {
        //add file
        if (!in_array($file, self::getInstance()->tempFiles)) {
            self::getInstance()->tempFiles[] = $file;
        };

        self::getInstance()->debug("Temporary file <".$file."> added.");
    }

    public static function clean(): void
    {
        self::getInstance()->debug("Removing temporary files");

        //get the array of temporary files
        if (!empty(self::getInstance()->tempFiles)) {
            self::getInstance()->debug("Found <".count(self::getInstance()->tempFiles)."> temporary files.");

            $noRemoved=0;
            //cleaning
            foreach (self::getInstance()->tempFiles as $key => $fileToRemove) {

                //just one try to remove files. If fails it will just always fail.
                $noRemoved++;
                unset(self::getInstance()->tempFiles[$key]);

                //skip
                if(!file_exists($fileToRemove)) {
                    continue;
                }

                $removeCmd = 'rm '.escapeshellarg($fileToRemove);
                exec($removeCmd, $details, $retVal);
                if (0 !== $retVal) {
                    self::getInstance()->error(
                        "Error while removing <".$fileToRemove."> . Details: ".implode($details)
                    );
                    continue;
                }
                unset(self::getInstance()->tempFiles[$key]);
            }

            self::getInstance()->debug("Temporary files removed");
        };
    }

    protected static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new GarbageCollector();
        }

        return self::$instance;
    }

    protected function __construct()
    {
    }

    private function __clone()
    {
    }
}