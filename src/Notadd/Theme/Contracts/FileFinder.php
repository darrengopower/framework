<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-18 15:15
 */
namespace Notadd\Theme\Contracts;
/**
 * Interface FileFinder
 * @package Notadd\Theme\Contracts
 */
interface FileFinder {
    /**
     * @param $path
     * @return mixed
     */
    public function exits($path);
    /**
     * @param $path
     * @return mixed
     */
    public function find($path);
}