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
class SkipCollector
{
    use Messenger;

    public const TYPE_EPISODE    = 10;
    public const TYPE_EMPTY_FILE = 20;
    public const TYPE_NOT_IMAGE  = 30;

    protected static $instance = null;
    protected $skippedFiles = [];

    /**
     * Add skipped files
     */
    public static function add(string $file, int $type=self::TYPE_EPISODE): void
    {
        //make new entry
        if (!in_array($file, self::getInstance()->skippedFiles)) {
            self::getInstance()->skippedFiles[$type][] = $file;
        }

        self::getInstance()->debug("Skipped file <".$file."> added.");
    }

    /**
     * get skipped files by type
     */
    public static function get(int $type=self::TYPE_EPISODE): array
    {
        if (!key_exists($type, self::getInstance()->skippedFiles)) {
            self::getInstance()->skippedFiles[$type] = [];
        }

        return self::getInstance()->skippedFiles[$type];
    }

    /**
     * get all skipped files
     */
    public static function getAll(): array
    {
        return self::getInstance()->skippedFiles;
    }

    protected static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new SkipCollector();
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