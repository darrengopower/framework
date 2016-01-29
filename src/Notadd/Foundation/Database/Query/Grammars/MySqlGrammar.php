<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:06
 */
namespace Notadd\Foundation\Database\Query\Grammars;
use Notadd\Foundation\Database\Query\Builder;
/**
 * Class MySqlGrammar
 * @package Notadd\Foundation\Database\Query\Grammars
 */
class MySqlGrammar extends Grammar {
    /**
     * @var array
     */
    protected $selectComponents = [
        'aggregate',
        'columns',
        'from',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
        'lock',
    ];
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    public function compileSelect(Builder $query) {
        $sql = parent::compileSelect($query);
        if($query->unions) {
            $sql = '(' . $sql . ') ' . $this->compileUnions($query);
        }
        return $sql;
    }
    /**
     * @param array $union
     * @return string
     */
    protected function compileUnion(array $union) {
        $joiner = $union['all'] ? ' union all ' : ' union ';
        return $joiner . '(' . $union['query']->toSql() . ')';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param bool|string $value
     * @return string
     */
    protected function compileLock(Builder $query, $value) {
        if(is_string($value)) {
            return $value;
        }
        return $value ? 'for update' : 'lock in share mode';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $values
     * @return string
     */
    public function compileUpdate(Builder $query, $values) {
        $sql = parent::compileUpdate($query, $values);
        if(isset($query->orders)) {
            $sql .= ' ' . $this->compileOrders($query, $query->orders);
        }
        if(isset($query->limit)) {
            $sql .= ' ' . $this->compileLimit($query, $query->limit);
        }
        return rtrim($sql);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    public function compileDelete(Builder $query) {
        $table = $this->wrapTable($query->from);
        $where = is_array($query->wheres) ? $this->compileWheres($query) : '';
        if(isset($query->joins)) {
            $joins = ' ' . $this->compileJoins($query, $query->joins);
            $sql = trim("delete $table from {$table}{$joins} $where");
        } else {
            $sql = trim("delete from $table $where");
        }
        if(isset($query->orders)) {
            $sql .= ' ' . $this->compileOrders($query, $query->orders);
        }
        if(isset($query->limit)) {
            $sql .= ' ' . $this->compileLimit($query, $query->limit);
        }
        return $sql;
    }
    /**
     * @param string $value
     * @return string
     */
    protected function wrapValue($value) {
        if($value === '*') {
            return $value;
        }
        return '`' . str_replace('`', '``', $value) . '`';
    }
}