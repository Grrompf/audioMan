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

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class NormalizeHelper implements EpisodeHelperInterface
{
    /**
     * Normalize words by replacing spaces to underlines. Replaces Umlauts.
     */
    final public function process(string $title): string
    {
        // remove spaces
        $title  = str_replace( ' ', "_", $title ); // spaces

        // maps German (umlauts) and other European characters onto two characters before just removing diacritics
        $title    = preg_replace( '@\x{00c4}@u'    , "AE",    $title );    // umlaut Ä => AE
        $title    = preg_replace( '@\x{00d6}@u'    , "OE",    $title );    // umlaut Ö => OE
        $title    = preg_replace( '@\x{00dc}@u'    , "UE",    $title );    // umlaut Ü => UE
        $title    = preg_replace( '@\x{00e4}@u'    , "ae",    $title );    // umlaut ä => ae
        $title    = preg_replace( '@\x{00f6}@u'    , "oe",    $title );    // umlaut ö => oe
        $title    = preg_replace( '@\x{00fc}@u'    , "ue",    $title );    // umlaut ü => ue
        $title    = preg_replace( '@\x{00f1}@u'    , "ny",    $title );    // ñ => ny
        $title    = preg_replace( '@\x{00ff}@u'    , "yu",    $title );    // ÿ => yu
        $title    = preg_replace( '@\x{00df}@u'    , "ss",    $title );    // maps German ß onto ss
        $title    = preg_replace( '@\x{00c6}@u'    , "AE",    $title );    // Æ => AE
        $title    = preg_replace( '@\x{00e6}@u'    , "ae",    $title );    // æ => ae
        $title    = preg_replace( '@\x{0132}@u'    , "IJ",    $title );    // ? => IJ
        $title    = preg_replace( '@\x{0133}@u'    , "ij",    $title );    // ? => ij
        $title    = preg_replace( '@\x{0152}@u'    , "OE",    $title );    // Œ => OE
        $title    = preg_replace( '@\x{0153}@u'    , "oe",    $title );    // œ => oe
        $title    = preg_replace( '@\x{00d0}@u'    , "D",    $title );    // Ð => D
        $title    = preg_replace( '@\x{0110}@u'    , "D",    $title );    // Ð => D
        $title    = preg_replace( '@\x{00f0}@u'    , "d",    $title );    // ð => d
        $title    = preg_replace( '@\x{0111}@u'    , "d",    $title );    // d => d
        $title    = preg_replace( '@\x{0126}@u'    , "H",    $title );    // H => H
        $title    = preg_replace( '@\x{0127}@u'    , "h",    $title );    // h => h
        $title    = preg_replace( '@\x{0131}@u'    , "i",    $title );    // i => i
        $title    = preg_replace( '@\x{0138}@u'    , "k",    $title );    // ? => k
        $title    = preg_replace( '@\x{013f}@u'    , "L",    $title );    // ? => L
        $title    = preg_replace( '@\x{0141}@u'    , "L",    $title );    // L => L
        $title    = preg_replace( '@\x{0140}@u'    , "l",    $title );    // ? => l
        $title    = preg_replace( '@\x{0142}@u'    , "l",    $title );    // l => l
        $title    = preg_replace( '@\x{014a}@u'    , "N",    $title );    // ? => N
        $title    = preg_replace( '@\x{0149}@u'    , "n",    $title );    // ? => n
        $title    = preg_replace( '@\x{014b}@u'    , "n",    $title );    // ? => n
        $title    = preg_replace( '@\x{00d8}@u'    , "O",    $title );    // Ø => O
        $title    = preg_replace( '@\x{00f8}@u'    , "o",    $title );    // ø => o
        $title    = preg_replace( '@\x{017f}@u'    , "s",    $title );    // ? => s
        $title    = preg_replace( '@\x{00de}@u'    , "T",    $title );    // Þ => T
        $title    = preg_replace( '@\x{0166}@u'    , "T",    $title );    // T => T
        $title    = preg_replace( '@\x{00fe}@u'    , "t",    $title );    // þ => t
        $title    = preg_replace( '@\x{0167}@u'    , "t",    $title );    // t => t

        return $title;
    }
}