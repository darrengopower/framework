<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-05 00:08
 */
namespace Notadd\Foundation\Translation;
interface LoaderInterface {
    /**
     * @param  string $locale
     * @param  string $group
     * @param  string $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null);
    /**
     * @param  string $namespace
     * @param  string $hint
     * @return void
     */
    public function addNamespace($namespace, $hint);
}