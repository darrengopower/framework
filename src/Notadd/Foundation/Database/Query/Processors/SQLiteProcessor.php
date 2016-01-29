<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:14
 */
namespace Notadd\Foundation\Database\Query\Processors;
/**
 * Class SQLiteProcessor
 * @package Notadd\Foundation\Database\Query\Processors
 */
class SQLiteProcessor extends Processor {
    /**
     * @param array $results
     * @return array
     */
    public function processColumnListing($results) {
        $mapping = function ($r) {
            $r = (object)$r;
            return $r->name;
        };
        return array_map($mapping, $results);
    }
}