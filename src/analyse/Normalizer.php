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

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Normalizer
{
    /**
     * Normalize words by replacing spaces to underlines. Replaces Umlauts.
     */
    final public function normalizeUtf8(string $word): string
    {
        // remove spaces
        $word  = str_replace( ' ', "_", $word ); // spaces

        // maps German (umlauts) and other European characters onto two characters before just removing diacritics
        $word    = preg_replace( '@\x{00c4}@u'    , "AE",    $word );    // umlaut Ä => AE
        $word    = preg_replace( '@\x{00d6}@u'    , "OE",    $word );    // umlaut Ö => OE
        $word    = preg_replace( '@\x{00dc}@u'    , "UE",    $word );    // umlaut Ü => UE
        $word    = preg_replace( '@\x{00e4}@u'    , "ae",    $word );    // umlaut ä => ae
        $word    = preg_replace( '@\x{00f6}@u'    , "oe",    $word );    // umlaut ö => oe
        $word    = preg_replace( '@\x{00fc}@u'    , "ue",    $word );    // umlaut ü => ue
        $word    = preg_replace( '@\x{00f1}@u'    , "ny",    $word );    // ñ => ny
        $word    = preg_replace( '@\x{00ff}@u'    , "yu",    $word );    // ÿ => yu
        $word    = preg_replace( '@\x{00df}@u'    , "ss",    $word );    // maps German ß onto ss
        $word    = preg_replace( '@\x{00c6}@u'    , "AE",    $word );    // Æ => AE
        $word    = preg_replace( '@\x{00e6}@u'    , "ae",    $word );    // æ => ae
        $word    = preg_replace( '@\x{0132}@u'    , "IJ",    $word );    // ? => IJ
        $word    = preg_replace( '@\x{0133}@u'    , "ij",    $word );    // ? => ij
        $word    = preg_replace( '@\x{0152}@u'    , "OE",    $word );    // Œ => OE
        $word    = preg_replace( '@\x{0153}@u'    , "oe",    $word );    // œ => oe
        $word    = preg_replace( '@\x{00d0}@u'    , "D",    $word );    // Ð => D
        $word    = preg_replace( '@\x{0110}@u'    , "D",    $word );    // Ð => D
        $word    = preg_replace( '@\x{00f0}@u'    , "d",    $word );    // ð => d
        $word    = preg_replace( '@\x{0111}@u'    , "d",    $word );    // d => d
        $word    = preg_replace( '@\x{0126}@u'    , "H",    $word );    // H => H
        $word    = preg_replace( '@\x{0127}@u'    , "h",    $word );    // h => h
        $word    = preg_replace( '@\x{0131}@u'    , "i",    $word );    // i => i
        $word    = preg_replace( '@\x{0138}@u'    , "k",    $word );    // ? => k
        $word    = preg_replace( '@\x{013f}@u'    , "L",    $word );    // ? => L
        $word    = preg_replace( '@\x{0141}@u'    , "L",    $word );    // L => L
        $word    = preg_replace( '@\x{0140}@u'    , "l",    $word );    // ? => l
        $word    = preg_replace( '@\x{0142}@u'    , "l",    $word );    // l => l
        $word    = preg_replace( '@\x{014a}@u'    , "N",    $word );    // ? => N
        $word    = preg_replace( '@\x{0149}@u'    , "n",    $word );    // ? => n
        $word    = preg_replace( '@\x{014b}@u'    , "n",    $word );    // ? => n
        $word    = preg_replace( '@\x{00d8}@u'    , "O",    $word );    // Ø => O
        $word    = preg_replace( '@\x{00f8}@u'    , "o",    $word );    // ø => o
        $word    = preg_replace( '@\x{017f}@u'    , "s",    $word );    // ? => s
        $word    = preg_replace( '@\x{00de}@u'    , "T",    $word );    // Þ => T
        $word    = preg_replace( '@\x{0166}@u'    , "T",    $word );    // T => T
        $word    = preg_replace( '@\x{00fe}@u'    , "t",    $word );    // þ => t
        $word    = preg_replace( '@\x{0167}@u'    , "t",    $word );    // t => t

        return $word;
    }
}