<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-06 19:07
 */
namespace Notadd\Foundation\Database\Query\Grammars;
use Notadd\Foundation\Database\Query\Builder;
class PostgresGrammar extends Grammar {
    /**
     * @var array
     */
    protected $operators = [
        '=',
        '<',
        '>',
        '<=',
        '>=',
        '<>',
        '!=',
        'like',
        'not like',
        'between',
        'ilike',
        '&',
        '|',
        '#',
        '<<',
        '>>',
    ];
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param bool|string $value
     * @return string
     */
    protected function compileLock(Builder $query, $value) {
        if(is_string($value)) {
            return $value;
        }
        return $value ? 'for update' : 'for share';
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $values
     * @return string
     */
    public function compileUpdate(Builder $query, $values) {
        $table = $this->wrapTable($query->from);
        $columns = $this->compileUpdateColumns($values);
        $from = $this->compileUpdateFrom($query);
        $where = $this->compileUpdateWheres($query);
        return trim("update {$table} set {$columns}{$from} $where");
    }
    /**
     * @param array $values
     * @return string
     */
    protected function compileUpdateColumns($values) {
        $columns = [];
        foreach($values as $key => $value) {
            $columns[] = $this->wrap($key) . ' = ' . $this->parameter($value);
        }
        return implode(', ', $columns);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string|null
     */
    protected function compileUpdateFrom(Builder $query) {
        if(!isset($query->joins)) {
            return '';
        }
        $froms = [];
        foreach($query->joins as $join) {
            $froms[] = $this->wrapTable($join->table);
        }
        if(count($froms) > 0) {
            return ' from ' . implode(', ', $froms);
        }
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    protected function compileUpdateWheres(Builder $query) {
        $baseWhere = $this->compileWheres($query);
        if(!isset($query->joins)) {
            return $baseWhere;
        }
        $joinWhere = $this->compileUpdateJoinWheres($query);
        if(trim($baseWhere) == '') {
            return 'where ' . $this->removeLeadingBoolean($joinWhere);
        }
        return $baseWhere . ' ' . $joinWhere;
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return string
     */
    protected function compileUpdateJoinWheres(Builder $query) {
        $joinWheres = [];
        foreach($query->joins as $join) {
            foreach($join->clauses as $clause) {
                $joinWheres[] = $this->compileJoinConstraint($clause);
            }
        }
        return implode(' ', $joinWheres);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @param array $values
     * @param string $sequence
     * @return string
     */
    public function compileInsertGetId(Builder $query, $values, $sequence) {
        if(is_null($sequence)) {
            $sequence = 'id';
        }
        return $this->compileInsert($query, $values) . ' returning ' . $this->wrap($sequence);
    }
    /**
     * @param \Notadd\Foundation\Database\Query\Builder $query
     * @return array
     */
    public function compileTruncate(Builder $query) {
        return ['truncate ' . $this->wrapTable($query->from) . ' restart identity' => []];
    }
}