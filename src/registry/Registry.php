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

namespace audioMan\registry;

use audioMan\utils\Tools;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Registry
{
    public const KEY_FORMAT         = 'format';
    public const KEY_LIB_DIR        = 'libDir'; //album dir
    public const KEY_AUDIO          = 'audio'; //audio format
    public const KEY_LEVEL          = 'level'; //nesting level
    public const KEY_NORMALIZE      = 'normalize';
    public const KEY_OUTPUT         = 'output';
    public const KEY_PATH_SEPARATOR = 'path_separator'; //depending on OS
    public const KEY_ROOT_DIR       = 'rootDir'; //start dir
    public const KEY_SEPARATOR      = 'separator'; //format in title
    public const KEY_VERBOSITY      = 'verbosity';
    public const KEY_NO_INTERACTION = 'no-interaction'; //forcing yes as answer

    protected static $instance = null;
    protected $values = [];

    /**
     * @param string $key
     * @return mixed|null
     */
    public static function get(string $key)
    {
        if (isset(self::getInstance()->values[$key])) {
            return self::getInstance()->values[$key];
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public static function set(string $key, $value): void
    {
        self::getInstance()->values[$key] = $value;
    }

    protected static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();

            //dir separator of the OS
            self::$instance::set(self::KEY_PATH_SEPARATOR, '/');
            if (stripos(PHP_OS, 'WIN') === 0) {
                self::$instance::set(self::KEY_PATH_SEPARATOR, '\\');
            }

            //format default: title separator
            self::$instance::set(self::KEY_SEPARATOR, ' - ');

            //output default: HOME/audioMan
            $output = Tools::createDir("~/audioMan");
            self::$instance::set(self::KEY_OUTPUT, $output);

            //no-interaction default: false
            self::$instance::set(self::KEY_NO_INTERACTION, false);

            //audio default: mp3
            self::$instance::set(self::KEY_AUDIO, 'mp3');

            //init level
            self::$instance::set(self::KEY_LEVEL, null);

            //normalise default: true
            self::$instance::set(self::KEY_NORMALIZE, true);
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