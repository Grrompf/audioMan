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

namespace audioMan\mp3;


use audioMan\AbstractBase;
use audioMan\Registry;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class Mp3Normalizer  extends AbstractBase
{
    /**
     * Rewriting title and album of the mp3 file
     */
    final public function handle(): void
    {
        //change to root dir
        chdir(Registry::get(Registry::KEY_LIB_DIR));
        $this->comment("Normalizing mp3 files!");

        //rescan
        if (false === $files = $this->getScanner()->scanFiles('mp3', true)) {
            $this->warning("No files normalized. No files found in <".basename(getcwd()).">!");
            $msg = PHP_EOL."Exit".PHP_EOL;
            die($msg);
        }

        $noChanges = 0;
        foreach ($files as $file) {

            $suffix = ".mp3";
            $fileName = basename($file, $suffix);
            $rename = $this->normalizeUtf8($fileName).$suffix;
            if ($rename !== $file) {
                $noChanges++;
                $move = escapeshellarg($rename);
                $orig = escapeshellarg($file);
                shell_exec("mv $orig $move");
            }
        }
        if ($noChanges > 0) {
            $msg = "<".count($files). " files> normalized in <".basename(Registry::get(Registry::KEY_LIB_DIR)).">";
            $this->comment($msg);
        } else {
            $this->info("No files normalized.");
        }
    }

    private function normalizeUtf8(string $s): string
    {
        // remove spaces
        $s    = str_replace( ' '    , "_",    $s );    // spaces

        // maps German (umlauts) and other European characters onto two characters before just removing diacritics
        $s    = preg_replace( '@\x{00c4}@u'    , "AE",    $s );    // umlaut Ä => AE
        $s    = preg_replace( '@\x{00d6}@u'    , "OE",    $s );    // umlaut Ö => OE
        $s    = preg_replace( '@\x{00dc}@u'    , "UE",    $s );    // umlaut Ü => UE
        $s    = preg_replace( '@\x{00e4}@u'    , "ae",    $s );    // umlaut ä => ae
        $s    = preg_replace( '@\x{00f6}@u'    , "oe",    $s );    // umlaut ö => oe
        $s    = preg_replace( '@\x{00fc}@u'    , "ue",    $s );    // umlaut ü => ue
        $s    = preg_replace( '@\x{00f1}@u'    , "ny",    $s );    // ñ => ny
        $s    = preg_replace( '@\x{00ff}@u'    , "yu",    $s );    // ÿ => yu
        $s    = preg_replace( '@\x{00df}@u'    , "ss",    $s );    // maps German ß onto ss
        $s    = preg_replace( '@\x{00c6}@u'    , "AE",    $s );    // Æ => AE
        $s    = preg_replace( '@\x{00e6}@u'    , "ae",    $s );    // æ => ae
        $s    = preg_replace( '@\x{0132}@u'    , "IJ",    $s );    // ? => IJ
        $s    = preg_replace( '@\x{0133}@u'    , "ij",    $s );    // ? => ij
        $s    = preg_replace( '@\x{0152}@u'    , "OE",    $s );    // Œ => OE
        $s    = preg_replace( '@\x{0153}@u'    , "oe",    $s );    // œ => oe
        $s    = preg_replace( '@\x{00d0}@u'    , "D",    $s );    // Ð => D
        $s    = preg_replace( '@\x{0110}@u'    , "D",    $s );    // Ð => D
        $s    = preg_replace( '@\x{00f0}@u'    , "d",    $s );    // ð => d
        $s    = preg_replace( '@\x{0111}@u'    , "d",    $s );    // d => d
        $s    = preg_replace( '@\x{0126}@u'    , "H",    $s );    // H => H
        $s    = preg_replace( '@\x{0127}@u'    , "h",    $s );    // h => h
        $s    = preg_replace( '@\x{0131}@u'    , "i",    $s );    // i => i
        $s    = preg_replace( '@\x{0138}@u'    , "k",    $s );    // ? => k
        $s    = preg_replace( '@\x{013f}@u'    , "L",    $s );    // ? => L
        $s    = preg_replace( '@\x{0141}@u'    , "L",    $s );    // L => L
        $s    = preg_replace( '@\x{0140}@u'    , "l",    $s );    // ? => l
        $s    = preg_replace( '@\x{0142}@u'    , "l",    $s );    // l => l
        $s    = preg_replace( '@\x{014a}@u'    , "N",    $s );    // ? => N
        $s    = preg_replace( '@\x{0149}@u'    , "n",    $s );    // ? => n
        $s    = preg_replace( '@\x{014b}@u'    , "n",    $s );    // ? => n
        $s    = preg_replace( '@\x{00d8}@u'    , "O",    $s );    // Ø => O
        $s    = preg_replace( '@\x{00f8}@u'    , "o",    $s );    // ø => o
        $s    = preg_replace( '@\x{017f}@u'    , "s",    $s );    // ? => s
        $s    = preg_replace( '@\x{00de}@u'    , "T",    $s );    // Þ => T
        $s    = preg_replace( '@\x{0166}@u'    , "T",    $s );    // T => T
        $s    = preg_replace( '@\x{00fe}@u'    , "t",    $s );    // þ => t
        $s    = preg_replace( '@\x{0167}@u'    , "t",    $s );    // t => t

        return $s;
    }
}