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

namespace audioMan\album\helper;

use audioMan\episode\EpisodeCreator;
use audioMan\episode\helper\NormalizeHelper;
use audioMan\episode\helper\TitleHelper;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class HelperFactory
{
    const COVER_HELPER  = 10;
    const IMAGE_HELPER  = 20;
    const MERGE_HELPER  = 30;
    const VOLUME_HELPER = 40;

    private static $instance;
    private $helper = [];

    public static function get (int $type): AlbumHelperInterface
    {

        if ($helper = self::getInstance()->getHelper($type)) {
            return $helper;
        };

        switch ($type) {
            case self::COVER_HELPER:
                $helper = new CoverHelper();
                break;
            case self::IMAGE_HELPER: $helper = new AlbumImageHelper();
                break;
            case self::MERGE_HELPER: $helper = new MergeHelper(new EpisodeCreator());
                break;
            case self::VOLUME_HELPER: $helper = new VolumeHelper(new NormalizeHelper(), new TitleHelper());
                break;
            default:
                throw new \LogicException('Requested type <'.$type.'> not found.');
        }
        // set helper in registry
        self::getInstance()->setHelper($type, $helper);

        return $helper;
    }

    private static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function getHelper(int $type): ?AlbumHelperInterface
    {
        if (array_key_exists($type, $this->helper) && isset($this->helper[$type])) {
            return $this->helper[$type];
        }

        return null;
    }

    private function setHelper(int $type, AlbumHelperInterface $helper): void
    {
        $this->helper[$type] = $helper;
    }
}