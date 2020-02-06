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
class Messenger
{
    final public function debug(string $message): void
    {
        // debug
        if (Registry::get(Registry::KEY_VERBOSITY) > 2) {
            print $this->writeln($message, null, 'fg=white;bg=blue');
        }
    }

    final public function info(string $message): void
    {
        // very verbose
        if (Registry::get(Registry::KEY_VERBOSITY) >= 2) {
            print $this->writeln($message, null, 'fg=default;bg=default');
        }
    }

    final public function comment(string $message): void
    {
        // very verbose
        if (Registry::get(Registry::KEY_VERBOSITY) > 1) {
            print $this->writeln($message, null, 'fg=blue;bg=default');
        }
    }

    final public function caution(string $message): void
    {
        // verbose
        if (Registry::get(Registry::KEY_VERBOSITY) >= 1) {
            print $this->writeln($message, 'CAUTION', 'fg=red;bg=default');
        }
    }

    final public function warning(string $message): void
    {
        // verbose
        if (Registry::get(Registry::KEY_VERBOSITY) >= 1) {
            print $this->writeln($message, 'WARNING', 'fg=black;bg=yellow');
        }
    }

    final public function success(string $message): void
    {
        // verbose normal
        if (Registry::get(Registry::KEY_VERBOSITY) >= 0) {
            print $this->writeln($message, 'OK', 'fg=black;bg=green');
        }
    }

    final public function error(string $message): void
    {
        // verbose normal
        if (Registry::get(Registry::KEY_VERBOSITY) >= 0) {
            print $this->writeln($message, 'ERROR', 'fg=white;bg=red');
        }
    }

    final public function break(): void
    {
        // verbose normal
        if (Registry::get(Registry::KEY_VERBOSITY) >= 0) {
            print $this->writeln(PHP_EOL.'--------------'.PHP_EOL, null, 'fg=default;bg=default');
        }
    }

    private function writeln(string $message, ?string $type, string $style): string
    {
        if (null !== $type) {
            $type = sprintf("[%s] ", $type);
            $indentLength = \strlen($type);
            $lineIndentation = str_repeat(' ', $indentLength);
        } else {
            $type='';
            $lineIndentation='';
        }
        $color = $this->getColor($style);

        $text = explode("\n", $message);
        $textLines = PHP_EOL.$type.array_shift($text).PHP_EOL;

        $lastLine ="";
        if (!empty($text)) {
            $lastLine = $lineIndentation.array_pop($text);
        }

        foreach ($text as $line) {
            $textLines .= $lineIndentation.$line.PHP_EOL;
        }
        $textLines .= $lastLine;

        return "\033[".$color."m".$textLines."\033[0m";
    }

    private function getColor(string $style): string
    {
        $colors = explode(';', $style);
        $fgColor = $this->findFgColor($colors[0]);
        $bgColor = $this->findBgColor($colors[1]);

        return sprintf("%s;%s", $fgColor, $bgColor);
    }

    private function findFgColor(string $style): string
    {
        switch ($style) {
            case 'fg=black':  $fgColor ='30';
                break;
            case 'fg=blue':   $fgColor ='34';
                break;
            case 'fg=green':  $fgColor ='32';
                break;
            case 'fg=red':    $fgColor ='31';
                break;
            case 'fg=white':  $fgColor ='37';
                break;
            case 'fg=yellow': $fgColor ='33';
                break;
            default:
                $fgColor = '30';
        }

        return $fgColor;
    }

    private function findBgColor(string $style): string
    {
        switch ($style) {
            case 'bg=blue':   $bgColor ='44';
                break;
            case 'bg=green':  $bgColor ='42';
                break;
            case 'bg=red':    $bgColor ='41';
                break;
            case 'bg=yellow': $bgColor ='43';
                break;
            default:
                $bgColor = '48';
        }

        return $bgColor;
    }
}