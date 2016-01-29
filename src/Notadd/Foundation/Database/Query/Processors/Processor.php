<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 02:34
 */
namespace Notadd\Foundation\Database\Query\Processors;
use Notadd\Foundation\Database\Query\Builder;
/**
 * Class Processor
 * @package Notadd\Foundation\Database\Query\Processors
 */
class Processor {
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $results
     * @return array
     */
    public function processSelect(Builder $query, $results) {
        return $results;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param string $sql
     * @param array $values
     * @param string $sequence
     * @return int
     */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null) {
        $query->getConnection()->insert($sql, $values);
        $id = $query->getConnection()->getPdo()->lastInsertId($sequence);
        return is_numeric($id) ? (int)$id : $id;
    }
    /**
     * @param array $results
     * @return array
     */
    public function processColumnListing($results) {
        return $results;
    }
}