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

use audioMan\Registry;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TmpCleaner extends Messenger
{
    protected static $instance = null;

    public static function addFile(string $file): void
    {
        //only if not root dir
        if (dirname($file) === Registry::get(Registry::KEY_LIB_DIR)) {
            return;
        }
        $tmpFiles = [];

        //get the array of temporary files
        if (Registry::get(Registry::KEY_TMP_FILES)) {
            $tmpFiles = Registry::get(Registry::KEY_TMP_FILES);
        };

        //making entry unique
        $key = md5($file);
        $tmpFiles[$key] = $file;
        Registry::set(Registry::KEY_TMP_FILES, $tmpFiles);

        self::getInstance()->debug("Temporary file <".$file."> added.");
    }

    public static function clean(): void
    {
        self::getInstance()->comment("Removing temporary files");

        //get the array of temporary files
        if ($tmpFiles = Registry::get(Registry::KEY_TMP_FILES)) {
            self::getInstance()->info("Found <".count($tmpFiles)."> temporary files.");

            //cleaning
            foreach ($tmpFiles as $fileToRemove) {

                if(file_exists($fileToRemove)) {
                    $removeCmd = 'rm '.escapeshellarg($fileToRemove);
                    exec($removeCmd, $output, $retVal);
                    if (0 !== $retVal) {
                        self::getInstance()->error(
                            "Error while removing <".$fileToRemove.">".PHP_EOL."Details: ".implode($output)
                        );
                        $msg = PHP_EOL."Exit".PHP_EOL;
                        die($msg);
                    } else {
                        self::getInstance()->info("Removed <".$fileToRemove.">");
                    }
                }
            }
        };
    }

    protected static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new TmpCleaner();
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