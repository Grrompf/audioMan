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

namespace audioMan\album;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class AlbumTree
{
    public $tree = [];
    protected static $instance = null;

    public static function add(array $albumModel)
    {
        self::getInstance()->tree[] = $albumModel;
    }

    public static function createTree(array &$list, $parent): array
    {

        $tree = array();
        foreach ($parent as $k=>$l){
            if(isset($list[$l['level']])){
                $l['children'] = self::getInstance()::createTree($list, $list[$l['level']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }

    public static function getAll(): array
    {
        $new = array();
        foreach (self::getInstance()->tree as $model){
            $new[$model['parentLevel']][] = $model;
        }

        $tree = self::createTree($new, [self::getInstance()->tree[0]]);
        var_dump($new);die;
        return $new;
        return self::getInstance()::createTree(self::getInstance()->tree, self::getInstance()->tree[0]);
    }

    protected static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
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