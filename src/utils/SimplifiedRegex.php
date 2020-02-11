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
class SimplifiedRegex extends Messenger
{
    const REGEX_TITLE   = "TITLE";
    const REGEX_NUMBER  = "n";
    const TXT_SEPARATOR = "Separator";
    const TXT_SPACE     = "Space";
    const REGEX_DOT     = ".";
    const REGEX_DASH    = "-";
    const REGEX_SPACE   = " ";
    const PATTERN_DEFAULT = '#n(\s?[.-/\s]\s?)TITLE$#';
    const PATTERN_APPEND = '#TITLE(\s?[.-/\s]\s?)n$#';

    /**
     * Validates user input and composes the format pattern.
     * Exit on validation failure.
     */
    final public function compose(string $simplifiedRegex): string
    {
        $this->validate($simplifiedRegex);

        //position
        $isAppend = (1 === preg_match(self::PATTERN_APPEND, $simplifiedRegex));
        Registry::set(Registry::KEY_APPEND, $isAppend);

        //separators
        if (1 === preg_match(self::PATTERN_DEFAULT, $simplifiedRegex, $matches)) {
            $separator = $matches[1];
        }
        if (1 === preg_match(self::PATTERN_APPEND, $simplifiedRegex, $matches)) {
            $separator = $matches[1];
        }
        Registry::set(Registry::KEY_SEPARATOR, $separator);

        //regex
        $pattern = str_replace(self::REGEX_TITLE, '(.*)', $simplifiedRegex);
        $pattern = str_replace(self::REGEX_NUMBER, '\d+', $pattern);
        $pattern = str_replace(self::REGEX_SPACE, '\s', $pattern);
        $pattern = str_replace(self::REGEX_DOT, '\.', $pattern);
        $pattern = str_replace(self::REGEX_DASH, '\-', $pattern);

        return "#^".$pattern."$#";
    }

    private function validate(string $simplifiedRegex): void
    {
        //MANDATORY
        //title
        if (false === strpos($simplifiedRegex, self::REGEX_TITLE)) {
            $this->mandatoryErrorMsg(self::REGEX_TITLE, $simplifiedRegex);
        }

        //number
        if (false === strpos($simplifiedRegex, self::REGEX_NUMBER)) {
            $this->mandatoryErrorMsg(self::REGEX_NUMBER, $simplifiedRegex);
        }

        //separators
        if (false === strpos($simplifiedRegex, self::REGEX_DOT) &&
            false === strpos($simplifiedRegex, self::REGEX_DASH) &&
            false === strpos($simplifiedRegex, self::REGEX_SPACE)) {
            $this->mandatoryErrorMsg(self::TXT_SEPARATOR, $simplifiedRegex);
        }

        //AMOUNTS
        //title
        if (1 !== substr_count($simplifiedRegex, self::REGEX_TITLE)) {
            $this->amountErrorMsg(self::REGEX_TITLE, $simplifiedRegex);
        }

        //number
        if (1 !== substr_count($simplifiedRegex, self::REGEX_NUMBER)) {
            $this->amountErrorMsg(self::REGEX_NUMBER, $simplifiedRegex);
        }

        //dots
        if (substr_count($simplifiedRegex, self::REGEX_DOT) > 1) {
            $this->amountErrorMsg(self::REGEX_DOT, $simplifiedRegex);
        }

        //dash
        if (substr_count($simplifiedRegex, self::REGEX_DASH) > 1) {
            $this->amountErrorMsg(self::REGEX_DASH, $simplifiedRegex);
        }

        //space
        if (substr_count($simplifiedRegex, self::REGEX_SPACE) > 2) {
            $this->amountErrorMsg(self::TXT_SPACE, $simplifiedRegex);
        }

        //other signs not allowed
        $test = str_replace(self::REGEX_TITLE, '', $simplifiedRegex);
        $test = str_replace(self::REGEX_NUMBER, '', $test);
        $test = str_replace(self::REGEX_SPACE, '', $test);
        $test = str_replace(self::REGEX_DOT, '', $test);
        $test = str_replace(self::REGEX_DASH, '', $test);

        if (strlen($test) > 0) {
            $this->error("Found not allowed letters <".$test.">");
            die(PHP_EOL."Exit.".PHP_EOL);
        }

        //POSITION
        if (1 !== preg_match(self::PATTERN_DEFAULT, $simplifiedRegex) &&
            1 !== preg_match(self::PATTERN_APPEND, $simplifiedRegex))
        {
            $this->error("Position not allowed <".$simplifiedRegex.". Title and number should be separated.");
            die(PHP_EOL."Exit.".PHP_EOL);
        }
    }

    private function mandatoryErrorMsg(string $type, string $input): void
    {
        $this->error($type." is mandatory.".PHP_EOL."Your input <".$input.">");
        die(PHP_EOL."Exit.".PHP_EOL);
    }

    private function amountErrorMsg(string $type, string $input): void
    {
        $this->error($type." is allowed only once.".PHP_EOL."Your input <".$input.">");
        die(PHP_EOL."Exit.".PHP_EOL);
    }
 }