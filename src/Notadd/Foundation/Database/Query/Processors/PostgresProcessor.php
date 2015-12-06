<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:13
 */
namespace Notadd\Foundation\Database\Query\Processors;
use Notadd\Foundation\Database\Query\Builder;
class PostgresProcessor extends Processor {
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param string $sql
     * @param array $values
     * @param string $sequence
     * @return int
     */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null) {
        $results = $query->getConnection()->selectFromWriteConnection($sql, $values);
        $sequence = $sequence ?: 'id';
        $result = (array)$results[0];
        $id = $result[$sequence];
        return is_numeric($id) ? (int)$id : $id;
    }
    /**
     * @param array $results
     * @return array
     */
    public function processColumnListing($results) {
        $mapping = function ($r) {
            $r = (object)$r;
            return $r->column_name;
        };
        return array_map($mapping, $results);
    }
}